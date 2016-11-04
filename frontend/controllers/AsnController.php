<?php

namespace frontend\controllers;

use common\models\DropshipEmailDetails;
use common\models\DropshipOrderline;
use common\models\Orderdetails;
use common\models\StockItem;

use yii\rest\ActiveController;
use common\models\Account;
use common\models\CustomerProductMapping;
use common\models\DigitalProduct;
use yii\web\Response;
use Yii;
use common\models\gauth\GAUser;
use common\components\EmailKeys;
use common\components\ItemPurchaserHelper;


class AsnController extends ActiveController {
    use itemPurchaserHelper;

    public $modelClass = '\common\models\StockItem';
//    public $modelClass = 'api\modules\v1\models\StockItem';

    /**
     * INIT
     * ====
     */
    public function init() {
        parent::init();
        \Yii::$app->user->enableSession = false;

    }

    /**
     * BEFORE ACTION
     * =============
     *
     * @param \yii\base\Action $action
     *
     * @return bool
     */
    public function beforeAction($action) {
        $before = parent::beforeAction($action);

        unset($before['index']);

        $userIp     = Yii::$app->request->userIp;
        $firstThree = substr($userIp, 0, 3);

        if ($firstThree != '172' && $firstThree != '127' && $firstThree != '192') {
            return false;
        }

        return $before;
    }

    /**
     * ACTIONS
     * =======
     * Remove the index option to prevent a caller obtaining a list of all products
     *
     * @return array
     */
    public function actions() {
        $actionList = parent::actions();

        unset($actionList['index']);

        return $actionList;
    }

    /**
     * BEHAVIOURS
     * =========
     * This handles the basic authentication by calling the auth method
     *
     * @return array
     */
    public function behaviors() {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']     = [
            'class' => \yii\filters\auth\HttpBasicAuth::className(),
            'auth'  => [$this, 'auth'],
        ];
        $behaviors['contentNegotiator'] = [
            'class'   => \yii\filters\ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviors;
    }

    /**
     * AUTH
     * ====
     * This verifies the basic authentication parameters
     *
     * @param $username
     * @param $password
     *
     * @return null|static
     */
    public function auth($username, $password) {

        $model = new GAUser();

        if (empty($username) || empty($password)) {
            return null;
        }

        $user = $model->findOne(['username' => $username]);
        if (!$user) {
            return null;
        }

        if (!\Yii::$app->security->validatePassword($password, $user->password)) {
            return null;
        }

        return $user;
    }

    /**
     * SAVE DROP SHIP EMAIL
     * ====================
     * This is intended to be called from the EDI system to record the drop
     * ship email address for the passed purchase order and, if the order has
     * been processed, send the key via email to the purchaser.
     *
     * @param null $accountNo
     * @param null $po
     * @param null $emailAddress
     * @param null $brand
     * @param null $partcode The following three must either
     * @param null $quantity all be present or all be
     * @param null $price    absent - though price can be zero
     *
     * @return bool
     */
    public function actionSaveDropShipEmail($accountNo = null, $po = null, $email = null, $brand = null,
                                            $partcode = null, $quantity = null, $price = null) {

        $purchaseOrder = trim($po);
        $brand         = trim($brand);

        $result = $this->verifyDSEInputs($accountNo, $purchaseOrder, $email, $partcode, $quantity, $price);

        if (is_object($result)) {
            $account = $result;

            // ---------------------------------------------------------------
            // Check that the partcode, if provided, is recognised. This returns
            // an error message if not, and the digital product if it is
            // ---------------------------------------------------------------
            $digitalProduct = $this->findOracleProductCode($account, $partcode);
            if (is_string($digitalProduct)) {
                $result = $digitalProduct;                 // Sets the error message

            } else {

                // -----------------------------------------------------------
                // This returns either an error message or a dropship email object
                // -----------------------------------------------------------
                $connection  = DigitalProduct::getDb();
                $transaction = $connection->beginTransaction();

                $result = $this->recordDropShipEmail($account, $accountNo, $purchaseOrder, $email, $brand);
                if (is_object($result)) {
                    $dse    = $result;
                    $result = $this->saveTheOrderDetails($dse, $digitalProduct, $partcode, $quantity, $price);
                }

                if ($result === true) {
                    $transaction->commit();

                } else {
                    $transaction->rollback();
                }
            }
        }

        $this->sendResultToCaller($result);

        // -------------------------------------------------------------------
        // If all worked well we now send any previously recorded emails, and
        // then save and process any provided order lines.
        // ProcessOrderDetails may create stock items ready for sendEmail...
        // to process
        // -------------------------------------------------------------------
        if ($result === true) {
            $this->processTheOrderDetails($account, $dse, $digitalProduct, $partcode, $quantity, $price);
            $this->sendEmailForPreOrderedItems($account, $dse);
        }


        return false;
    }

