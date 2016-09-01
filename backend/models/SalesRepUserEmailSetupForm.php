<?php

namespace backend\models;

use Yii;
use yii\base\Model;

use common\models\Account;
use common\models\gauth\GAUser;

/**
 * This is the model for the Form SalesRepUserEmailSetupForm
 * There is no corresponding table.
 *
 * @property string $exertis_account_number Account Number e.g. EC012345
 * @property string $emailaddress Email Address
 * @property string $emailaddress_repeat Verifies email address
 *
 */


class SalesRepUserEmailSetupForm extends Model
{

    public $exertis_account_number;
    public $emailaddress;
    public $emailaddress_repeat;
    public $xbox;
    public $accountFound;
    
    // RCH 20160205
    // see https://trello.com/c/ugl9kIUW
    public $edi_rep; // Capture EDI rep so we can make sure account is setup in Oracle.

    public $errors;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['exertis_account_number', 'emailaddress', 'emailaddress_repeat'], 'required'],
            [['emailaddress', 'emailaddress_repeat'], 'trim'],
            [['emailaddress', 'emailaddress_repeat'], 'email'],
            ['exertis_account_number', 'isExertisCustomer'],
            [['edi_rep', 'accountFound', 'xbox'], 'safe'],
            ['edi_rep', 'string', 'min'=>5, 'when' => function($model){
                return Yii::$app->creditDb->createCommand('SELECT * FROM icom_buyer_t b JOIN icom_customer_t c ON c.coExternal=b.coExternal WHERE c.coExternal="'.$model->exertis_account_number.'" AND b.lbFirstName!="To Define" AND b.lbLastName!="To Define"')->queryOne() == false;
            },
            'whenClient' => 'function(attr, val){
                return $("#accFound").val()==0
            }'],
            ['edi_rep', 'required', 'when' => function($model){
                return Yii::$app->creditDb->createCommand('SELECT * FROM icom_buyer_t b JOIN icom_customer_t c ON c.coExternal=b.coExternal WHERE c.coExternal="'.$model->exertis_account_number.'" AND b.lbFirstName!="To Define" AND b.lbLastName!="To Define"')->queryOne() == false;
            },
            'whenClient' => 'function(attr, val){
                return $("#accFound").val()==0
            }'],
          
            // validates if the value of "email_address" attribute equals to that of "email_address_repeat"
            // see http://www.yiiframework.com/doc-2.0/guide-tutorial-core-validators.html#compare
            ['emailaddress', 'compare'],
          ];
    }


  /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'exertis_account_number' => Yii::t('app', 'Exertis Account No.'),
            'emailaddress' => Yii::t('app', 'Email Address'),
            'emailaddress_repeat' => Yii::t('app', 'Email Address again'),
            'edi_rep' => Yii::t('app', 'EDI Rep'),
            'xbox' => Yii::t('app', 'Add Xbox Rule'),
        ];
    }


    /**
     * update the user's email address related to this account.
     * This should only be done when the Account has one user, and the user's email is NULL or end with @dummy.com
     * @return User|boolean User if Email was setup in the user correctly, boolean FALSE if there was a problem
     */
    public function setupEmail()
    {
        $this->errors = array();
        
        
        if(empty($this->edi_rep)){
            $enableShop = 1;
        } else {
            $enableShop = 0;
        }

        // strip off Compnay name and leave us with just the account number

        $accountPreprocessed = explode('||',$this->exertis_account_number);
        $accountPreprocessed = trim($accountPreprocessed[0]);
        $this->exertis_account_number = $accountPreprocessed;
        
        if ($this->validate()) {
            $account = Account::findOne(['customer_exertis_account_number'=>$this->exertis_account_number]);
            // RCH 20150325
            if (empty($account)) {

                // The selected Customer does not have an EDSR account - so build one
                $account = new Account();
                $account->createNewAccount($this->exertis_account_number);
                $account->saveWithAuditTrail(__METHOD__.': Created by Sales Rep entering email before order arrived');
                
                //Add Rule for Account
                $accountRule = new AccountRuleMapping();
                $accountRule->account_id = $account->id;
                
                if($this->xbox){
                    $accountRule->account_rule_id = '32,8';
                } else {
                    $accountRule->account_rule_id = '32';
                }
                
                $accountRule->assigned = date('Y-m-d H:i:s');
                $accountRule->save();

                //create the first stockroom
                $stockroom = new \common\models\Stockroom();
                $stockroom->createNewStockRoom($account->id);

            }
            
            //Change rule for account
            $accountRule = AccountRuleMapping::findOne(['account_id'=>$account->id]);

            if($this->xbox){
                $accountRule->account_rule_id = '32,8';
            } else {
                $accountRule->account_rule_id = '32';
            }
            $accountRule->save();

            $numUsersOnAccount = GAUser::find()->where(['account_id'=>$account->id])->count();
            if (count($numUsersOnAccount)==0) {
            	$this->errors[] = 'Not enough users on the account. There should be 1';
                return FALSE; // no user to set!
            } elseif (count($numUsersOnAccount)>1) {
            	$this->errors[] = 'Too many users on the account. There should be only 1';
                return FALSE; // too many users, what's going on?!
            } else {
                // check if user already has an email address
                $user = GAUser::findOne(['account_id'=>$account->id]);
                if(!$user){
                    return FALSE;
                }
                
                $user->setScenario('emailsetup');
                if (empty($user->email) || strpos($user->email,'@dummy.com')!==FALSE) {
                    $user->email = $this->emailaddress;
                    $user->shopEnabled = $enableShop;
                    if ($user->save())
                        // Success!
                        return $user;
                    else {
                        $this->errors[] = $user->getErrors();
                        return FALSE;
                    }
                } else {
                    $this->errors[] = 'User already has an email address. No need to set it.';
                    return FALSE;
                }

            }
        } else {
        	$this->errors[] = print_r($this->errors,true);
            return FALSE;
        }
    }



    public function isExertisCustomer($attribute, $params) {
        $result = \common\models\Customer::find()
                ->where(['exertis_account_number' => $this->$attribute])
                ->one();
        if (empty($result)) {
            $this->addError($attribute, 'Customer is unknown ' . $this->$attribute);
        }
    } // isExertisCustomer


}
