<?php

namespace backend\controllers;

use Yii;
use common\models\Account;
use common\models\Customer;
use backend\models\SalesRepUserEmailSetupForm;
use yii\helpers\ArrayHelper;

use common\models\gauth\GAUser;
use common\models\gauth\GAUserKey;

use yii\filters\VerbFilter;

use yii\filters\AccessControl;


class SalesRepAccountEmailFormController extends \yii\web\Controller {

	public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],

			 'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['enableshop'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => Yii::$app->user->can("setupuseremail"),
                        'actions' => ['index','index-new','fetch-customer-details','accounts-to-set-up-list', 'ajaxfindaccount', 'ajaxgetrepids'],
                        'roles' => ['@'],
                    ],
                ],
            ],

        ];
    }

/*
    public function actionIndex() {
        $model = new SalesRepUserEmailSetupForm();
        $account_typeahead_data = ArrayHelper::getColumn(
                        //Account::find()->select('customer_exertis_account_number')->waitingForEmailSetup()->asArray()->all(), 'customer_exertis_account_number'
                        Account::find()->select(['customer_exertis_account_number', 'customer.name'])->joinWith('customer')->waitingForEmailSetup()->all(),
                            function ($element) {
                                    return $element['customer_exertis_account_number'].' || '.$element->customer['name'];
                                }

                                );

        // Check if n optional parameter has been passed in, probably because
        // of a link in the Account Setup Email
        $getId = Yii::$app->request->get('id');
        if ($getId) {
            $account = Account::find()->waitingForEmailSetup($getId)->one();
            if ($account) {
                //die($account->customer_exertis_account_number);
                $customer = Customer::findOne(['exertis_account_number'=>$account->customer_exertis_account_number]);
                //die($customer->attributes);
                // pre-set the account so the user doesn't have to
                $model->exertis_account_number = $account->customer_exertis_account_number.' || '.$customer->name;
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            // Handle the POSTed form
            //
            //$model->validate(); // called here mainly to set teh defaults
            $result = $model->setupEmail();
            if ($result!==FALSE) {
                $user = $result;
				$keyExpiryDate = $this->_addBusinessDays(date("Y-m-d"), 1);
				$keyExpiryDate = date('Y-m-d H:i:s', strtotime($keyExpiryDate.'20:00:00'));
                GAUserKey::generate($user->id, GAUserKey::TYPE_EMAIL_ACTIVATE, $keyExpiryDate); // link expires in 24hrs
                if (!$numSent = $user->sendEmailConfirmation($user->userKeys[0])) {
	                Yii::$app->getSession()->setFlash('error', 'First User for ' . $model->exertis_account_number . ' but there was a problem sending email to ' . $user->email);
				} else {
	                Yii::$app->getSession()->setFlash('success', 'First User for ' . $model->exertis_account_number . ' has been setup and an email has been sent to ' . $user->email);
				}
            } else {
                Yii::$app->getSession()->setFlash('error', 'There was an error setting up ' . $model->exertis_account_number);
                Yii::$app->getSession()->addFlash('error', \yii\helpers\VarDumper::dump($model->errors,10,true));
            }
        }
        // display the form

        return $this->render('index', [
                    'model' => $model,
                    'account_typeahead_data' => $account_typeahead_data,
                        ]
        );
    } // actionIndex

*/



    /**
     * Called via AJAX for the TypeAhead / suggestion
     * @param type $q
     */
    public function actionAccountsToSetUpList($q = null) {
        $query = new \yii\db\Query();
        
        $query->select(['name','exertis_account_number','c.status'])
            ->from('customer c')
            ->join('LEFT JOIN', 'account', 'account.customer_exertis_account_number=exertis_account_number')
            ->join('LEFT JOIN', 'user', 'user.account_id=account.id')
            ->where('c.exertis_account_number LIKE "%'.$q.'%" AND (account.id is null OR (user.email is null OR user.email LIKE "%dummy%"))')
            ->orderBy('c.exertis_account_number');
        $command = $query->createCommand();
        $data = $command->queryAll();
        $out = [];
        foreach ($data as $d) {
            if ($d['status'] != Customer::STATUS_TRANSACTIONAL) {
                $out[] = ['value' => ''.$d['exertis_account_number'].' || '.$d['name'].' !! WARNING: BAD ACCOUNT STATUS: '.$d['status']];                
            } else {
                $out[] = ['value' => $d['exertis_account_number'].' || '.$d['name']];
            }
        }
        
        if(empty($out)){
            $out[] = ['value' => "Account already created."];
        }
        
        $json = \yii\helpers\Json::encode($out);
        echo $json;
    }
    
    
    /**
     * Finance will click on a link in the email sent by EDSR earlier.
     * They do this after they have checked that the account is EDI Ready in Oracle
     * 
     * This link will enable the shop.
     * 
     * @param string $key Key on link ing Email
     * @return string HTML Content for the view
     */
    public function actionEnableshop($key){
        
        $key = json_decode($this->_cryptDecryptStr('decrypt', $key));
       
        //$user = new GAUser();
        $returnedUser = GAUser::find()->where(['email'=>$key->userEmail])->one();
        
        
        if($returnedUser){
        
            if($returnedUser->shopEnabled){
                $message = 'Shop is already enabled for user '.$returnedUser->email.' (ID:'.$returnedUser->id.').';
                return $this->render('shopactivated', ['message'=>$message]);
            } else {
                $returnedUser->shopEnabled = true;
                $returnedUser->setScenario('emailsetup'); // RCH 20160428
                if($returnedUser->saveWithAuditTrail(__METHOD__.': Shop Enabled by Finance')) {
        //die(print_r($returnedUser->attributes,true));
                
                    $message = 'Shop has been enabled for user '.$returnedUser->email.' (ID:'.$returnedUser->id.').';
                    return $this->render('shopactivated', ['message'=>$message]);
                } else {
                    $message = 'There\'s been a problem enabling the shop for user '.$returnedUser->email.' (ID:'.$returnedUser->id.'). Error = '.print_r($returnedUser->getErrors(),true);
                    // RCH 20160428 - Consider using \Yii::$app->session->setFlash() to display the message
                    return $this->render('shopactivated', ['message'=>$message]);                    
                }
            }
            
        } else {
            echo 'User not found.';
        }
        
    }
    
    public function actionAjaxfindaccount($accountNo){
        
        $findAccount = count(Yii::$app->creditDb->createCommand('SELECT * FROM icom_buyer_t b JOIN icom_customer_t c ON c.coExternal=b.coExternal WHERE c.coExternal="'.$accountNo.'" AND b.lbFirstName!="To Define" AND b.lbLastName!="To Define"')->queryAll());
        
        echo $findAccount;
        
        
    }
    
    public function actionAjaxgetrepids($accountNo){
        $reps = Yii::$app->creditDb->createCommand('SELECT account_number, first_name, last_name, sales_rep_id, focus_business_area FROM rep_t WHERE account_number="'.$accountNo.'"')->queryAll();
        
        $table = '<p>EDI Rep IDs</p>';
        
        $table = '<table class="table-hover" width="100%" align="center" style="text-align:center">
            <tr class="thead">
                <td>Name</td>
                <td>Business area</td>
                <td>REP ID</td>
                <td>Button</td>
            </tr>';
        
        foreach($reps as $rep){
            $full_name = $rep['first_name'].' '.$rep['last_name'];
            $table .= '<tr>
                <td>'.$full_name.'</td>
                <td>'.$rep['focus_business_area'].'</td>
                <td>'.$rep['sales_rep_id'].'</td>
                <td><button type="button" class="btn btn-primary btn-small add-rep-id" onclick=\'addId("'.$full_name.'")\'><i class="glyphicon glyphicon-plus"></i></button></td>
            </tr>';
        }
        
        $table .= '<tr>
            <td>Wesley Boyes</td>
            <td>PC, Print, AV and Consumables</td>
            <td>100000515</td>
            <td><button type="button" class="btn btn-primary btn-small add-rep-id" onclick=\'addId("Wesley Boyes")\'><i class="glyphicon glyphicon-plus"></i></button></td>
        </tr>';
        
        $table .= '<tr>
            <td>Daniel Izzo</td>
            <td>PC, Print, AV and Consumables</td>
            <td>100006174</td>
            <td><button type="button" class="btn btn-primary btn-small add-rep-id" onclick=\'addId("Daniel Izzo")\'><i class="glyphicon glyphicon-plus"></i></button></td>
        </tr>';
        
        $table .= '</table>';
        
        echo $table;
    }

    public function actionIndex() {
        $model = new SalesRepUserEmailSetupForm();
        /*$customer_typeahead_data = ArrayHelper::getColumn(
                        //Account::find()->select('customer_exertis_account_number')->waitingForEmailSetup()->asArray()->all(), 'customer_exertis_account_number'
                        Customer::find()->select(['exertis_account_number', 'customer.name'])->waitingForEmailSetupOrNoAccount()->all(),
                            function ($element) {
                                    return $element['customer_exertis_account_number'].' || '.$element->customer['name'];
                                }

                                );*/
        
        // Check if an optional parameter has been passed in, probably because
        // of a link in the Account Setup Email
        $getId = Yii::$app->request->get('id');
        if ($getId) {
            $account = Account::find()->waitingForEmailSetup($getId)->one();
            if ($account) {
                //die($account->customer_exertis_account_number);
                $customer = Customer::findOne(['exertis_account_number'=>$account->customer_exertis_account_number]);

                //die($customer->attributes);
                // pre-set the account so the user doesn't have to
                $model->exertis_account_number = $account->customer_exertis_account_number.' || '.$customer->name;
            }
        }
        
        
        // Handle the Form POST but do some checks first
        if ($model->load(Yii::$app->request->post())) {
            
            $accountNumber = explode(' ', $model->exertis_account_number)[0];
                        
            // Handle the POSTed form
            //
            //$model->validate(); // called here mainly to set the defaults
            $result = $model->setupEmail();

            // ---------------------------------------------------------------
            // $result is the user object if a new account was setup, else false
            // ---------------------------------------------------------------

            if ($result!==FALSE) {
                \Yii::trace('Part 2');
                $this->sendInvitationEmail($result, $model->exertis_account_number ) ;
                
                if($model->accountFound == 0){
                    //Setting up and sending email to Lisa Bailey
                    $crypted = $this->_cryptDecryptStr('encrypt',json_encode(['userEmail'=>$model->emailaddress]));
                    $url = 'http://edsr.exertis.co.uk/sales-rep-account-email-form/enableshop?key='.$crypted;

                    $this->_sendEmailToFinance($url, $model);
                }
                    
            } elseif (!$this->resendInvitationEmails($model->exertis_account_number)) {
                Yii::$app->getSession()->setFlash('error', 'There was an error setting up ' . $model->exertis_account_number);
                Yii::$app->getSession()->addFlash('error', \yii\helpers\VarDumper::dump($model->errors,10,true));
            }
        }
        // display the form

        return $this->render('index', [
                    'model' => $model,
                    //'account_typeahead_data' => $customer_typeahead_data,
                        ]
        );
    } // actionIndex

    /**
     * SEND EMAIL TO FINANCE
     * =====================
     * This sends the finance an email with a link.
     * The link takes them to a page to enable the shop for the user
     * 
     * @param $url
     * @param $accountSetupDetails
     *
     */
    private function _sendEmailToFinance($url, SalesRepUserEmailSetupForm $accountSetupDetails){
        
        $account = Account::find()->where(['customer_exertis_account_number'=>$accountSetupDetails->exertis_account_number])->one();
        $account->saveWithAuditTrail(__METHOD__.': Sending EDI Setup Check email to '.Yii::$app->params['financeEDIEmail']);
        
        return \Yii::$app->mailer->compose('shopActivation', ['url' => $url, 'accountSetupDetails'=>$accountSetupDetails])
                ->setFrom('webteam@exertis.co.uk')
                ->setTo(Yii::$app->params['financeEDIEmail'])
                ->setSubject('EDSR: Is account EDI Ready in Oracle? '.$accountSetupDetails->exertis_account_number)
                ->setBcc(Yii::$app->params['account.copyAllEmailsTo'])// RCH 20160413
                ->send();
    }
    
    
    
    /**
     * SEND INVITATION EMAIL
     * =====================
     * This send the user an email with a link.
     * The link takes them to a page to set their password and agree to T&Cs
     * 
     * @param $user
     * @param $accountNumber
     *
     * @return bool
     */
    private function sendInvitationEmail ($user, $accountNumber) {

        $result = false ;

        $keyExpiryDate = $this->_addBusinessDays(date("Y-m-d"), 1);
        $keyExpiryDate = date('Y-m-d H:i:s', strtotime($keyExpiryDate . '20:00:00'));

        GAUserKey::generate($user->id, GAUserKey::TYPE_EMAIL_ACTIVATE, $keyExpiryDate); // link expires in 24hrs
        
        if (!$numSent = $user->sendEmailConfirmation($user->userKeys[0])) {
            Yii::$app->getSession()->setFlash('error', 'First User for ' . $accountNumber . ' but there was a problem sending email to ' . $user->email);

        } else {
            
            $returnMsg = 'First User for ' . $accountNumber . ' has been setup and an email has been sent to ' . $user->email . '.';
            
            $findAccount = count(Yii::$app->creditDb->createCommand('SELECT * FROM icom_buyer_t b JOIN icom_customer_t c ON c.coExternal=b.coExternal WHERE c.coExternal="'.$accountNumber.'" AND b.lbFirstName!="To Define" AND b.lbLastName!="To Define"')->queryAll());
            
            if(!$findAccount){
                $returnMsg .= '<br><br><i>Email has been sent to finance for EDI setup, shop will be disabled initially.</i>';
            }
            
            
            Yii::$app->getSession()->setFlash('success', $returnMsg);
            $result = true ;
        }
        return $result ;
    }

    /**
     * RESEND INVITATION EMAILS
     * ========================
     * This is called when a new product is added and the user had already
     * been created. It checks the users on the provided account for any
     * where their personal account hasn't been confirmed and for each
     * re-sends the invitation email.
     *
     * @param $exertisAccountNumber
     *
     * @return bool
     */
    private function resendInvitationEmails($exertisAccountNumber) {
        $result = false ;

        if ($exertisAccountNumber) {
            $account = Account::findOne(['customer_exertis_account_number' => $exertisAccountNumber]);

            if ($account) {
                $users = GAUser::find()->where(['account_id' => $account->id])->all();

                if (is_array($users) && count($users)) {
                    foreach ($users as $user) {
                        $userKeys  = $user->userKeys;
                        $confirmed = false;

                        foreach ($userKeys as $userKey) {
                            if ($userKey->consume_time) {
                                $confirmed = true;
                                print_r($userKeys[0]->toArray());exit;
                                break;
                            }
                        }
                        if (!$confirmed) {
                            $result = $this->sendInvitationEmail($user, $exertisAccountNumber);
                        }
                    }
                }
            } else {
                \Yii::trace('Part 5');
            }
        }
        return $result ;
    }


	# $date must be in YYYY-MM-DD format
	# You can pass in either an array of holidays in YYYYMMDD format
	# OR a URL for a .ics file containing holidays
	# this defaults to the UK government holiday data for England and Wales
	private function _addBusinessDays($date,$numDays=1,$holidays='') {
	    if ($holidays==='') $holidays = 'https://www.gov.uk/bank-holidays/england-and-wales.ics';

	    if (!is_array($holidays)) {
	        $ch = curl_init($holidays);
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	        $ics = curl_exec($ch);
	        curl_close($ch);
	        $ics = explode("\n",$ics);
	        $ics = preg_grep('/^DTSTART;/',$ics);
	        $holidays = preg_replace('/^DTSTART;VALUE=DATE:(\\d{4})(\\d{2})(\\d{2}).*/s','$1-$2-$3',$ics);
	    }

	    $addDay = 0;
	    while ($numDays--) {
	        while (true) {
	            $addDay++;
	            $newDate = date('Y-m-d', strtotime("$date +$addDay Days"));
	            $newDayOfWeek = date('w', strtotime($newDate));
	            if ( $newDayOfWeek>0 && $newDayOfWeek<6 && !in_array($newDate,$holidays)) break;
	        }
	    }

	    return $newDate;
	} // _addBusinessDays

        
        
        private function _cryptDecryptStr($action, $encrypt){
                $encrypt_method = "AES-256-CBC";
                $secret_key = 'E715224B95AFB14D';
                $secret_iv = 'E715224B95AFB14D';

                // hash
                $key = hash('sha256', $secret_key);

                // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
                $iv = substr(hash('sha256', $secret_iv), 0, 16);

                if( $action == 'encrypt' ) {
                    $output = openssl_encrypt($encrypt, $encrypt_method, $key, 0, $iv);
                    $output = base64_encode($output);
                }
                else if( $action == 'decrypt' ){
                    $output = openssl_decrypt(base64_decode($encrypt), $encrypt_method, $key, 0, $iv);
                }

                return $output;
        }


}