    /**
     * SEND RESULT TO CALLER
     * =====================
     * Force sends the response to the caller, which leaves this process in a
     * state where it can continue processing.
     *
     * If PHP is running under fast-cgi (fpm-cgi), we need to close that request
     *
     * @param $responseCode
     * @param $result
     */
    private function sendResultToCaller($result) {
        if ($result === true) {
            $responseCode = 200;
            $result       = 'success';

        } else {
            $responseCode = 400;
        }

        Yii::$app->response->format     = 'json';
        Yii::$app->response->statusCode = $responseCode;
        Yii::$app->response->data       = $result;

        Yii::$app->response->headers->add('Connection', 'close');
        Yii::$app->response->send();

        if (php_sapi_name() == 'fpm-fcgi') {
            fastcgi_finish_request();
        }
    }

    /**
     * PROCESS THE ORDER DETAILS
     * =========================
     *
     * @param $account                  account record
     * @param $dse                      drop ship email record
     * @param $digitalProduct           digital product record
     * @param $partcode                 customer partcode
     * @param $quantity
     * @param $price
     */
    private function processTheOrderDetails($account, $dse, $digitalProduct, $partcode, $quantity, $price) {
        if ($digitalProduct) {
            $this->iphAccount = $account;


            $orderLines  = $dse->dropshipOrderlines;
            $itemDetails = [];

            foreach ($orderLines as $orderLine) {

                $lineItem             = $digitalProduct->toArray();
                $lineItem['cost']     = $orderLine->price;
                $lineItem['quantity'] = $orderLine->quantity;
                $lineItem['total']    = $orderLine->price * $orderLine->quantity;

                $itemDetails[] = $lineItem;
            }
            $this->completeThePurchase($dse->po, $itemDetails);


            echo 'do the work';
        }
    }

    /**
     * SAVE THE ORDER DETAILS
     * ======================
     *
     * @param $dse
     * @param $digitalProduct
     * @param $partcode
     * @param $quantity
     * @param $price
     *
     * @return bool|string
     */
    private function saveTheOrderDetails($dse, $digitalProduct, $partcode, $quantity, $price) {
        $result = true;
        if (is_object($digitalProduct)) {
            $dsOrder                    = new DropshipOrderline;
            $dsOrder->dropship_id       = $dse->id;
            $dsOrder->customer_partcode = $partcode;
            $dsOrder->oracle_partcode   = $digitalProduct->partcode;
            $dsOrder->quantity          = $quantity;
            $dsOrder->price             = $price;
            if ((!$result = $dsOrder->save())) {
                $result = 'Unable to save the order details';
            }
        }

        return $result;
    }

    /**
     * RECORD DROPSHIP EMAIL
     * =====================
     *
     * @param $account
     * @param $accountNo
     * @param $purchaseOrder
     * @param $emailAddress
     * @param $brand
     *
     * @return bool|DropshipEmailDetails|string
     */
    private function recordDropShipEmail($account, $accountNo, $purchaseOrder, $emailAddress, $brand) {

        if (($result = $this->checkIfDuplicate($account, $accountNo, $purchaseOrder, $emailAddress, $brand)) === false) {
            $this->deleteOldEmails($account, $accountNo, $purchaseOrder, $brand);
            $result = $this->addNewDropShipEmail($account, $accountNo, $purchaseOrder, $emailAddress, $brand);
        }

        return $result;
    }

