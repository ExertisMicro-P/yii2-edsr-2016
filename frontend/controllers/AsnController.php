<?php

namespace frontend\controllers;

use yii\rest\ActiveController;
use common\models\Orderdetails;
use yii\web\Response;
use Yii;
use common\models\gauth\GAUser;

class AsnController extends ActiveController {
    
  public $modelClass = '\common\models\StockItem';
//    public $modelClass = 'api\modules\v1\models\StockItem';
 
    public function init() {
        parent::init();
        \Yii::$app->user->enableSession = false;
        
        
    }
    
    public function beforeAction($action) {
        $before = parent::beforeAction($action);
        
        unset($before['index']);

        
        $userIp = Yii::$app->request->userIp;
        $firstThree = substr($userIp, 0, 3);
        
        if($firstThree != '172' && $firstThree != '127'){
            return false;
        }
        
        return $before;
    }
    
    /**
     * ACTIONS
     * =======
     * Remove the index option to prevent a called obtaining a list of all products
     *
     * @return array
     */
    public function actions() {
        $actionList = parent::actions() ;

        unset($actionList['index']) ;

        return $actionList ;
    }
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\HttpBasicAuth::className(),
            'auth' => [$this, 'auth'],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => \yii\filters\ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviors;
    }
    
     public function auth($username, $password) {
        
        $model = new GAUser();
        
        if(empty($username) || empty($password)) {
            return null;
        }
        
        $user = $model->findOne(['username' => $username]);
        
        if(!$user) {
            return null;
        }
        
        if(!\Yii::$app->security->validatePassword($password, $user->password)){
            return null;
        }
        
        return $user;
        
    /**
     * SAVE DROP SHIP EMAIL
     * ====================
     * This is intended to be called from the EDI system to record the drop
     * ship email address for the passed purchase order and, if the order has
     * been processed, send the key via email to the purchaser.
     *
     * @param $accountNo
     * @param $custPo
     * @param $emailAddress
     */
    public function actionSaveDropShipEmail($accountNo=null, $custPo=null, $emailAddress=null ){
        die('sde ' . time());
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
    public function actionFindOrder($accountNo, $custPo, $emailAddress=null, $createAsn=null, $csv=null){
        set_time_limit(120);
        $result = [];
        $errors = 0;
        
        // RCH 20160818
        // support a list of POs
        $poAry = explode(',',$custPo);
        
        \Yii::beginProfile(__METHOD__.'#1');
        
        //Find all the orders with the given PO
        //$orders = Orderdetails::findAll(['po' => $poAry]);
        $orders = Orderdetails::find()
                    ->with(['stockitem'])
                    ->where(['po' => $poAry])->all();
        \Yii::endProfile(__METHOD__.'#1');
        
        //If there is at least one order found...
        if(count($orders) > 0){

            $results = 0;
            $csvrows = array();

            \Yii::beginProfile(__METHOD__.'#2');
            
            //Loop through the orders
            foreach($orders as $order){
                //Check if the order belongs to the given account
                if (!isset($order->stockitem->stockroom->account)) continue;
                
                if($order->stockitem->stockroom->account->customer_exertis_account_number != $accountNo){
                    //If not add error.
                    $errors++;
                } else {
                    //If all goes fine, group the orders by Stock Item ID and return the values
                    \Yii::beginProfile(__METHOD__.'#2.1');
                    $result[$order->stockitem->id]['stock_item_id'] = $order->stockitem->id;
                    $result[$order->stockitem->id]['product_code'] = $order->stockitem->productcode;
                    $result[$order->stockitem->id]['product_name'] = $order->stockitem->productName;
                    $result[$order->stockitem->id]['product_key'] = $order->stockitem->key; //'11111-22222-33333-44444-55555'
                    $result[$order->stockitem->id]['po'] = $order->po;
                    \Yii::endProfile(__METHOD__.'#2.1');
                    
                    if ($csv) {
                        $csvrows[] = $this->str_putcsv($result[$order->stockitem->id]);
                    }
                    $results++;
                }
            } // foreach
            \Yii::endProfile(__METHOD__.'#2');


            if($errors > 0){
                $result['status'] = 400;
                $result['message'] = 'PO ('.$custPo.') does not belong to this account.';
            } else {
                $result['status'] = 200;
                $result['message'] = 'Order Found.';
                $result['footer']['total'] = $results;
                $result['footer']['pos'] = $poAry;
                $result['footer']['account'] = $accountNo;
            }   

        } else {
                $result['status'] = 404;
                $result['message'] = 'Order ('.$custPo.') not found.';
        }

        //Check if email address was passed in, if so send the email.
        if(isset($emailAddress)){
            $result =  ['message' => 'Send email to: ' . $emailAddress];
        }
        
        //Check if createAsn is true, if so create Asn otherwise do not create it.
        if($createAsn == true){
            $result['AsnCreated'] = true;
        }
        
       
        if ($csv) {
            \Yii::$app->response->format = Response::FORMAT_RAW;
            array_unshift($csvrows, 'stock_item_id, product_code,product_name,product_key,po');
            return implode("\n",$csvrows);
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

