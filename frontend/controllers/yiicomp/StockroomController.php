<?php
/**
 * Created by PhpStorm.
 * User: noelwalsh
 * Date: 08/12/2014
 * Time: 16:38
 */

namespace frontend\controllers\yiicomp;

use common\components\EmailKeys;
use common\models\DigitalProduct;
use common\models\EmailedItem;
use common\models\EmailedUser;
use Yii;
use kartik\mpdf\Pdf;
use yii\web\Controller;
use common\models\Stockroom;
use common\models\StockItem;
use common\models\StockItemSearch;
use common\models\SessionDelivering;

use yii\filters\AccessControl;
use common\components\DigitalPurchaser;

use exertis\savewithaudittrail\models\Audittrail;
use common\models\StockActivity;

use common\models\Account;

class StockroomController extends \frontend\controllers\EdsrController {

    const MAX_LIFE_IN_MINUTES = 20015;          // 15 minutes before a session order times out


    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['stockroom', 'header', 'selected', 'emailkeys', 'markkeysdelivered', 'viewkeys',
                                      'revieworders', 'orders', 'fetchkeys', 'reemailkey', 'checkselected', 'countdelivery', 'getkeylimit'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * HEADER
     * ======
     * This handles display and, possibly editing, of the stockroom name
     *
     * @return string
     */
    public function actionHeader() {
        $result = $this->getUserDetails();
        if ($result !== true) {

            $formatType = Yii::$app->request->get('t');

            if ($formatType == 'h') {
                return $this->getStockroomNames();

            } elseif ($formatType == 'd') {
                Yii::$app->response->format = 'json';

                return $this->user->getStockroomDetails();
            }
        }
        Yii::$app->response->format = 'raw';
        header('Content-Type: application/javascript');

        return $this->render('header.js', [

        ]);
    }


    public function actionOrders() {

    }

    /**
     * SELECTED
     * ========
     * This action will display the products the current user has selected but
     * has yet to deliver to a customer.
     *
     * @return string
     */
    public function actionSelected() {
        if ($this->clearOldSelectedItems() !== false) {

            $formatType = Yii::$app->request->get('t');

            if ($formatType == 'h') {
                return $this->showSelectedProducts();

            } elseif ($formatType == 'd') {
                Yii::$app->response->format = 'json';

                return $this->user->getStockroomDetails();
            }
        }

        Yii::$app->response->format = 'raw';
        header('Content-Type: application/javascript');

        return $this->render('selected.js', [

        ]);
    }


    public function actionGetkeylimit() {

        return (int)\common\models\Account::findOne(['id' => Yii::$app->user->identity->account_id])->key_limit;

    }


    public function actionCountdelivery() {

        return (int)SessionDelivering::find()->where(['account_id' => Yii::$app->user->identity->account_id, 'session_id' => session_id()])->sum('quantity');

    }


    /**
     * CHECK SELECTED
     * ==============
     * This method is used to either read the items currently selected for
     * delivery (by both the current user and others) or to save changes to
     * the list.
     */
    public function actionCheckselected() {
        $response = [];
        if ($this->clearOldSelectedItems() !== false) {
            if (Yii::$app->request->isPost) {
                $response                     = $this->saveFlaggedForDelivery();
                $response['allocatedAlready'] = $this->getAllocatedItems(false);

            } else {
                $response['allocatedBySelf']  = $this->getAllocatedItems(true);
                $response['allocatedAlready'] = $this->getAllocatedItems(false);
            }

//            $credit = $this->getCreditLevels();

            return json_encode($response);
        }

    }

    /**
     * SAVE FLAGGED FOR DELIVERY
     * =========================
     * This handles updating the list of products the current user wants to
     * deliver, detecting any which are already allocated to another user.
     */
    private function saveFlaggedForDelivery() {
        $response = ['status' => true, 'allocatedAlready' => [], 'errors' => []];

        // ---------------------------------------------------------------
        // First, read the user input and list them by stock item id
        // ---------------------------------------------------------------
        $items = json_decode(Yii::$app->request->post('pids'));

        $products = [];
        foreach ($items as $item) {
            $products[$item->stockItem] = $item;
        }

        $this->removeDeletedOrPreviouslySelectedItem($products);

        // ---------------------------------------------------------------
        // If any inputs remain, they now need to be added to the db.
        // The save will fail if another user has already flagged the item
        // for delivery, so we need to return that fact to update the UI
        // ---------------------------------------------------------------
        foreach ($items as $item) {
            $sOrder             = new SessionDelivering;
            $sOrder->account_id = $this->user->account->id;
            $sOrder->session_id = session_id();

            $sOrder->stockitem_id = $item->stockItem;

            $sOrder->photo       = $item->photo;
            $sOrder->partcode    = $item->partcode;
            $sOrder->description = $item->description;
            $sOrder->quantity    = $item->quantity;
            $sOrder->po          = $item->po;


            $keyLimit = $this->actionGetkeylimit();
            $delivery = $this->actionCountdelivery();

            if ($delivery < $keyLimit) {

                try {
                    $sOrder->save();

                } catch (\yii\db\Exception $exc) {
                    // -------------------------------------------------------
                    // PDO duplicate record error. Could do with a constant
                    // -------------------------------------------------------
                    if ($exc->errorInfo[1] == 1062) {
                        $response['allocatedAlready'][] = $sOrder->stockitem_id;
                    } else {
                        $response['errors'][] = $exc->message();
                    }
                }
            }
        }

        return $response;
    }

    /**
     * GET ALLOCATED ITEMS
     * ===================
     *
     * @param $response
     *
     * @return mixed
     */
    private function getAllocatedItems($currentUsersItems = false) {
        $sessionItems = SessionDelivering::find()
                                         ->where([($currentUsersItems ? 'and' : 'not'), ['created_by' => $this->user->id]])
                                         ->orderBy('stockitem_id')
                                         ->all();
        $items        = [];
        foreach ($sessionItems as $item) {
            $items['keys'][]  = $item->stockitem_id;
            $items['items'][] = $item->toArray();
        }

        return $items;
    }

    /**
     * REMOVE DELETED OR PREVIOUSLY SELECTED ITEMS
     * ===========================================
     * Reads the items the current user already has selected and compares them
     * to the new selected list. Any which are in both are removed from the
     * input list, to prevent them being added to the database again, while
     * any which are in the database but not the selection list, are removed
     * from the database.
     *
     * @param $products
     *
     * @throws \Exception
     */
    private function removeDeletedOrPreviouslySelectedItem(&$products) {
        // ---------------------------------------------------------------
        // First read any existing records for the current session
        // ---------------------------------------------------------------
        $sessionItems = SessionDelivering::find()
                                         ->where(['created_by' => $this->user->id])
                                         ->orderBy('stockitem_id')
                                         ->all();

        // ---------------------------------------------------------------
        // Iterate over the existing records and narrow the selections
        // down to just those which are new.
        // ---------------------------------------------------------------
        foreach ($sessionItems as $oItem) {
            $stockItemId = $oItem->stockitem_id;

            if (array_key_exists($stockItemId, $products)) {
                unset($products[$stockItemId]);

                // -----------------------------------------------------------
                // If this was originally added during a different session,
                // update the session id to help reduce the change of it
                // being purged.
                // -----------------------------------------------------------
                if ($oItem->session_id <> session_id()) {
                    $oItem->session_id = session_id();
                }
                $oItem->updated_at = null;     // This should force an update to the current timestamp value
                $oItem->save();

            } else {
                $oItem->delete();
            }
        }
    }


    /**
     * SHOW SELECTED PRODUCTS
     * ======================
     *
     * @return string
     */
    private function showSelectedProducts() {
        Yii::$app->response->format = 'raw';
        header('Content-Type: application/javascript');

        return $this->render('selected.html', [

        ]);
    }


    /**
     * STOCKROOM
     * =========
     * Use this to return the html, plus custom tags, for the overall
     * client view
     */
    public function actionStockroom() {

        $result = $this->getUserDetails();
        if ($result !== false) {

            $formatType = Yii::$app->request->get('t');

            if ($formatType == 'd') {


                Yii::$app->response->format = 'json';

                return $this->user->getStockroomDetails();
            }
        }

        \Yii::$app->session->setFlash('error', 'Not authorised');

        return Yii::$app->getResponse()->redirect('/', 400);
        $this->user = false;

    }

    /**
     * GET STOCK ROOM NAMES
     * ====================
     * Returns the list of stock rooms for the current user
     *
     * @return string
     */
    private function getStockroomNames() {
        $params = [
            'StockroomSearch' => [
                'account_id' => $this->user->account_id,
            ]
        ];

        // -------------------------------------------------------------------
        // Add any user provided search criteria. But make sure the params are
        // added last in case they sneakily try to submit an account_id value
        // -------------------------------------------------------------------
        $userInputs = Yii::$app->request->getQueryParams();
        if (is_array($userInputs)) {
            $params = array_merge($userInputs, $params);
        }

        $cLevel = new \common\components\CreditLevel($this->user);

        // -------------------------------------------------------------------
        //
        // -------------------------------------------------------------------
        return $this->render('header-html', [
            'credit' => $cLevel->readCurrentCredit(),
            //            'dataProvider' => $dataProvider,
            //            'searchModel' => $searchModel,
        ]);

    }

    /**
     * EMAIL KEYS
     * ==========
     */
    public function actionEmailkeys() {

        \Yii::info(__METHOD__);


        $status = 200;
        $errors = [];

        $result = $this->getUserDetails();
        \Yii::info(__METHOD__ . ':' . __LINE__);

        if (($user = $this->getUserDetails()) === false) {
            \Yii::info(__METHOD__ . ':' . __LINE__);
            $status   = 401;
            $response = 'Forbidden';

        } else {
            \Yii::info(__METHOD__ . ':' . __LINE__);
            $recipientDetails = $this->getAndValidateRecipientDetails(); // RCH 20160324 under investigation
            /**
             * @var array $selectedItems Array of Stockroom IDs select for email delivery
             */
            $selectedItems = $this->getValidStockItems($user);


            StockActivity::log('Emailing keys for ' . count($selectedItems) . ' products', $this->stockroomId,
                               null, $recipientDetails['orderNumber'],
                               EmailedItem::tableName(), null,
                               $recipientDetails['recipient'], $recipientDetails['email']
            );

            if (count($recipientDetails['errors']) > 0 ||
                count($selectedItems) == 0
            ) {
                \Yii::info(__METHOD__ . ':' . __LINE__);

                StockActivity::log('Emailing failed due to insufficient available stock', $this->stockroomId);
                $errors ['insufficient'] = count($selectedItems) == 0;
                $errors['recipient']     = $recipientDetails['errors'];

                StockActivity::log('Unable to email as not enough stock available', $this->stockroomId);

            } else {
                \Yii::info(__METHOD__ . ':' . __LINE__);
                $emailer = new EmailKeys();

                // RCH 20160125
                // Grab the company logo for this account
                $account = \common\models\Account::find()->where(['id' => $this->user->account_id])->one();

                $errors = $emailer->completeEmailOrder($recipientDetails, $selectedItems, $account);
            }

            if ($errors !== true && count($errors)) {
                \Yii::info(__METHOD__ . ':' . print_r($errors, true));

                $result = $errors;
                $status = 409;             // Conflict

            } else {
                $result = 'ok';
            }
        }
        \Yii::info(__METHOD__ . ':' . __LINE__);

        Yii::$app->response->format = 'json';
        Yii::$app->response->setStatusCode($status);

        return json_encode($result);

    }


    /**
     * MARK KEYS DELIVERED
     * ===================
     *
     * @return string
     * @throws
     */
    public function actionMarkkeysdelivered() {
        $status = 200;
        $errors = [];

        if (($user = $this->getUserDetails()) === false) {
            $status   = 401;
            $response = 'Forbidden';

        } else {
            $recipientDetails = $this->getAndValidateDeliveryDetails();
            $selectedItems    = $this->getValidStockItems($user);

            StockActivity::log('Flagging keys delivered for ' . count($selectedItems) . ' products', $this->stockroomId,
                               null, $recipientDetails['orderNumber'],
                               EmailedItem::tableName(), null
            );

            if (count($recipientDetails['errors']) > 0) {
                $errors = print_r($recipientDetails['errors'], true);

                StockActivity::log('Delivery failed due to insufficient available stock', $this->stockroomId);
                StockActivity::log('Unable to deliver', $errors);

                $response = $errors;
                $status   = 409;             // Conflict

            } elseif (count($selectedItems) == 0) {
                $response = 'No matching, undelivered, items';
                $status   = 404;

            } else {
                $account = \common\models\Account::find()->where(['id' => $this->user->account_id])->one();
                $emailer = new EmailKeys();

                if ($emailer->markKeysDelivered($recipientDetails, $selectedItems, $account)) {
                    $response = 'ok';
                } else {
                    $status = 404;
                }

            }

        }
        Yii::$app->response->format = 'json';
        Yii::$app->response->setStatusCode($status);

        return json_encode($response);
    }

    /**
     * RE-EMAIL KEY
     * ============
     *
     */
    public function actionReemailkey() {
        \Yii::info(__METHOD__);

        $status = 200;
        $errors = [];

        $result = $this->getUserDetails();
        if ($result !== false) {
            $recipientDetails = $this->getAndValidateRecipientDetails();

            $emailedItem = EmailedItem::find()->where(['id' => Yii::$app->request->post('eitem', 0)])->one();
            $stockItem   = $emailedItem->stockItem;

            \Yii::info(__METHOD__ . ': Emailling Stockitem ' . $stockItem->id);

            $pCodes[$stockItem->productcode] = ['quantity' => 1, 'description' => '', 'items' => []];
            $selectedCounts                  = ['codes' => $pCodes, 'errors' => null];

            StockActivity::log('Re-emailing key for product', $this->stockroomId,
                               null, $recipientDetails['orderNumber'],
                               EmailedItem::tableName(), null,
                               $recipientDetails['recipient'], $recipientDetails['email']
            );

            $productKey = DigitalPurchaser::getProductInstallKey($stockItem);

            $selectedCounts['codes'][$stockItem['productcode']] = [
                'description' => $stockItem->description,
                'faqs'        => $stockItem->digitalProduct->faqs,
                'items'       => [$stockItem->id],
                'downloadUrl' => [$stockItem->downloadURL],
                'keyItems'    => [$stockItem->id => $productKey]
            ];
//            $selectedCounts['codes'][$stockItem['productcode']]['items'][]     = $productKey;


            $recipientDetails[] = ['stockItemId' => $stockItem->id];

            $account = \common\models\Account::find()->where(['id' => $this->user->account_id])->one();

            $emailer = new EmailKeys();

            $result = $emailer->reEmailKeys($recipientDetails, $selectedCounts, $account);
//            $result = $this->sendOrderEmailToCustomer($recipientDetails, $selectedCounts, $stockItem->id);

        }
        Yii::$app->response->format = 'json';
        Yii::$app->response->setStatusCode($status);

        return json_encode($result);
    }

    /**
     * ACTION VIEW KEYS
     * ================
     * This is called with a product code and status code which it uses
     * to build a table listing the keys for all stock in that state.
     *
     */
    public function actionViewkeys() {
        $status = 200;
        $errors = [];

        $result = $this->getUserDetails();

        if ($result === false) {
            Yii::$app->response->setStatusCode('401');
            echo 'Forbidden';

            return;
        }

        $digitalProductId  = $_GET['pid'];
        $this->stockroomId = $_GET['stockroom'];
        $status            = $_GET['status'];

        return $this->fetchProductAndKeys($status, $digitalProductId);
    }

    /**
     * FETCH KEYS
     * ==========
     */
    public function actionFetchkeys() {

        if (($user = $this->getUserDetails()) === false) {
            $status   = 401;
            $response = 'Forbidden';

        } else {
            $status       = 200;
            $stockItemIds = Yii::$app->request->post('pid');

            $stockItems       = StockItem::find()
                                         ->where(['stock_item.id' => $stockItemIds])
                                         ->joinWith('stockroom')
                                         ->andWhere(['account_id' => $user->account_id])
                                         ->all();
            $response['keys'] = [];

            foreach ($stockItems as $stockItem) {
                $response['keys'][$stockItem->id] = DigitalPurchaser::getProductInstallKey($stockItem);
            }
        }

        Yii::$app->response->format = 'json';
        Yii::$app->response->setStatusCode($status);

        return json_encode($response);

    }


    /**
     * ACTION ORDER
     * ============
     * This should be used to generate an order history for a specific product
     *
     * @return string
     */
    public function actionRevieworders() {

        $status = 200;
        $errors = [];

        $digitalProductId  = Yii::$app->request->get('pid');
        $this->stockroomId = Yii::$app->request->get('stockroom');
        $status            = Yii::$app->request->get('status');

        $result = $this->getUserDetails();

        if ($result === false) {
            echo 'Forbidden';

            return;
        }

        return $this->fetchOrderHistory($status, $digitalProductId);
    }


    /**
     * GET AND VALIDATE SELECTED COUNTS
     * ================================
     * Gathers the number of items required per product as an array indexed
     * by product code.
     */
    private function getAndValidateSelectedCounts() {
        $selectedItems = Yii::$app->request->post('items');

        $pCodes = [];
        $total  = 0;

        foreach ($selectedItems as $item) {
            $pCode = $item['productCode'];
            if (!array_key_exists($pCode, $pCodes)) {
                $pCodes[$pCode] = ['description' => '', 'faqs' => '', 'items' => [], 'keyItems' => []];
            }
            $pCodes[$pCode]['items'][] = $item['pid'];
            $total++;
        }

//        $this->countAvailableItemPerProduct($pCodes);
//        $inSufficient = $this->checkAvailableCountsAreSufficient($pCodes);
        $inSufficient = [];

        return ['totalItems' => $total, 'codes' => $pCodes, 'errors' => $inSufficient];
    }

    /**
     * GET AND VALIDATE RECIPIENT DETAILS
     * ==================================
     */
    private function getAndValidateRecipientDetails() {
        $recipientDetails = [
            'email'       => trim(Yii::$app->request->post('email', '')),
            'recipient'   => trim(Yii::$app->request->post('recipient')),
            'orderNumber' => trim(Yii::$app->request->post('onumber')),
            'euserId'     => trim(Yii::$app->request->post('euser', 0)),
            'message'     => trim(Yii::$app->request->post('message', '')),
            'errors'      => []
        ];

        $recipientDetails['user'] = Yii::$app->getModule("user")->model("User")->findByEmail($recipientDetails['email']);

        if (empty($recipientDetails['email'])) {
            $recipientDetails['errors']['email'] = 'You must provide an email address';

        } elseif (!filter_var($recipientDetails['email'], FILTER_VALIDATE_EMAIL)) {
            $recipientDetails['errors']['email'] = 'This is not a valid email address';
        }

        if (empty($recipientDetails['recipient'])) {
            $recipientDetails['errors']['recipient'] = 'You must provide the recipient name';
        }
        if (empty($recipientDetails['orderNumber'])) {
            $recipientDetails['errors']['orderNumber'] = 'You must provide an order number';

        } else {
            $euser = EmailedUser::find()
                                ->where(['order_number' => StockItem::STATUS_DELIVERING . $recipientDetails['orderNumber'],
                                         'account_id'   => $this->user->account_id]);
            // ---------------------------------------------------------------
            // When re-emailing, we also get the emailuser id value, so need
            // to check that as well
            // ---------------------------------------------------------------
            if ($recipientDetails['euserId']) {
                $euser = $euser->andWhere(['<>', 'id', $recipientDetails['euserId']]);
            }
            if ($euser->count() > 0) {
                $recipientDetails['errors']['orderNumber'] = 'This order number (' . $recipientDetails['orderNumber'] . ')  has already been used';
            }
        }

        return $recipientDetails;
    }

    /**
     * GET AND VALIDATE DELIVERY DETAILS
     * =================================
     * This is used when the request is to mark a set of stock items as delivered
     *
     * @return mixed
     */
    private function getAndValidateDeliveryDetails() {
        $deliveryDetails = [
            'email'       => null,
            'recipient'   => null,
            'orderNumber' => trim(Yii::$app->request->post('onumber')),
            'message'     => '',
            'errors'      => []
        ];

        if (EmailedUser::find()->where(['order_number' => StockItem::STATUS_DELIVERING . $deliveryDetails['orderNumber'],
                                        'account_id'   => $this->user->account_id])->count() > 0
        ) {
            $recipientDetails['errors']['orderNumber'] = 'This order number (' . $deliveryDetails['orderNumber'] . ')  has already been used';
        }

        return $deliveryDetails;

    }

    /**
     * GET VALID STOCK ITEMS
     * =====================
     * Selects the requested stock items, filtering out any which don't
     * actually belong to the current user account.
     *
     * @param $user
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    private function getValidStockItems($user) {
        $stockItemIds = Yii::$app->request->post('items');

        // -------------------------------------------------------------------
        // This is because the deliver keys provides the id, but email sends
        // an array of details
        // -------------------------------------------------------------------
        $siIds = [];
        foreach ($stockItemIds as $sItem) {
            if (is_array($sItem)) {
                $siIds [] = $sItem['pid'];
            } else {
                $siIds[] = $sItem;
            }
        }

        return $this->validateStockKeys($user, $siIds);

    }

    /**
     * VALIDATE STOCK KEYS
     * ===================
     *
     * @param $siIds
     *
     * @return array
     */
    protected function validateStockKeys($user, $siIds, $unpurchased = true) {

        $query = StockItem::find()
                          ->select('stock_item.id, stockroom_id')
                          ->where(['stock_item.id' => $siIds])
                          ->joinWith('stockroom')
                          ->andWhere(['account_id' => $user->account_id]);

        if ($unpurchased) {
            $query->andWhere(['status' => StockItem::STATUS_PURCHASED]);
        } else {
            $query->andWhere(['<>', 'status', StockItem::STATUS_PURCHASED]);
        }

        $stockItems = $query->asArray()->all();

        $siIds = [];
        foreach ($stockItems as $sItem) {
            $siIds[] = $sItem['id'];
        }

        return $siIds;
    }


    /**
     * COUNT AVAILABLE ITEMS PER PRODUCT
     * =================================
     *
     * @param $pCodes
     */
    private function countAvailableItemPerProduct(&$pCodes) {

        $productCodes = array_keys($pCodes);

        $count  = StockItem::find()
                           ->where(['in', 'productcode', $productCodes])
                           ->andWhere(['stockroom_id' => $this->stockroomId])
                           ->andWhere(['<>', 'spare', StockItem::KEY_SPARE])// RCH 20150820
                           ->andWhere(['<>', 'spare', StockItem::KEY_HIDDEN])// RCH 20160229
                           ->groupBy(['productcode'])
                           ->select(['productcode, count(*) num'])
                           ->all();
        $counts = [];

        foreach ($count as $cnt) {
            $pCodes[$cnt->productcode]['available'] = $cnt->num;
        }
    }


    /**
     * CHECK AVAILABLE COUNTS ARE SUFFICIENT
     * =====================================
     * Compares the requested count to the actual available number for each
     * requested product and returns
     *
     * RCH 20160408 - Not used?
     *
     *      true            if all counts can be provided
     *      [counts]        an array of available counts where they can't be
     *
     * @param $pCodes
     *
     * @return array|bool
     */
    private function checkAvailableCountsAreSufficient($pCodes) {
        $inSufficient = [];

        foreach ($pCodes as $productCode => $details) {
            if (!array_key_exists('available', $details)) {
                $inSufficient[$productCode] = 0;

            } elseif ($details['available'] < $details['quantity']) {
                $inSufficient[$productCode] = $details['available'];
            }
        }

        //echo "\nEnd check ava";

        return count($inSufficient) == 0 ? [] : $inSufficient;
    }

    /**
     * UPDATE BY STOCK ITEM
     * ====================
     * Flags the passed stock items as delivered.
     *
     * @param $newStatusCode
     * @param $selectedItems
     *
     * @return bool
     */
    private function updateByStockItemId($newStatusCode, $selectedItems) {
        $ids = [];
        foreach ($selectedItems as $item) {
            $ids[] = $item->id;
        }

        StockItem::updateAll(['status' => $newStatusCode], ['id' => $ids]);

        return true;
    }


    /**
     * SEND ORDER EMAIL TO CUSTOMER
     * ============================
     *
     * @var Mailer                          $mailer
     * @var Message                         $message
     * @var \amnah\yii2\user\models\UserKey $userKey
     *
     * @param                               $recipientDetails
     * @param                               $selectedCounts
     * @param                               $account Account so we can include things like accountLogo
     *
     * @return mixed
     */
    private function sendOrderEmailToCustomer($recipientDetails, $selectedDetails, $stockId, $account = null) {

        $stockItemId = $stockId;

        \Yii::info(__METHOD__ . ': $recipientDetails=' . print_r($recipientDetails, true));
        \Yii::info(__METHOD__ . ': $selectedDetails=' . print_r($selectedDetails, true));
        \Yii::info(__METHOD__ . ': $account=' . print_r($account, true));

        $mailer           = Yii::$app->mailer;
        $oldViewPath      = $mailer->viewPath;
        $mailer->viewPath = '@common/mail';

        $subject = 'Exertis Digital Stock Room: ' . Yii::t("user", "Order Details for " . $recipientDetails['orderNumber']); // RCH orderNumber is actuall just an arbitrary ref entered by the user

        $account = Account::findOne(['id' => Yii::$app->user->identity->account_id]);

        //Check if account has a logo

        if (!$account->logo) {

            // RCH 20160425
            // We can't fail it here! it will prevent the email being sent and as this is called via AJAX
            // it'll fail silently, leaving a mess.
            // Consider generating another email to the user to ask them to set their logo up.
            //
            //
            //Yii::$app->getSession()->setFlash('warning', 'Please set a logo for your account.');
            //$this->redirect('/settings');

            //return;
        }


        //Pushing product codes into an array
        //$this->generateCSVfile($selectedDetails);
        $this->printKey($stockItemId);
        //$filename = 'codepdfs/code-'.Yii::$app->user->identity->id.'-'.Yii::$app->user->identity->account_id.'.pdf';

        $message = $mailer->compose('stockroom/orderedetails',
                                    compact("subject", "recipientDetails", "selectedDetails", "account"))
                          ->setTo([$recipientDetails['email'] => $recipientDetails['recipient']])
                          ->setBcc(Yii::$app->params['account.copyAllEmailsTo'])// RCH 20150420
                          ->setSubject($subject);
        //->attach($filename);

        // check for messageConfig before sending (for backwards-compatible purposes)
        if (empty($mailer->messageConfig["from"])) {
            $message->setFrom(Yii::$app->params["adminEmail"]);
        }
        $result = $message->send();
        // restore view path and return result
        $mailer->viewPath = $oldViewPath;

        return $result;
    }

    /*
     * Generate CSV File
     * Creates a CSV file for the codes
     */
    private function generateCSVfile($selectedDetails) {

        $codes = [];
        foreach ($selectedDetails['codes'] as $productCode => $details) {

            foreach ($details['keyItems'] as $productKey) {
                array_push($codes, $productKey);
            }
        }

        //Creating CSV file.
        $csvFile = "Codes\n";
        foreach ($codes as $code) {
            $csvFile .= $code . "\n";
        }

        $csv_handler = fopen('Codes.csv', 'w');
        fwrite($csv_handler, $csvFile);
        fclose($csv_handler);

    }

    /**
     * FETCH PRODUCT AND KEYS
     * ======================
     * For view keys
     *
     * @param $status
     * @param $digitalProductId
     *
     * @return string
     */
    private function fetchProductAndKeys($status, $digitalProductId) {
        $dprod = DigitalProduct::findOne($digitalProductId);

        $stockItems = StockItem::find()
                               ->where(['stockroom_id' => $this->stockroomId])
                               ->andWhere(['productcode' => $dprod->partcode])
                               ->andWhere(['status' => $status])
                               ->andWhere(['<>', 'spare', StockItem::KEY_SPARE])// RCH 20150820
                               ->andWhere(['<>', 'spare', StockItem::KEY_HIDDEN])// RCH 20160229
                               ->orderBy(['id' => SORT_DESC])
                               ->all();

        return $this->render('keyview', [
            'product' => $dprod,
            'items'   => $stockItems
        ]);
    }

    /**
     * FETCH ORDER HISTORY
     * ===================
     *
     * @param $status
     * @param $digitalProductId
     *
     * @return string
     */
    private function fetchOrderHistory($status, $digitalProductId) {
        $dprod = DigitalProduct::findOne($digitalProductId);

        $stockItems = StockItem::find()
                               ->where(['stockroom_id' => $this->stockroomId])
                               ->andWhere(['productcode' => $dprod->partcode])
                               ->andWhere(['status' => $status])
                               ->andWhere(['<>', 'spare', StockItem::KEY_SPARE])// RCH 20150820
                               ->andWhere(['<>', 'spare', StockItem::KEY_HIDDEN])// RCH 20160229
                               ->all();

        return $this->render('orderhistory', [
            'product' => $dprod,
            'items'   => $stockItems
        ]);
    }

    /**
     * CLEAR OLD SELECTED
     * ==================
     * Called at the start of each related process, this is used to remove
     * any items from other sessions which have 'expired'.
     *
     * Expired items are those which haven't been referenced in a pre-defined
     * time span. As we're checking the session, this can delete entries added
     * by the current user in an older session.
     *
     * @return mixed
     */
    private function clearOldSelectedItems() {
        $result = $this->getUserDetails();
        if ($result) {
            $sessionOrders = SessionDelivering::deleteAll(
                ['and', ['<>', 'session_id', session_id()],
                 ['>', 'TIMESTAMPDIFF(MINUTE, updated_at, NOW())', self::MAX_LIFE_IN_MINUTES]
                ]
            );
        }

        return $result;
    }


    //=========================PRINTING KEY==============================\\


    public function printKey($key) {
        $keys = explode(',', $key);

        return $this->printKeys($keys, false);
    }


    /**
     * PRINT KEYS
     * ==========
     *
     * @param $stockitem_ids
     * @param $unpurchasedOnly
     *
     * @return string|void|\yii\web\Response
     * @throws
     * @throws \Exception
     */
    private function printKeys($stockitem_ids, $unpurchasedOnly) {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect('/user/index');
        }

        if (($user = $this->getUserDetails()) === false) {
            return $this->redirect('/user/index');

        } else {
            $this->layout = false;

            $stockitem_ids = $this->validateStockKeys($user, $stockitem_ids, $unpurchasedOnly);

            StockActivity::log('Printing keys for ' . count($stockitem_ids) . ' products', $this->stockroomId,
                               null, null,
                               EmailedItem::tableName(), null
            );

            $recipientDetails = [
                'email'       => null,
                'recipient'   => null,
                'orderNumber' => 'PrintedKeys' . date('d-m-Y H:i:s'),
                'errors'      => []
            ];

            // ---------------------------------------------------------------
            // If we're re-printing, we don't need to record the details
            // ---------------------------------------------------------------
            if ($unpurchasedOnly) {
                $emailer = new EmailKeys();
                $account = \common\models\Account::find()->where(['id' => $this->user->account_id])->one();
                list($result, $selectedDetails) = $emailer->saveEmailedOrderDetails($recipientDetails, $stockitem_ids, $account);
            } else {
                $result = true;
            }

            if ($result === true) {
                $products = $this->findAllProducts($stockitem_ids);
                $html     = $this->printAllProducts($products);

                return $this->convertHtmlToPdf($html);

            } else {
                $status = 404;
                print_r($result);

                return 'Invalid request';

            }
        }
    }