    /**
     * ADD NEW DROPSHIP EMAIL
     * ======================
     * Saves the new details
     *
     * @return DropshipEmailDetails|string
     */
    private function addNewDropShipEmail($account, $accountNo, $purchaseOrder, $emailAddress, $brand) {
        $dse             = new DropshipEmailDetails();
        $dse->account_id = $account->id;
        $dse->account_no = $accountNo;
        $dse->po         = $purchaseOrder;
        $dse->email      = $emailAddress;

        if ($brand && strlen($brand)) {
            $dse->brand = $brand;
        }

        try {
            if ($dse->save()) {
                $result = $dse;

            } elseif (array_key_exists('email', $dse->errors)) {
                $result = 'Malformed parameter values';

            } else {
                $result = 'Malformed parameter values';
            }

        } catch (\yii\db\Exception $exc) {
            // -------------------------------------------------------
            // PDO duplicate record error. Could do with a constant
            // -------------------------------------------------------
            if ($exc->errorInfo[1] == 1062) {
                $result = 'Duplicate Request';

            } else {
                $result = $exc->getMessage();
            }
        }

        return $result;
    }

    /**
     * CHECK IF DUPLICATE
     * ==================
     *
     * @param $account
     * @param $accountNo
     * @param $purchaseOrder
     * @param $emailAddress
     * @param $brand
     *
     * @return bool|string
     */
    private function checkIfDuplicate($account, $accountNo, $purchaseOrder, $emailAddress, $brand) {
        $dse = DropshipEmailDetails::find()
                                   ->where(['account_id' => $account->id,
                                            'po'         => $purchaseOrder,
                                            'email'      => $emailAddress,
                                            'deleted_at' => null]);
        if ($brand && strlen($brand)) {
            $dse->andWhere(['brand' => $brand]);
        } else {
            $dse->andWhere(['brand' => null]);
        }

        return $dse->count() == 0 ? false : 'Duplicate Request';
    }

    /**
     * DELETE OLD EMAILS
     * =================
     * Soft deletes all previous emails for this account and purchase order
     *
     * @param $account
     * @param $accountNo
     * @param $purchaseOrder
     * @param $brand
     *
     * @return bool|string
     */
    private function deleteOldEmails($account, $accountNo, $purchaseOrder, $brand) {

        $dse = DropshipEmailDetails::updateAll(['deleted_at' => date('Y-m-d H:i:s')],
                                               ['account_id' => $account->id,
                                                'po'         => $purchaseOrder,
                                                'deleted_at' => null]);
    }


    /**
     * VERIFY DSE INPUTS
     * =================
     * Calls support methods to validate that the supplied data formats are correct,
     * then that the account is known a nda ht
     *
     * @param $accountNo
     * @param $purchaseOrder
     * @param $emailAddress
     * @param $customerPartcode
     * @param $quantity
     * @param $price
     *
     * @return array|bool|string
     */
    private function verifyDSEInputs($accountNo, $purchaseOrder, $emailAddress, $customerPartcode, $quantity, $price) {
        if (($result = $this->verifyDSEParametersProvided($accountNo, $purchaseOrder, $emailAddress)) === true) {
            if (($result = $this->verifyDropshipOrderDetails($customerPartcode, $quantity, $price)) === true) {
                if (($result = $this->verifyPO($purchaseOrder)) === true) {

                    $result = $this->verifyAccount($accountNo);
                }
            }
        }

        return $result;
    }


    /**
     * VERIFY DSE PARAMETERS PROVIDED
     * ==============================
     * Checks that each mandatory parameter was provided
     *
     * Deliberately returns the same message for all parameters
     *
     * @param $accountNo
     * @param $purchaseOrder
     * @param $emailAddress
     *
     * @return bool|string
     */
    private function verifyDSEParametersProvided($accountNo, $purchaseOrder, $emailAddress) {
        $result = true;

        if (!$accountNo) {
            $result = 'Missing Parameter';

        } elseif (!$purchaseOrder) {
            $result = 'Missing Parameter';

        } elseif (!$emailAddress) {
            $result = 'Missing Parameter';
        }

        return $result;
    }

    /**
     * VERIFY DROPSHIP ORDER DETAILS
     * =============================
     * This check that either all items, partcode, quantity and price, are
     * provided, or that all are absent.
     *
     * The price can be 0 (or 0.00), so can't check with empty(), and also
     * need to ensure the quantity is a positive, integral, value
     *
     * @param $customerPartcode
     * @param $quantity
     * @param $price
     *
     * @return array|string
     */
    private function verifyDropshipOrderDetails($customerPartcode, $quantity, $price) {

        if (!empty($customerPartcode) &&
            !empty($quantity) && is_numeric($quantity) > 0 &&
            is_int($quantity + 0) &&                                // + 0 to force the conversion needed by is_int
            isset($quantity) && is_numeric($price) && doubleval($price) >= 0
        ) {

            return true;
        }

        // -------------------------------------------------------------------
        // Not all set, so check that none are set
        // -------------------------------------------------------------------
        if (empty($customerPartcode) &&
            empty($quantity) &&
            empty($price)
        ) {
            return true;
        }

        return ['Invalid partcode, quantity and price combination', false];
    }

