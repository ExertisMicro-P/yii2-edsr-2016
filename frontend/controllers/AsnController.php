<?php

namespace frontend\controllers;

use common\models\DropshipEmailDetails;
use yii\rest\ActiveController;
use common\models\Account;
use yii\web\Response;
use Yii;
use common\models\gauth\GAUser;

class AsnController extends ActiveController {

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
     *
     * @return array
     */
    public function actionSaveDropShipEmail($accountNo = null, $po = null, $email = null, $brand = null) {

        $responseCode = 400;

        $purchaseOrder = trim($po) ;
        $brand = trim($brand) ;

        if (($result = $this->verifySDEParametersProvided($accountNo, $po, $email)) === true) {

            $result = $this->verifyAccount($accountNo);

            if (is_object($result)) {
                $account = $result;

                $result = $this->verifyPO($account, $po);

                if ($result === true) {
                    if (($result = $this->recordDropShipEmail($account, $accountNo, $po, $email, $brand)) === true) {
                        $responseCode = 200;
                        $result       = 'success';
                    }
                }
            }
        }
        Yii::$app->response->format     = 'json';
        Yii::$app->response->statusCode = $responseCode;

        return ['message' => $result];
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
     * @return bool|string
     */
    private function recordDropShipEmail($account, $accountNo, $purchaseOrder, $emailAddress, $brand) {

        if (($result = $this->checkIfDuplicate($account, $accountNo, $purchaseOrder, $emailAddress, $brand)) === false) {
            $dse                  = new DropshipEmailDetails();
            $dse->account_id      = $account->id;
            $dse->account_no      = $accountNo;
            $dse->po              = $purchaseOrder;
            $dse->email           = $emailAddress;

            if ($brand && strlen($brand)) {
                $dse->brand = $brand ;
            }

            try {
                if ($dse->save()) {
                    $result = true;

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
                    $result = $exc->message();
                }
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
                                   ->where(['account_id' => $account->id])
                                   ->andWhere(['po' => $purchaseOrder])
                                   ->andWhere(['email' => $emailAddress]) ;
        if ($brand && strlen($brand)) {
            $dse->andWhere(['brand' => $brand]) ;
        } else {
            $dse->andWhere(['brand' => null]) ;
        }

        return $dse->count() == 0 ? false : 'Duplicate Request';
    }


    /**
     * VERIFY SDE PARAMETERS PROVIDED
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
    private function verifySDEParametersProvided($accountNo, $purchaseOrder, $emailAddress) {
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
     * @param $account
     * @param $purchaseOrder
     *
     * @return bool|string
     */
    private function verifyPO($account, $purchaseOrder) {
        if (strlen(trim($purchaseOrder)) > 0) {
            return true;
        }

        return 'Incorrect PO Number';
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

