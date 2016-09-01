<?php
namespace frontend\controllers;

use common\models\StockActivity;
use common\models\StockItem;
use Yii;
use Url;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\CreditLevel ;

use yii\web\HttpException;

use frontend\models\RegisterForm;

/**
 * Site controller
 */
class SrflatController extends SiteController
{

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {            
        if ($action->id == 'sso') {
            Yii::$app->controller->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }    

    /**
     * INDEX
     * =====
     *
     * @return \yii\web\Response
     */
    public function actionIndex()
    {

        if (\Yii::$app->user->isGuest) {
            return $this->redirect('/site/index');
        }

        if (Yii::$app->session->get('internal_user') && !  Yii::$app->session->get('current_account')) {
            return $this->redirect('dashboard') ;
        }

        return $this->listOrders();
    }



    private function listOrders()
    {
        $this->getUserDetails();
        $orders = $this->ordertable();

        StockActivity::log('Request for stockroom details', $this->stockroomId);

        $cLevel = new CreditLevel($this->user) ;

        $bodyContent = $this->renderPartial('/yiicomp/stockroom-flat/ordertable-html', [
            'title'        => 'Orders',
            'dataProvider' => $orders['orderDetails']['provider'],
            'searchModel'  => $orders['orderDetails']['model'],
            'canBuy'       => $orders['accountStatus'] == \common\models\Customer::STATUS_TRANSACTIONAL,
            'credit'       => $cLevel->readCurrentCredit()
        ]);

        return $this->render('/site/customerHome', [
//            'model' => $model,
//            'registermodel' => $registermodel
            'orders'      => $orders,
            'bodyContent' => $bodyContent
        ]);


    }

    /**
     * GET CURRENT CREDIT
     * ==================
     * This returns an array of the credit limit and balance, adjusted to allow
     * for all known items not included in the main balance.
     *
     *
     * @return array
     */
    private function getCurrentCredit()
    {
        $account = $this->user->account;
        $credit  = $account->credit;

        $result = [
            'limit'   => $credit ? $credit->credit_limit : 0,
            'balance' => $credit ? $credit->credit_balance : 0,
        ];

        return $result ;
    }

    public function actionSummary()
    {
        $this->getUserDetails();
        $orders = $this->ordertable(true);

        $cLevel = new CreditLevel($this->user) ;

        StockActivity::log('Request for stockroom summary ', $this->stockroomId);

        $bodyContent = $this->renderPartial('/yiicomp/stockroom-flat/ordersummary-html', [
            'title'        => 'Stock Summary',
            'dataProvider' => $orders['orderDetails']['provider'],
            'searchModel'  => $orders['orderDetails']['model'],
            'canBuy'       => $orders['accountStatus'] == \common\models\Customer::STATUS_TRANSACTIONAL,
            'credit'       => $cLevel->readCurrentCredit()
        ]);

        return $this->render('/site/customerHome', [
            'orders'      => $orders,
            'bodyContent' => $bodyContent
        ]);
    }


    /**
     * ORDER TABLE
     * ===========
     * This actually returns the stock items
     *
     * @return string
     */
    public function ordertable($grouped = false)
    {

        $sHandler = new StockHandler ($this);

        $stockRooms = $this->user->getStockroomDetails();

        $results                  = $sHandler->getOrdertable($this->user->stockrooms[0]->id, $grouped);
        $results['accountStatus'] = $stockRooms['accountStatus'];

        return $results;

    }
    
    /**
     * Possible entry point.
     * Allows Hybris Website to pass in details of a logged in account
     * thus implementing Single Sign On (SSO)
     * 
     * @return string
     * @throws HttpException
     */
    public function actionSso() {
        $session = Yii::$app->session;
        $session->open();
        
        if(Yii::$app->request->isPost){
            // This an incoming connection
            // An external platform is sending us encrypted credentials
            $detailsInPOST = Yii::$app->request->post('details');
            if(!empty($detailsInPOST)){
                Yii::info(__METHOD__.': detailsInPOST = '.$detailsInPOST);
                //echo Yii::$app->request->post('details').'<hr>';
                //die();
                
                // $detailsInPOST should be Base64 encoded
                // It's happy it's got +'s in
                $detailsJSON = $this->_decrypt2($detailsInPOST);
                
                // RCH 20160118
                // Seems that using FireFox RESTClient and POSTing from a form changes the format of 'details'
                // If the above fails, try again
                if(empty($detailsJSON) || json_decode($detailsJSON)==NULL ){
                   $detailsInPOST1 = urldecode($detailsInPOST);
                   Yii::info(__METHOD__.': detailsInPOST (2)= '.$detailsInPOST1);
                   $detailsJSON = $this->_decrypt2($detailsInPOST1); 
                }
                if(empty($detailsJSON) || json_decode($detailsJSON)==NULL ){
                   $detailsInPOST2 = urlencode($detailsInPOST);
                   Yii::info(__METHOD__.': detailsInPOST (3)= '.$detailsInPOST2);
                   $detailsJSON = $this->_decrypt2($detailsInPOST2); 
                }
                
                //die($detailsJSON);
                if(!empty($detailsJSON)){
                    Yii::info(__METHOD__.': detailsJSON = '.$detailsJSON);
                    $detailsObj = json_decode($detailsJSON);
                    Yii::info(__METHOD__.': detailsObj = '.print_r($detailsObj,true));
                    
                    // RCH 20160203
                    // cleanse the username
                    $detailsObj->username = preg_replace('/[^\w]/','_',$detailsObj->username);
                    
                    
                    if ($debugoutput = $this->_handleSSODebug($detailsObj)) {
                        return $debugoutput; // output debug and then exit
                    }
                                        
                    // Find a matching account
                    $account = \common\models\Account::find()->where(['customer_exertis_account_number'=>$detailsObj->account])->one();
                    
                    //Create session
                    $session->set('ssoLogin', 'true');
                    
                    if (empty($account)) {
                        // Go ahead and create account and user anyway (assuming we know the account)
                        $customerExists = \common\models\Customer::find()->where(['exertis_account_number'=>$detailsObj->account])->exists();
                        if ($customerExists) {
                            // This is a known customer, so we can create an account
                            // We'll use a handy function already provided in OrderFileParser
                            try {
                                $ofp = new \console\components\OrderFeedFile\OrderFileParser();
                                $ofp->createCustomerAccountAndStockRoom($detailsObj->account, false); // false = don't send email
                                $account = \common\models\Account::find()->where(['customer_exertis_account_number'=>$detailsObj->account])->one();
                            } catch (yii\base\Exception $e) {
                                throw new \yii\web\HttpException(403, $e->getMessage());
                            }                            
                            
                            // Now create the first user on this account
                            $user = $this->_createUserOnAccount( $detailsObj, $account);   
                            
                            
                            // And login
                            return $this->_SSOLoginAndRedirect($user, $detailsObj->username);
                                    
                        } else {
                            throw new \yii\web\HttpException(403, 'Account unknown (code 194)');                            
                        }
                    } else {
                        // We know this account, do we know the user?
                        $user = $account->getUsers()
                                    ->where(['=', 'email', $detailsObj->emailaddress])
                                    ->one();
                        if (!empty($user)) {
                            // We have an account, and a user. We're okay to login
                            return $this->_SSOLoginAndRedirect($user, $detailsObj->username);                                                        
                        } else {
                            // We have an account, but no user, create one, and login                          
                            $user = $this->_createUserOnAccount($detailsObj, $account);                            
                            return $this->_SSOLoginAndRedirect($user, $detailsObj->username);                            
                        }
                    }
                }
            } else {
                Yii::info(__METHOD__.': Empty details. POST = '.print_r(Yii::$app->request->post('details'),true));
                throw new HttpException(403, 'Not authroized! (241)');
            }
      
        } else {
            throw new HttpException(403, 'Not authorized! (245)');
        }
                
    } 

    private function _handleSSODebug($detailsObj) {

        Yii::info(__METHOD__.': '.print_r($detailsObj, true));
        //$full_name = $detailsObj->firstname . " " . $detailsObj->lastname;

        if ((!empty($detailsObj->debug) && $detailsObj->debug == 1) && Yii::$app->request->post('showurl', 0) == 0){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $detailsObj;
            
        }
        
        if(Yii::$app->request->post('showurl') == 1){
            return 'details=' . urlencode(Yii::$app->request->post('details'));
        }
        
        return false; // no debug output required
        
    }
    
    /**
     * 
     * @param string $str Previousoly encrypted, base64 encoded string
     * @return string Decrypted string
     */
    private function _decrypt2($str){
        
        //die($str);
        $str = base64_decode($str);
        //die($str);

        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, "", MCRYPT_MODE_ECB, "");
        
        if ($td) {
            
            //$ivSz = mcrypt_enc_get_iv_size ( $td );
            //die('sz='.$ivSz);
            
            //die('sz='.mcrypt_enc_get_key_size($td));
            $key = substr(\Yii::$app->params['security.ENCRYPTION_KEY'], 0, mcrypt_enc_get_key_size($td));
            //die('$key='.$key);
            //die('key size = '.strlen($key) * 8);
            //die('iv size = '.strlen(self::IVSTR) * 8);

            mcrypt_generic_init($td, $key, substr(\Yii::$app->params['security.IVSTR'], 0, mcrypt_enc_get_key_size($td)));
            //$decrypted = trim(utf8_encode(mdecrypt_generic($td, $str)), " \t\n\r\0\x02\x06\x07\x0A\x0B"); // was seeing /BELs (0x07) and ACKs on the end
            $decrypted = preg_replace('/[\x00-\x1F\x80-\xFF]+$/', '', utf8_encode(mdecrypt_generic($td, $str))); // was seeing /BELs (0x07) and ACKs on the end

            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);


            //die('decrypted='.$decrypted);
            //die('Decrypted=???'.$decrypted.'???');

            
            return $decrypted;
        } else {
            die('mcrypt_module_open failed');
        }

    }
    
    /**
     * Creates User and attaches it to an account
     * @param type $detailsObj
     * @param Account $account
     * @return GAUser 
     */
    private function _createUserOnAccount($detailsObj, $account) {
        // Check if the account already has a user, so we know if
        // we need to create the first main user, or a subuser
        $role = (empty($account->users)) ? \common\models\EDSRRole::ROLE_MAINUSER : \common\models\EDSRRole::ROLE_SUBUSER;                             

                            
        $user = Yii::$app->getModule("user")->model("User");
        $user->setScenario('ldap'); // relax password strength
        $user->username = $detailsObj->username;
        $user->role_id = $role;
        $user->newPassword = 'not sure what to put in here';
        $user->newPasswordConfirm = $user->newPassword;
        $user->email = $detailsObj->emailaddress;
        $user->status = \common\models\gauth\GAUser::STATUS_ACTIVE; // Enable by default (no need for verification emails etc)
        $user->account_id = $account->id; // and tie it to the account
        $user->create_time = date("Y-m-d H:i:s");
        if (!$user->save()) {
            Yii::error(__METHOD__.': Failed to save new user: '. print_r($user->getErrors(),true));
        }
        return $user;
    }
    

    /**
     * Performs a login (based on SSO from Hybris
     * Redirects to the first page
     * 
     * @param GAUser $user
     * @param string $username
     * @return type
     * @throws Exception
     */
    private function _SSOLoginAndRedirect($user, $username) {
        // we have a user, if they're a normal user, login as them
        if ($user->can('customer')) {
            $identity = \common\models\gauth\GAUser::findOne(['username' => $username]);
            if(!Yii::$app->user->login($identity)) {
                throw new Exception('Login Failed');
            } else {
                // We're logged in based on details passed in (e.g. from Hybris)
                return $this->redirect('/shop/index');
            }
        } 
    }
}