    /**
     * FIND ALL PRODUCTS
     * =================
     * The printout is intend to use a view named after the partcode, so we
     * read that. In doing do, we also verify that the purchase belongs to
     * the currently logged in account and silently ignore any that don't.
     *
     * @param $keys
     * @param $sroomIds
     *
     * @return array
     */
    private function findAllProducts($keys) {
        $stockItems = StockItem::find()
                               ->select('stock_item.id, productcode, stockroom_id, eztorm_order_id, eztorm_product_id')// RCH 20160215 Added stockroom_id and eztorm_order_id to stop Reprint keys failing when we call DigitalPurchaser
                               ->where(['stock_item.id' => $keys])
                               ->joinWith('stockroom')
                               ->joinWith('digitalProduct')
                               ->joinWith('digitalProduct.productLeafletInfo')
                               ->andWhere(['account_id' => $this->user->account_id])
                               ->all();

        $products = [];

        foreach ($stockItems as $sitem) {

            $products[] = [
                'stockId'     => $sitem->id,
                'partcode'    => ($partcode = $sitem->productcode),
                'dpId'        => $sitem->digitalProduct->id,
                'description' => $sitem->digitalProduct->description,
                'image'       => $sitem->digitalProduct->image_url,
                'thumb'       => $sitem->digitalProduct->getMainImageThumbnailTag(false),
                'key'         => DigitalPurchaser::getProductInstallKey($sitem),
                'leaflet'     => [
//                    'image'      => Yii::getAlias('@webroot') . '/' . Yii::$app->params['uploadPath'] . 'product_leaflets/' . $partcode . '/' . $sitem->digitalProduct->productLeafletInfo->image,
'image_type' => $sitem->digitalProduct->productLeafletInfo->image_type,
'image'      => $sitem->digitalProduct->productLeafletInfo->getLeafletImageFilename(),

'key_xcoord'     => $sitem->digitalProduct->productLeafletInfo->key_xcoord,
'key_ycoord'     => $sitem->digitalProduct->productLeafletInfo->key_ycoord,
'key_box_width'  => $sitem->digitalProduct->productLeafletInfo->key_box_width,
'key_box_height' => $sitem->digitalProduct->productLeafletInfo->key_box_height,

'logo_xcoord'     => $sitem->digitalProduct->productLeafletInfo->logo_xcoord,
'logo_ycoord'     => $sitem->digitalProduct->productLeafletInfo->logo_ycoord,
'logo_box_width'  => $sitem->digitalProduct->productLeafletInfo->logo_box_width,
'logo_box_height' => $sitem->digitalProduct->productLeafletInfo->logo_box_height,

'name_xcoord'     => $sitem->digitalProduct->productLeafletInfo->name_xcoord,
'name_ycoord'     => $sitem->digitalProduct->productLeafletInfo->name_ycoord,
'name_box_width'  => $sitem->digitalProduct->productLeafletInfo->name_box_width,
'name_box_height' => $sitem->digitalProduct->productLeafletInfo->name_box_height
                ]
            ];
        }

//echo '<pre>';print_r($products);exit;
        return $products;
    }

