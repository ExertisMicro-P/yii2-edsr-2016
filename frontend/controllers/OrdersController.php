<?php
/**
 * Created by PhpStorm.
 * User: noelwalsh
 * Date: 08/12/2014
 * Time: 16:38
 */

namespace frontend\controllers;

use common\models\DigitalProduct;
use common\models\EmailedItem;
use common\models\EmailedUser;
use common\models\Orderdetails;
use common\models\SessionOrder;

use Yii;
use yii\web\Controller;
use common\models\Stockroom;
use common\models\StockItem;
use common\models\StockItemSearch;

use yii\filters\AccessControl;
use common\components\DigitalPurchaser;

use exertis\savewithaudittrail\models\Audittrail;
use common\components\CreditLevel ;
use common\models\Accounts;

class OrdersController extends EdsrController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'list', 'flaggedtobuy', 'current-credit' ,'detail', 'getkeylimit', 'countbasket', 'spendinglimitreached', 'basketvalue', 'getspendinglimit', 'ajax-get-install-key'],
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
        if (\Yii::$app->user->isGuest) {
            return $this->showDefaultHomePage();
        }

        if (Yii::$app->session->get('internal_user') && !  Yii::$app->session->get('current_account')) {
            return $this->redirect('dashboard') ;
        }

        /*$this->layout = '@frontend/views/layouts/mainnw';
        
        $query = \common\models\StockItem::find()
                ->joinWith('stockroom')
                ->andWhere(['LIKE', 'status', '#'])
                ->andWhere(['=', 'stockroom.account_id', Yii::$app->user->identity->account_id])
                ->orderBy('timestamp_added desc');
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query
        ]);
        
        

        $bodyContent = $this->renderPartial('list-html', [
            'title'        => 'Orders',
            'dataProvider' => $dataProvider,
        ]);

        return $this->render('/site/customerHome', [
            'bodyContent' => $bodyContent
        ]);*/
        return $this->showOrderPage();
    }

    /**
     * DETAIL
     * ======
     */
    public function actionDetail() {

        $siId = Yii::$app->request->post('expandRowKey') ;

        $stockItem = StockItem::find()
                    ->where(['id' => $siId])
                    ->one() ;

        return Yii::$app->controller->renderPartial('order-detail', ['model' => $stockItem->emailedUser]);
    }

    /**
     * SHOW ORDER PAGE
     * ===============
     */
    public function showOrderPage()
    {
        $this->getUserDetails();
        $orders = $this->ordertable();

        $this->layout = '@frontend/views/layouts/mainnw';

        $bodyContent = $this->renderPartial('list-html', [
            'title'        => 'Orders',
            'dataProvider' => $orders['orderDetails']['provider'],
            'searchModel'  => $orders['orderDetails']['model']
        ]);

        return $this->render('/site/customerHome', [
            'orders'      => $orders,
            'bodyContent' => $bodyContent
        ]);

    }

    /**
     * FLAGGED TO BUY
     * ==============
     * Either reads or updates the list of items the current user has flagged
     * as intending to buy.
     *
     * @return string
     */
    public function actionFlaggedtobuy()
    {
        $this->getUserDetails();
        $result = [] ;

        if (Yii::$app->request->isPost) {
            $this->saveFlaggedOrders();
    	} else {
            $result = $this->getFlaggedToBuy() ;
        }

        $credit = $this->getCreditLevels () ;
        return json_encode(['selected' => $result, 'credit' => $credit]) ;
    }
    
    //Ajax call /web/js/getInstallKey.js
    public function actionAjaxGetInstallKey($id){
        $stockItem = StockItem::findOne(['id'=>$id]);
        
        if($stockItem->stockroom->account_id <> Yii::$app->user->identity->account_id){
            $result = 'This key does not belong to your account.';
        } else {
            $result = \common\components\DigitalPurchaser::getProductInstallKey($stockItem);
        }
        
        return $result;
    }
    
    
    //Ajax call /web/edsr/src/components/basket.js
    public function actionSpendinglimitreached(){
       
        $mainUser = Yii::$app->user->identity->account->findMainUser();
        
        $basket = SessionOrder::find()->where(['account_id' => Yii::$app->user->identity->account_id, 'session_id' => session_id()])->all();
        $basketValue = 0;
        $spendingReached = false;
        
        foreach($basket as $b){
            $basketValue += $b['quantity'] * $b['cost'];
        }
        
        if($basketValue > Yii::$app->user->identity->spending_limit) {
            $spendingReached = true;
        } else {
            $spendingReached = false;
        }
        
        if($mainUser->id == Yii::$app->user->identity->id) {
            $spendingReached = false;
        }
        
        return $spendingReached;
        
    }
    
    //Ajax call /web/edsr/src/components/basket.js
    public function actionGetspendinglimit(){
        
        $mainUser = Yii::$app->user->identity->account->findMainUser();
       
        if($mainUser->id == Yii::$app->user->identity->id){
            return 999999;
        } else {
            return Yii::$app->user->identity->spending_limit;
        }
    }
    
    
    public function actionBasketvalue(){
        
        $basket = SessionOrder::find()->where(['account_id' => Yii::$app->user->identity->account_id, 'session_id' => session_id()])->all();
        $basketValue = 0;
        
        foreach($basket as $b){
            $basketValue += $b['quantity'] * $b['cost'];
        }
        
        return $basketValue;
        
    }
    
    
    public function actionGetkeylimit(){
        
        return \common\models\Account::findOne(['id' => Yii::$app->user->identity->account_id])->key_limit;
        
    }
    
    
    public function actionCountbasket(){
        
        return SessionOrder::find()->where(['account_id' => Yii::$app->user->identity->account_id, 'session_id' => session_id()])->sum('quantity');
        
    }
    

    /**
     * GET FLAGGED TO BUY
     * ==================
     * Retrieves the list of previously picked items for the current session
     *
     * @return string
     */
    protected function getFlaggedToBuy()
    {
        // ---------------------------------------------------------------
        // Now read any existing records for the current session
        // ---------------------------------------------------------------
        $accountId = $this->user->account->id;

        $sessionOrders = SessionOrder::find()
            ->where(['session_id' => session_id()])
            ->andWhere(['account_id' => $accountId])
            ->orderBy('product_id')
            ->all();

        $oItems = [];
        foreach ($sessionOrders as $oItem) {
            $oItems[] = $oItem->attributes;
        }

        return $oItems ;
    }

    /**
     * SAVE FLAGGED ORDERS
     * ===================
     *
     * @throws \Exception
     */
    private function saveFlaggedOrders()
    {   
        // ---------------------------------------------------------------
        // First, read the user input and list them by digital product id
        // ---------------------------------------------------------------
        $items = json_decode(Yii::$app->request->post('items'));

        $products = [];
        foreach ($items as $item) {
            $products[$item->item->product_id]           = $item->item;
            $products[$item->item->product_id]->quantity = $item->quantity;
        }

        // ---------------------------------------------------------------
        // Now read any existing records for the current session
        // ---------------------------------------------------------------
        $accountId = $this->user->account->id;

        $sessionOrders = SessionOrder::find()
            ->where(['session_id' => session_id()])
            ->andWhere(['account_id' => $accountId])
            ->orderBy('product_id')
            ->all();

        // ---------------------------------------------------------------
        // Iterate over the existing records and either update the count
        // or delete the record if set to zero or not found in the input
        // ---------------------------------------------------------------
        foreach ($sessionOrders as $oItem) {
            $productId = $oItem->product_id;
            
            if (array_key_exists($productId, $products)) {
                $quantity = $products[$productId]->quantity;
                if ($quantity) {
                    $oItem->quantity = $quantity;

                    if (!$oItem->save()) {
                        print_r($oItem->errors);
                    }
                } else {
                    $oItem->delete();
                }
                unset($products[$productId]);

            } else {
                $oItem->delete();
            }
        }

        // ---------------------------------------------------------------
        // If any inputs remain, they now need to be added to the db.
        // ---------------------------------------------------------------
        foreach ($products as $productId => $product) {
            $sOrder             = new SessionOrder;
            $sOrder->product_id = $productId;
            $sOrder->account_id = $this->user->account->id;
            $sOrder->session_id = session_id();

            $sOrder->photo       = $product->photo;
            $sOrder->partcode    = $product->partcode;
            $sOrder->description = $product->description;
            $sOrder->cost        = $product->cost;
            $sOrder->quantity    = $product->quantity;

            if (Yii::$app->session->get('internal_user')) {
                $sOrder->sales_rep_id = Yii::$app->session->get('internal_user') ;
            }

            if (!$sOrder->save()) {
                print_r($sOrder->errors);
                exit;
            }
        }
    }

    /**
     * CURRENT CREDIT
     * ==============
     * This will return the outstanding credit balance for the account,
     * reading the cleared value from the credit table and then subtracting
     * the value of
     *      1) all items recorded in the session_order
     *      2) all entries in the stock_item table which haven't been cleared
     *         this is flagged by the fact that the filename is missing from
     *         the associated orderedetails record.
     */
    public function actionCurrentCredit()
    {
        return json_encode($this->getCreditLevels()) ;
    }

    /**
     * GET CREDIT LEVELS
     * =================
     * @return string
     */
    private function getCreditLevels() {
        $this->getUserDetails();
        $cLevel = new CreditLevel($this->user) ;

        return $cLevel->readCurrentCredit() ;
    }

    /**
     * ORDER TABLE
     * ===========
     * This actually returns the stock items
     *
     * @return string
     */
    public function ordertable()
    {

        $sHandler = new StockHandler ($this);

        $stockRooms = $this->user->getStockroomDetails();

        // RCH 20160812
        // added [0] - later we may need to change this if we support multiple stock rooms per account
        $results                  = $sHandler->getOrderedList($this->user->stockrooms[0]->id);
        $results['accountStatus'] = $stockRooms['accountStatus'];

        return $results;
    }


}