    /**
     * FIND ORACLE PRODUCT CODE
     * ========================
     * Attempts to translate the passed customer product code to the equivalent
     * oracle one, then locates and returns the matching DigitalProduct record.
     *
     * If no match is found, it assumes the code is actually the oracle one and
     * double checks by reading that.
     *
     * If all is OK, it returns the digital product, otherwise an error message
     *
     * @param $account
     * @param $customerPartcode
     *
     * @return mixed
     */
    private function findOracleProductCode($account, $customerPartcode) {
        $digitalProduct = false;

        if (!empty($customerPartcode)) {

            $translation = CustomerProductMapping::find()
                                                 ->where(['customer_account_number' => $account->customer_exertis_account_number])
                                                 ->where(['customer_partcode' => $customerPartcode])
                                                 ->one();
            if (!empty($translation)) {
                $customerPartcode = $translation->oracle_partcode;
            }

            $digitalProduct = DigitalProduct::find()
                                            ->where(['partcode' => $customerPartcode])
//                                            ->andWhere(['is_digital' => 1])
                                            ->one();

            if (empty($digitalProduct)) {
                $digitalProduct = 'Unrecognised partcode';
            }
        }

        return $digitalProduct;
    }

    /**
     * CHECK AND SAVE ORDER DETAILS
     * ============================
     * Attempts to translate the passed customer product code to the equivalent
     * oracle one, then locates and returns the matching DigitalProduct record.
     *
     * If no match is found, it assumes the code is actually the oracle one and
     * double checks by reading that.
     *
     * @param $account
     * @param $customerPartcode
     * @param $quantity
     * @param $price
     *
     * @return mixed
     */
    private function checkAndSaveOrderDetails($account, $customerPartcode, $quantity, $price) {
    }

    /**
     * VERIFY ACCOUNT
     * ==============
     *
     * @param $accountNo
     *
     * @return string
     */
    private function verifyAccount($accountNo) {
        $account = Account::find()->where(['customer_exertis_account_number' => $accountNo])->one();

        if (empty($account)) {
            $account = 'Invalid Account';
        }

        return $account;
    }


    /**
     * VERIFY PO
     * =========
     * Simply checks that the purchase order was non-blank
     *
     * @param $purchaseOrder
     *
     * @return bool|string
     */
    private function verifyPO($purchaseOrder) {
        if (strlen($purchaseOrder) > 0) {
            return true;
        }

        return 'Incorrect PO Number';
    }

    /**
     * SEND EMAIL FOR PRE-ORDERED ITEMS
     * ================================
     * This is called after a new drop ship email is recorded. It scans the
     * stock_item table for associated items where the keys were purchased
     * before this was recorded. For each one it then generates and sends
     * the notification email.
     *
     * @param $account
     * @param $dse
     *
     * @return int
     */
    private function sendEmailForPreOrderedItems($account, $dse) {

        $accountNumber = $dse->account_id;

        // -------------------------------------------------------------------
        // Read the full list of entries where the email has not been sent
        // -------------------------------------------------------------------
        $items = Orderdetails::find()
                             ->joinWith(['stockitem' => function($query) use ($accountNumber) {
                                 $query->where(['<>', 'status', StockItem::STATUS_NOT_PURCHASED])
                                       ->joinWith(['stockroom' => function($query) use ($accountNumber) {
                                           $query->joinWith(['account' => function($query) use ($accountNumber) {
                                               $query->where(['account.id' => $accountNumber]);
                                           }]);
                                       }]);

                             }])
                             ->where(['po' => $dse->po])
                             ->all();

        // -------------------------------------------------------------------
        // Now gather the stock item details and send the emails. The emailkeys
        // module handles grouping and sorting into one or more emails
        // -------------------------------------------------------------------
        $codes = [];
        if (count($items)) {
            $recipientDetails = [
                'email'       => $dse->email,
                'recipient'   => '',
                'orderNumber' => $dse->po,
                'message'     => ''
            ];

            $account = $items[0]->stockitem->stockroom->account;

            $emailer = new EmailKeys();
            foreach ($items as $order) {
                $codes[] = $order->stock_item_id;
            }
            if (count($codes)) {
                $emailer->completeEmailOrder($recipientDetails, $codes, $account, $dse->brand, true);
            }
        }
    }