    /**
     * PRINT ALL PRODUCTS
     * ==================
     * This builds an html string with the formatted html for all the requested
     * keys.
     *
     * @param $products
     *
     * @return string
     */
    private function printAllProducts($products) {
        $html = '';
        foreach ($products as $product) {
            $view = $this->findViewfileForProduct($product);

            $html .= $this->processProduct($product, $view);
//echo ($html);exit;
//break ;
        }

        return $html;
    }

    /**
     * FIND VIEW FILE FOR PRODUCT
     * ==========================
     * Each product can have its own customised view by creating a directory
     * named after it's partcode and including a view file called main.php.
     *
     * If that doesn't exist, we drop back to using default.php
     *
     * For convenience, any shared partials should be placed in the common directory
     *
     * @param $product
     *
     * @return string
     */
    private function findViewfileForProduct($product) {
        $baseViewPath  = $this->viewPath . '/';
        $localViewPath = '../../printkeys/mindypdftemplates/';

        $productPath = $localViewPath . $product['partcode'] . '/';
        $productView = $productPath . 'main.php';

        $fullPath = $baseViewPath . $productView;

        if (!is_file($fullPath) || !is_readable($fullPath)) {
            $productView = $localViewPath . 'default.php';
        } else {
            die (__METHOD__ . ': $fullPath=' . $fullPath);
        }

        return $productView;
    }

