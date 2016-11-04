<?php
namespace frontend\controllers;

use common\models\DigitalProduct;
use common\models\EmailedItem;
use common\models\EmailedUser;
use common\models\Orderdetails;
use common\models\SalesRepOrder;
use common\models\SessionOrder;
use yii\data\ActiveDataProvider;

use Yii;
use yii\web\Controller;
use common\models\Stockroom;
use common\models\StockItem;
use common\models\StockItemSearch;

use yii\filters\AccessControl;
use common\components\DigitalPurchaser;

use exertis\savewithaudittrail\models\Audittrail;
use common\components\CreditLevel;
use common\models\Accounts;
use console\components\OrderFeedFile\OrderFileParser;
use common\components\ItemPurchaserHelper ;



class CheckoutController extends OrdersController
{
    use itemPurchaserHelper ;


    /**
     * BEHAVIOURS
     * ==========
     *
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'purchase', 'getlimit', 'getbasket', 'getitemqty'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * INDEX
     * =====
     * The main entry point
     */
    public function actionIndex()
    {
        return $this->showCheckout();
    }

    /**
     * PURCHASE
     * ========
     * This action handles the 'payment' process and receives the full list of
     * ordered products together with their quantities.
     *
     * Before any real processing takes place, it has to confirm the quantities
     * and the actual prices can be accommodated by the current credit balance. If
     * not, it returns an error to the caller
     *
     * @return string
     */
    public function actionPurchase()
    {
        $this->getUserDetails();
        $result = [];

        if (Yii::$app->request->post('quant', '') == '') {
            $result['status'] = false;
            $result['error']  = 'You have nothing selected for purchase';

        } elseif (($userPo = trim(strtoupper(Yii::$app->request->post('po', '')))) == '') {
            $result['status'] = false;
            $result['error']  = 'You must provide a purchase order';

        } elseif (strlen($userPo) > Orderdetails::MAXUSERPOLENGTH) {
            $result['status'] = false;
            $result['error']  = 'The PO can be at most 27 characters long';

        } elseif (preg_match('/^[a-z0-9\_:\/]{1,27}$/i', $userPo) <> 1) {
            $result['status'] = false;
            $result['error']  = 'The PO must only contain letters, numbers, underscores or colons';

        } else {
            $userSelections = Yii::$app->request->post('quant', 0) ;

            $records = $this->_gatherDetails($userSelections);
            if (!$this->checkOrderAgainstCreditBalance($records)) {
                $result['status'] = false;
                $result['error']  = 'Your credit balance is too low to pay for this order';

            } else {
                $result = $this->completeThePurchase($userPo, $records);
                if ($result['status']) {
                    $this->clearSessionOrder();
                }
            }
        }

        return json_encode($result);
    }


    /**
     * CLEAR SESSION ORDER
     * ===================
     * Added a test for the account as well to support sales reps placing
     * orders, which they may do for multiple clients in one session
     */
    private function clearSessionOrder()
    {
        $accountId = $this->user->account->id;

        $sessionOrders = SessionOrder::deleteAll(
            ['session_id' => session_id(), 'account_id' => $accountId]
        );
        //->where(['account_id' => $accountId]) ;
    }

    public function actionGetbasket(){
        return SessionOrder::find()->where(['account_id' => Yii::$app->user->identity->account_id, 'session_id' => session_id()])->sum('quantity');
    }

    public function actionGetlimit(){
        return \common\models\Account::find()->where(['id' => Yii::$app->user->identity->account_id])->one()->key_limit;
    }

    public function actionGetitemqty($item){
        return SessionOrder::find()->where(['account_id' => Yii::$app->user->identity->account_id, 'session_id' => session_id(), 'partcode'=>$item])->one()->quantity;
    }


    /**
     * SHOW ORDER PAGE
     * ===============
     */
    public function showCheckout()
    {
        $this->getUserDetails();
        $orders = $this->getFlaggedToBuy();

        $this->layout = '@frontend/views/layouts/mainnw';
        $cLevel       = new CreditLevel($this->user);

        $bodyContent = $this->renderPartial('checkout', [
            'title'        => 'Orders',
            'dataProvider' => $this->getProvider(),
            'credit'       => $cLevel->readCurrentCredit()
        ]);

        return $this->render('/site/customerHome', [
            'bodyContent' => $bodyContent
        ]);

    }