    /**
     * Find Order
     * =============
     * URL to hit: http://api.edsr-working.com/v1/asn/find-order
     * Takes 4 parameters:
     *  - accountNo @string required Account Number
     *  - custPo @string required Customer PO
     *  - emailAddress @string optional, if not passed, the API won't send email just return the result
     *  - createAsn @boolean defaulted to true
     *
     * return @mixed
     */
    public function actionFindOrder($accountNo, $custPo, $emailAddress = null, $createAsn = null, $csv = null) {
        set_time_limit(120);
        $result = [];
        $errors = 0;

        // RCH 20160818
        // support a list of POs
        $poAry = explode(',', $custPo);

        \Yii::beginProfile(__METHOD__ . '#1');

        //Find all the orders with the given PO
        //$orders = Orderdetails::findAll(['po' => $poAry]);
        $orders = Orderdetails::find()
                              ->with(['stockitem'])
                              ->where(['po' => $poAry])->all();
        \Yii::endProfile(__METHOD__ . '#1');

        //If there is at least one order found...
        if (count($orders) > 0) {

            $results = 0;
            $csvrows = array();

            \Yii::beginProfile(__METHOD__ . '#2');

            //Loop through the orders
            foreach ($orders as $order) {
                //Check if the order belongs to the given account
                if (!isset($order->stockitem->stockroom->account)) {
                    continue;
                }

                if ($order->stockitem->stockroom->account->customer_exertis_account_number != $accountNo) {
                    //If not add error.
                    $errors++;
                } else {
                    //If all goes fine, group the orders by Stock Item ID and return the values
                    \Yii::beginProfile(__METHOD__ . '#2.1');
                    $result[$order->stockitem->id]['stock_item_id'] = $order->stockitem->id;
                    $result[$order->stockitem->id]['product_code']  = $order->stockitem->productcode;
                    $result[$order->stockitem->id]['product_name']  = $order->stockitem->productName;
                    $result[$order->stockitem->id]['product_key']   = $order->stockitem->key; //'11111-22222-33333-44444-55555'
                    $result[$order->stockitem->id]['po']            = $order->po;
                    \Yii::endProfile(__METHOD__ . '#2.1');

                    if ($csv) {
                        $csvrows[] = $this->str_putcsv($result[$order->stockitem->id]);
                    }
                    $results++;
                }
            } // foreach
            \Yii::endProfile(__METHOD__ . '#2');


            if ($errors > 0) {
                $result['status']  = 400;
                $result['message'] = 'PO (' . $custPo . ') does not belong to this account.';
            } else {
                $result['status']            = 200;
                $result['message']           = 'Order Found.';
                $result['footer']['total']   = $results;
                $result['footer']['pos']     = $poAry;
                $result['footer']['account'] = $accountNo;
            }

        } else {
            $result['status']  = 404;
            $result['message'] = 'Order (' . $custPo . ') not found.';
        }

        //Check if email address was passed in, if so send the email.
        if (isset($emailAddress)) {
            $result = ['message' => 'Send email to: ' . $emailAddress];
        }

        //Check if createAsn is true, if so create Asn otherwise do not create it.
        if ($createAsn == true) {
            $result['AsnCreated'] = true;
        }


        if ($csv) {
            \Yii::$app->response->format = Response::FORMAT_RAW;
            array_unshift($csvrows, 'stock_item_id, product_code,product_name,product_key,po');

            return implode("\n", $csvrows);
        } else {
            \Yii::$app->response->format = Response::FORMAT_JSON;

            return $result;
        }
    }


    private function str_putcsv($input, $delimiter = ',', $enclosure = '"') {
        $fp = fopen('php://temp', 'r+b');
        fputcsv($fp, $input, $delimiter, $enclosure);
        rewind($fp);
        $data = rtrim(stream_get_contents($fp), "\n");
        fclose($fp);

        return $data;
    }


}