    /**
     * PROCESS PRODUCT
     * ===============
     * This is responsible for collecting the html corresponding to a single
     * product and it's key, as defined by the passed product and view file.
     *
     * @param $product
     * @param $view
     *
     * @return string
     */
    private function processProduct($product, $view) {

        // -----------------------------------------------------------------------
        // Creating the image using the raw data is intensive and needs a lot of ram
        // -----------------------------------------------------------------------
        ini_set("memory_limit", "256M");

        $leafletImage = $this->getLeafletGdImage($product);
        if ($leafletImage) {
            $this->addKeyToLeaflet($leafletImage, $product);
            $this->addNameToLeaflet($leafletImage, $product);

            $this->addAccountLogoToLeaflet($leafletImage, $product);

            // -----------------------------------------------------------------------
            // Allocate the temporary file and write the image to it
            // -----------------------------------------------------------------------
            $workImg = tempnam(Yii::getAlias('@frontend') . '/runtime/tmp', 'LEF') . '.jpg';
            imagejpeg($leafletImage, $workImg);

            return $this->renderPartial($view, [
                'workImg' => $workImg
            ]);
        }
    }

    /**
     * CHECK FONTS
     * ===========
     * Find the ttf font to use for the text output. The same font should ideally
     * be used in the editor at the back end
     */
    private function checkFonts() {
        if (!defined('TTF_DIR')) {
            if (!array_key_exists('fontLocation', Yii::$app->params)) {
                die('You must define params[\'fontLocation\']');

            } else {
                DEFINE("TTF_DIR", Yii::$app->params['fontLocation']);
            }

            if (!array_key_exists('fontFile', Yii::$app->params)) {
                die('You must define params[\'fontFile\'');

            } else {
                DEFINE("TTF_FONTFILE", Yii::$app->params['fontFile']);
            }
        }
    }