    /**
     * GET SESSION ORDER ID
     * ====================
     * This reads one of the order records saved in the session table for this
     * persons session, simply to give us an unique id for uses elsewhere.
     *
     * Added a test for the account as well to support sales reps placing
     * orders, which they may do for multiple clients in one session
     */
    private function getSessionOrderId()
    {
        $accountId = $this->user->account->id;

        $sorder = SessionOrder::find()
            ->select('id')
            ->where(['session_id' => session_id()])
            ->andWhere(['account_id' => $accountId])
            ->one();

        if ($sorder) {
            $this->sessionOrderId = $sorder->id;
            $this->SOP            = 'sid' . $sorder->id;

        } else {
            Yii::error(__METHOD__.': Issue getting ID. session_id='.session_id().'/ account_id = '.$accountId);
            // ---------------------------------------------------------------
            // Should never get here, but in case we do, create a hopefully
            // unique, 23 character,  value based on the logged in user's id.
            // ---------------------------------------------------------------
            // RCH 20160701
            //$this->sessionOrderId = uniqid($this->user->id, true);
            $this->sessionOrderId = $this->_getSixDigitUniqId();
            $this->SOP = 'sid' . $this->sessionOrderId;
        }

        return $this->SOP;

    }

    /**
     * GET PROVIDER
     * ============
     * Added a test for the account as well to support sales reps placing
     * orders, which they may do for multiple clients in one session
     *
     * @return ActiveDataProvider
     */
    private function getProvider()
    {
        $accountId = $this->user->account->id;

        $query = SessionOrder::find()
            ->where(['session_id' => session_id()])
            ->andWhere(['account_id' => $accountId])
            ->joinWith('product');

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * GATHER DETAILS
     * ==============
     * Reads the details of each ordered item, including the customer specific
     * price, the requested quantity and the total cost
     *
     * @param $selections
     *
     * @return array
     */
    private function _gatherDetails($selections)
    {
        $partcodes = [];
        $account   = $this->user->account->customer_exertis_account_number;

        foreach ($selections as $partcode => $quantity) {
            $partcodes[] = $partcode;
        }

        $items     = DigitalProduct::find()
            ->where(['partcode' => $partcodes])
            ->all();
        $itemArray = [];
        foreach ($items as $item) {

            $itemDetails             = $item->toArray();
            $itemDetails['cost']     = ($cost = $item->getItemPrice($account)); //->one()->item_price);
            $itemDetails['quantity'] = ($quantity = $selections[$item->partcode]);
            $itemDetails['total']    = $quantity * $cost;
            $itemArray[]             = $itemDetails;
        }
        return $itemArray;
    }

    /**
     * CHECK ORDER AGAINST CREDIT BALANCE
     * ==================================
     * Sums the total ordered price and compares it against the current credit
     * balance, returning true if it can be paid for.
     *
     * @param $records
     *
     * @return bool
     */
    private function checkOrderAgainstCreditBalance($records)
    {
        $totalCost = 0;
        foreach ($records as $record) {
            $totalCost = $record['total'];
        }

        $credit = $this->getCurrentCredit();

        //return ($credit['balance'] > $totalCost;
        return ($credit['limit'] + $credit['balance']) > $totalCost; // RCH 20150212
    }


    /**
     * GET CURRENT CREDIT
     * ==================
     * This returns an array of the credit limit and balance, adjusted to allow
     * for all known items not included in the main balance.
     *
     * This is duplicated elsewhere - need to move to shared base controller
     *
     * @return array
     */
    private function getCurrentCredit()
    {
        $account = $this->user->account;
        $credit  = $account->credit;

        $result = [
            'limit'   => $credit ? $credit->overall_credit_limit : 0,
            'balance' => $credit ? $credit->available_credit : 0,
        ];

        return $result;
    }




}