    private function getLeafletGdImage($product) {
        return $this->getImageObject($product['leaflet']['image_type'], $product['leaflet']['image']);
    }

    private function addKeyToLeaflet($leafletImage, $product) {
        $this->checkFonts();

        //$text = 'YF3CN-3WXXY-7XXM2-YXX86-2XXMF'; // Debug / Demo
        $text = $product['key'];

        // -----------------------------------------------------------------------
        // Create a text box with the desired font and size and output the key
        // -----------------------------------------------------------------------
        $fontFile = TTF_DIR . TTF_FONTFILE;
        $fontSize = 55;
        $angle    = 0;

        $dimensions = imagettfbbox($fontSize, $angle, $fontFile, $text);

        // -----------------------------------------------------------------------
        // Using the output from the above, we can calculate the actual width and
        // height of the text in pixels.
        // -----------------------------------------------------------------------
        $dimensionsOfText         = new \stdClass();
        $dimensionsOfText->width  = abs($dimensions[2] - $dimensions[0]);
        $dimensionsOfText->height = abs($dimensions[1] - $dimensions[7]);

        // -----------------------------------------------------------------------
        // Now do the same for the area allocated to the key, and the difference
        // gives the offsets to output the text so that it is both vertically and
        // horizontally centered in the defined area.
        // -----------------------------------------------------------------------
        $dimensionsOfSpace         = new \stdClass();
        $dimensionsOfSpace->width  = $product['leaflet']['key_box_width'];
        $dimensionsOfSpace->height = $product['leaflet']['key_box_height'];

        $xoffset = abs($dimensionsOfSpace->width - $dimensionsOfText->width) / 2;
        $yoffset = abs($dimensionsOfSpace->height - $dimensionsOfText->height) / 2;

        // -----------------------------------------------------------------------
        // Can now write the text on to the image, including the set x and y coords
        // -----------------------------------------------------------------------
        $black = ImageColorAllocate($leafletImage, 0, 0, 0);
        $xpos  = $product['leaflet']['key_xcoord'];
        $ypos  = $product['leaflet']['key_ycoord'] - $dimensions[5];

        imagettftext($leafletImage, $fontSize, $angle, $xpos + $xoffset, $ypos + $yoffset, $black, $fontFile, $text);
    }

    private function addNameToLeaflet($leafletImage, $product) {
        $this->checkFonts();

        //var_dump($product); die();

        //$text = 'YF3CN-3WXXY-7XXM2-YXX86-2XXMF'; // Debug / Demo
        $text = $product['description'];

        // -----------------------------------------------------------------------
        // Create a text box with the desired font and size and output the key
        // -----------------------------------------------------------------------
        $fontFile = TTF_DIR . TTF_FONTFILE;
        $fontSize = 40;
        $angle    = 0;

        $dimensions = imagettfbbox($fontSize, $angle, $fontFile, $text);

        // -----------------------------------------------------------------------
        // Using the output from the above, we can calculate the actual width and
        // height of the text in pixels.
        // -----------------------------------------------------------------------
        $dimensionsOfText         = new \stdClass();
        $dimensionsOfText->width  = abs($dimensions[2] - $dimensions[0]);
        $dimensionsOfText->height = abs($dimensions[1] - $dimensions[7]);

        // -----------------------------------------------------------------------
        // Now do the same for the area allocated to the key, and the difference
        // gives the offsets to output the text so that it is both vertically and
        // horizontally centered in the defined area.
        // -----------------------------------------------------------------------
        $dimensionsOfSpace         = new \stdClass();
        $dimensionsOfSpace->width  = $product['leaflet']['name_box_width'];
        $dimensionsOfSpace->height = $product['leaflet']['name_box_height'];

        $xoffset = abs($dimensionsOfSpace->width - $dimensionsOfText->width) / 2;
        $yoffset = abs($dimensionsOfSpace->height - $dimensionsOfText->height) / 2;

        // -----------------------------------------------------------------------
        // Can now write the text on to the image, including the set x and y coords
        // -----------------------------------------------------------------------
        $black = ImageColorAllocate($leafletImage, 0, 0, 0);
        $xpos  = $product['leaflet']['name_xcoord'];
        $ypos  = $product['leaflet']['name_ycoord'] - $dimensions[5];

        imagettftext($leafletImage, $fontSize, $angle, $xpos + $xoffset, $ypos + $yoffset, $black, $fontFile, $text);
    }

    private function addAccountLogoToLeaflet($leafletImage, $product) {
        if ($product['leaflet']['logo_box_width'] && $product['leaflet']['logo_box_height']) {
            $accountLogo = $this->user->account->getAccountLogo();
            if ($accountLogo) {

                $accountLogo = Yii::getAlias('@webroot') . $accountLogo;

                list($width, $height) = getimagesize($accountLogo);

                $finfo    = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $accountLogo);
                finfo_close($finfo);

                $logoGdImage = $this->getImageObject($mimeType, $accountLogo);

                $left     = $product['leaflet']['logo_xcoord'];
                $top      = $product['leaflet']['logo_ycoord'];
                $lbWidth  = $product['leaflet']['logo_box_width'];
                $lbHeight = $product['leaflet']['logo_box_height'];

                if ($width > $lbWidth || $height > $lbHeight) {
                    $scale   = $scaleX = $scaleY = 0;
                    $owidth  = $width;
                    $oheight = $height;

                    if ($width > $lbWidth) {
                        $scaleX = $lbWidth / $width;
                    }
                    if ($height > $lbHeight) {
                        $scaleY = $lbHeight / $height;
                    }

                    if ($scaleX <> 0 && $scaleY <> 0) {
                        $scale = min($scaleX, $scaleY);

                    } elseif ($scaleX > 0) {
                        $scale = $scaleX;

                    } elseif ($scaleY > 0) {
                        $scale = $scaleY;
                    }

                    if ($scale) {
                        $owidth *= $scale;
                        $oheight *= $scale;
                    }

                    $xoffset = $left + abs($lbWidth - $width) / 2;
                    $yoffset = $top + abs($lbHeight - $height) / 2;

                    imagecopyresized($leafletImage, $logoGdImage,
                                     0, 0, 0, 0,
                                     $xoffset, $yoffset,
                                     $width, $height, $owidth, $oheight);

                } else {
                    imagecopy($leafletImage, $logoGdImage, $left, $top, 0, 0, $width, $height);
                }
            }
        }

    }


    private function getImageObject($imageType, $imageFile) {
        switch ($imageType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($imageFile);
                break;
            case 'image/png':
                return imagecreatefrompng($imageFile);
                break;
            case 'image/gif':
                return imagecreatefromgif($imageFile);
                break;
        }

        return false;
    }


    /**
     * CONVERT HTML TO PDF
     * ===================
     * This processes the passed html string to convert it into a pdf file,
     * which it then echos to the browser as a downloadable file called key.pdf
     *
     * @param $html
     */
    private function convertHtmlToPdf($html) {
        $filename = 'codepdfs/code-' . Yii::$app->user->identity->id . '-' . Yii::$app->user->identity->account_id . '.pdf';

        $pdf = new Pdf([
                           'mode'        => Pdf::MODE_CORE,
                           'format'      => 'A4',  //  Pdf::FORMAT_A4,
                           'orientation' => Pdf::ORIENT_PORTRAIT,

                           'methods' => [
                               'SetHeader' => ['Krajee Report Header'],
                               'SetFooter' => ['{PAGENO}'],
                           ],

                           'content'     => $html,
                           'filename'    => $filename,
                           'destination' => Pdf::DEST_FILE,

                           'cssFile' => 'css/mindy.css',

                       ]);

        echo $pdf->render();

        return;

        /*$mpdf = new \mPDF();
        $mpdf->WriteHTML($html);
        $mpdf->Output('code.pdf', 'F');*/

        // -------------------------------------------------------------------
        // Produce a single pdf with all pages at A4 size
        // -------------------------------------------------------------------
//        $workPDF = tempnam(Yii::getAlias('@frontend') . '/runtime/tmp', 'FOO');
//
//
//        $options = ['user-style-sheet' => Yii::getAlias('@webroot') . '/css/mindy.css'];
//
//        $snappy = new Pdf('/usr/local/bin/wkhtmltopdf');
//        $snappy->generateFromHtml($html, $workPDF, $options, true);
//
//        header('Content-Type: application/pdf');
//        header('Content-Disposition: attachment; filename="key.pdf"');
//        echo file_get_contents($workPDF);

    }


    //===================================================================\\

}
