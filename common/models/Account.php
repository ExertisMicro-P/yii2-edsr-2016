<?php

namespace common\models;

use Yii;

use common\models\gauth\GAUser;
use common\models\AccountQuery;
use common\models\EDSRRole;
use console\components\FileFeedErrorCodes;
use console\components\AccountSetupException;
use exertis\savewithaudittrail\SaveWithAuditTrailBehavior;
use exertis\savewithaudittrail\models\Audittrail;

use yii\helpers\Url;

/**
 * This is the model class for table "account".
 *
 * @property integer  $id
 * @property string   $eztorm_user_id
 * @property string   $customer_exertis_account_number
 * @property string   $timestamp
 * @property string   $logo           Filename of the logo
 * @property boolean  $include_key_in_email
 * @property boolean  $use_retail_view
 * @property boolean  $dont_raise_sop TRUE to avoid raising an SOP when purchasiGng with EDSR2 or EDSR Digital SFE
 *
 * @property Customer $customerExertisAccountNumber
 * @property Customer $customer
 *
 */
class Account extends \yii\db\ActiveRecord {
    /**
     * @var mixed image the attribute for rendering the file input
     * widget for upload on the form
     */
    public $image;


    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'account';
    }

    /**
     * @inheritdoc
     * @return AccountQuery
     */
    public static function find() {
        return new AccountQuery(get_called_class());
    }

    public function behaviors() {
        return [
            [
                'class'     => SaveWithAuditTrailBehavior::className(),
                'userClass' => '\common\models\gauth\GAUser',
            ],
            //Taggable::className(),

        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['timestamp', 'image', 'eztorm_user_id', 'include_key_in_email', 'customer_exertis_account_number', 'use_retail_view', 'dont_raise_sop'], 'safe'],
            [['image'], 'file', 'extensions' => 'jpg, gif, png'],

            [['eztorm_user_id'], 'string', 'max' => 45],
            [['include_key_in_email'], 'boolean'],
            [['use_retail_view', 'dont_raise_sop'], 'boolean'],

            [['customer_exertis_account_number'], 'string', 'max' => 20],
            [['customer_exertis_account_number'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'             => \Yii::t('app', 'ID'),
            'eztorm_user_id' => \Yii::t('app', 'eZtorm User ID'),

            'customer_exertis_account_number' => \Yii::t('app', 'Exertis Account Number'),
            'timestamp'                       => \Yii::t('app', 'Timestamp'),
            'include_key_in_email'            => \Yii::t('app', 'Incl. Key in Email'),
            'use_retail_view'                 => \Yii::t('app', 'Use Retail View'),
            'dont_raise_sop'                  => \Yii::t('app', 'Don\'t raise SOP'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerExertisAccountNumber() {
        return $this->hasOne(Customer::className(), ['exertis_account_number' => 'customer_exertis_account_number']);
    }

    public function createNewAccount($account_number) {

        $this->customer_exertis_account_number = $account_number;
        //we don't have an more details than this at this time.
        //later we will add ezorm_user_id.
        if (!$this->saveWithAuditTrail('Created account ' . $account_number)) {
            Yii::error(__METHOD__ . 'Account could not be created ' . print_r($this->getErrors(), true));
            throw new AccountSetupException(FileFeedErrorCodes::ACCOUNT_SAVE_FAILED,
                                            print_r($this->getErrors(), true));
        }
        //create dummy user
        $user = new GAUser();
        $user->setScenario('emailsetup'); // allows us to create a user without a password
        $user->status         = GAUser::STATUS_INACTIVE;
        $user->role_id        = EDSRRole::ROLE_MAINUSER;
        $user->spending_limit = 50000;

        $user->username   = $account_number;
        $user->account_id = $this->id;
        $user->email      = uniqid() . '@dummy.com'; // @todo not sure why I need to do this

        if (!$user->saveWithAuditTrail('Created user for account ' . $account_number)) {
            Yii::error(__METHOD__ . 'User can not be created for acc ' . $account_number . ' ' . print_r($user->getErrors(), true));
            throw new AccountSetupException(FileFeedErrorCodes::USER_SAVE_FAILED,
                                            print_r($user->getErrors(), true));
        }
    } // createNewAccount


    /**
     *
     * @return GAUser The main user of the account.
     */
    public function findMainUser() {
        $id = $this->id;
        // RCH 20150402
        // exclude a test account (id=9) which allows me to tie a test user to an account
        // in order to view/debug issues
        $user = GAUser::find()->where('account_id=:id AND role_id=:rid AND id>10', [':id' => $id, ':rid' => EDSRRole::ROLE_MAINUSER])->one();

        return $user;
    }


    /**
     *
     * @return string Email address of main user
     */
    public function getMainUserEmail() {
        $user = $this->findMainUser();

        return $user->email;
    }

    public function getUsers() {
        // Account has_many Users via User.account_id -> id
        return $this->hasMany(GAUser::className(), ['account_id' => 'id']);
    }

    public function getUsersAsArray() {
        return GAUser::find()->where(['account_id' => $this->id])->asArray()->all();
    }

    public function getUserCount() {
        // Account has_many Users via User.account_id -> id
        return $this->hasMany(GAUser::className(), ['account_id' => 'id'])->andOnCondition(['!=', 'user.id', Yii::$app->user->identity->id])->count();
    }

    /**
     * CREDIT
     * ======
     * Links to the credit details, giving the known credit balance and the
     * total credit limit.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCredit() {
        return $this->hasOne(CreditBalance::className(), ['account_number' => 'customer_exertis_account_number']);
    }


    public function getCustomer() {
        // Account is related to to a Customer record (Customer just holds extra data for the Account, like name and address)
        return $this->hasOne(Customer::className(), ['exertis_account_number' => 'customer_exertis_account_number']);
    }

    public function getStockrooms() {
        return $this->hasMany(Stockroom::className(), ['account_id' => 'id']);
    }

    public function getCustomerName() {
        return $this->customer->name;
    }

    public function getStockItemsCount() {
        $stockItemCount = 0;

        $stockrooms = $this->stockrooms;
        if (!empty($stockrooms)) {
            foreach ($stockrooms as $stockroom) {
                $stockItemCount += $stockroom->stockItemsCount;
            }
        }

        return $stockItemCount;
    }



    /*
    public function beforeSave($event) {
        parent::beforeSave($event);

        if (!$this->owner->isNewRecord) {
            //insert new column and add unique user Id.


        }


    }*/


    /**
     * Send email confirmation to Some who can setup the Account's email address
     *
     * @return int
     */
    public function sendAccountCreatedEmail() {
        /** @var Mailer $mailer */
        /** @var Message $message */

        // modify view path to module views
        $mailer           = Yii::$app->mailer;
        $oldViewPath      = $mailer->viewPath;
        $mailer->viewPath = Yii::$app->params['account.emailPath'];

        // send email
        $account    = $this;
        $accountNum = $account->customer_exertis_account_number;
        $customer   = $account->customer;
        //$profile = $user->profile;
        //$email   = $user->new_email !== null ? $user->new_email : $user->email;
        $email   = Yii::$app->params['account.AccountCreatedEmailRecipients']; // who to send the email to (probably sales@exertis.co.uk?)
        $subject = 'Exertis Digital Stock Room: ' . Yii::t("app", "Account") . ' ' . $accountNum . ' ' . $customer->name . ' requires urgent setup';
        $message = $mailer->compose('accountSetupEmail', compact("subject", "account", "customer"))
                          ->setTo($email)
                          ->setBcc(Yii::$app->params['account.copyAllEmailsTo'])// RCH 20150420
                          ->setSubject($subject);

        // check for messageConfig before sending (for backwards-compatible purposes)
        if (empty($mailer->messageConfig["from"])) {
            $message->setFrom(Yii::$app->params["adminEmail"]);
        }
        $result = $message->send();

        // restore view path and return result
        $mailer->viewPath = $oldViewPath;

        return $result;
        //       return true;
    } // sendAccountCreatedEmail


    /**
     * Send email to announce arrival of new stock in stockrooms
     *
     * @return int
     */
    public function sendStockItemCreatedEmail($data) {
        /** @var Mailer $mailer */
        /** @var Message $message */

        \Yii::info(__METHOD__ . ':' . print_r($data, true));

        $account = $this;

        // RCH 20150402
        // Check flag on account to see if we should include keys in the email
        $data['showkeys'] = $account->include_key_in_email;

        if ($account->include_key_in_email) {

//            $result = $this->sendEmailWithAllKeys($data) ;            // The orignal code, all keys in one email
            $result = $this->sendEmailPerPO($data);

            $this->generateCSVfile($data);

        } else {
            $result = $this->sendEmailWithoutKeys($data);
        }

        return $result;

    } // sendAccountCreatedEmail


    /**
     * SEND EMAIL WITH ALL KEYS
     * ========================
     * This is passed the set of codes and creates a single CSV file to send
     * using a single email
     *
     * @param $selectedDetails
     *
     * @return bool
     */
    private function sendEmailWithAllKeys($selectedDetails) {
        $mailerDetails = $this->setViewPathToModuleViews();
        $mailer        = $mailerDetails[0];
        $csvFilename   = 'Codes.csv';
        $result        = true;

        $this->generateCSVfile($selectedDetails);

        if (!$this->sendEmail($mailer, $selectedDetails, $csvFilename)) {
            $result = false;
        }

        $this->restoreViewPath($mailerDetails);

        return $result;
    }

    /**
     * SEND EMAIL WITHOUT KEYS
     * =======================
     *
     * @param $selectedDetails
     *
     * @return bool
     */
    private function sendEmailWithoutKeys($selectedDetails) {
        $mailerDetails = $this->setViewPathToModuleViews();
        $mailer        = $mailerDetails[0];
        $csvFilename   = null;
        $result        = true;

        if (!$this->sendEmail($mailer, $selectedDetails, $csvFilename)) {
            $result = false;
        }
        $this->restoreViewPath($mailerDetails);

        return $result;
    }


    /*
     * Generate CSV File
     * =================
     * Creates a CSV file for the codes, using a single file for all.
     *
     */
    private function generateCSVfile($selectedDetails) {

        $codes = [];

        foreach ($selectedDetails['pos'] as $details) {

            foreach ($details as $detail) {
                $item = Stockitem::findOne($detail['orderdetails']->stock_item_id);
                array_push($codes, $item->key);
            }
        }

        $csvFilename = 'Codes.csv';
        $this->buildCSVFile($codes, $csvFilename);
    }

    /**
     * SEND EMAIL PER PO
     * =================
     * This is passed a set of orders, grouped by purchase order, and generates
     * and emails a CSV file for each purchase order.
     *
     * @param $selectedDetails
     *
     * @return bool
     */
    private function sendEmailPerPO($selectedDetails) {
        $mailerDetails = $this->setViewPathToModuleViews();
        $mailer        = $mailerDetails[0];
        $csvFilename   = 'Codes.csv';
        $showkeys      = $selectedDetails['showkeys'];
        $result        = true;

        foreach ($selectedDetails['pos'] as $poKey => $details) {
            $codes = [];

            foreach ($details as $detail) {
                $item = Stockitem::findOne($detail['orderdetails']->stock_item_id);
                array_push($codes, $item->key);
            }

            $this->buildCSVFile($codes, $csvFilename);

            $data = [
                'pos'      => [$poKey => $details],
                'showkeys' => $showkeys
            ];

            if (!$this->sendEmail($mailer, $data, $csvFilename)) {
                $result = false;
                break;
            }
        }
        $this->restoreViewPath($mailerDetails);

        return $result;
    }

    /**
     * RESTORE VIEW PATH
     * =================
     *
     * @param $mailerDetails
     *
     * @return mixed
     */
    private function restoreViewPath($mailerDetails) {
        $mailer      = $mailerDetails[0];
        $oldViewPath = $mailerDetails[1];

        $mailer->viewPath = $oldViewPath;
    }


    /**
     * SET VIEW PATH TO MODULE VIEWS
     * =============================
     *
     * @return array
     */
    private function setViewPathToModuleViews() {
        $mailer           = Yii::$app->mailer;
        $oldViewPath      = $mailer->viewPath;
        $mailer->viewPath = Yii::$app->params['account.emailPath'];

        return [$mailer, $oldViewPath];
    }

    /**
     * SEND EMAIL
     * =========
     *
     * @param $mailer
     * @param $csvFile
     *
     * @return mixed
     *
     * @var Mailer $mailer
     * @var Message $message
     */
    private function sendEmail($mailer, $selectedDetails, $csvFile) {

        $account    = $this;
        $accountNum = $account->customer_exertis_account_number;
        $customer   = $account->customer;

        $email = $account->findMainUser()->email;

        $subject = 'Exertis Digital Stock Room: ' . Yii::t("app", "Account") . ' ' . $accountNum . ' ' . $customer->name . ' stock item available';
        $message = $mailer->compose('stockItemCreatedEmail', compact("subject", "account", "selectedDetails"))
                          ->setTo($email)
                          ->setBcc(Yii::$app->params['account.copyAllEmailsTo'])// RCH 20150420
                          ->setSubject($subject);
        if ($csvFile) {
            $message->attach($csvFile);
        }

        // RCH 20160321
        // @todo To be completed
        //$this->attachStockItemCreatedSpreadsheet ($message, $data) ;

        // check for messageConfig before sending (for backwards-compatible purposes)
        if (empty($mailer->messageConfig["from"])) {
            $message->setFrom(Yii::$app->params["adminEmail"]);
        }

        return $message->send();
    }


    /**
     * BUILD CSV FILE
     * ==============
     * Creates a CSV file from the passed data.
     *
     * @param $codes
     * @param $csvFilename
     */
    private function buildCSVFile($codes, $csvFilename) {

        $csvFile = "Codes\n";
        foreach ($codes as $code) {
            $csvFile .= $code . "\n";
        }

        $csv_handler = fopen($csvFilename, 'w');
        fwrite($csv_handler, $csvFile);
        fclose($csv_handler);
    }


    /**
     * Send email confirmation to Some who can setup the Account's email address
     *
     * @return int
     */
    public function sendStockItemCreatedEmailToSales($data) {
        \Yii::info(__METHOD__ . ':' . print_r($data, true));

        /** @var Mailer $mailer */
        /** @var Message $message */

        // modify view path to module views
        $mailer           = Yii::$app->mailer;
        $oldViewPath      = $mailer->viewPath;
        $mailer->viewPath = Yii::$app->params['account.emailPath'];

        $data['showkeys'] = true; // include keys

        // send email
        $account    = $this;
        $accountNum = $account->customer_exertis_account_number;
        $customer   = $account->customer;
        //$profile = $user->profile;
        //$email   = $user->new_email !== null ? $user->new_email : $user->email;

        $email   = Yii::$app->params['account.StockItemCreatedEmailToSalesRecipients']; //['russell.hutson@exertis.co.uk', 'hellen.balco@exertis.co.uk', 'jamie.hughes@exertis.co.uk'];
        $subject = 'Exertis Digital Stock Room: INTERNAL NOTIFICATION - ' . Yii::t("app", "Account") . ' ' . $accountNum . ' ' . $customer->name . ' stock item available';
        $message = $mailer->compose('stockItemCreatedEmail', compact("subject", "account", "data"))
                          ->setTo($email)
                          ->setSubject($subject);

        // check for messageConfig before sending (for backwards-compatible purposes)
        if (empty($mailer->messageConfig["from"])) {
            $message->setFrom(Yii::$app->params["adminEmail"]);
        }
        $result = $message->send();

        // restore view path and return result
        $mailer->viewPath = $oldViewPath;

        return $result;

    } // sendStockItemCreatedEmailToSales

    /**
     * ATTACH STOCK ITEM CREATED SPREADSHEET
     * =====================================
     * This creates the spreadsheet version of the newly purchased stock items
     * and attaches it to the passed message object, ready for emailing.
     *
     * RCH 20160321
     *
     * @todo This looks like it's unfinished
     *
     * @param $data
     */
    private function attachStockItemCreatedSpreadsheet(&$message, $data) {

        $exc = new \frontend\components\ExcelExporter;

        $spreadSheetName = $exc->newSalesEmail($data);

        $message->attach($spreadSheetName);
    }


    public function getAccountLogo() {

        if (!empty($this->logo)) {
            return Yii::$app->params['uploadUrl'] . '/account_logos/' . $this->logo;

            return Yii::$app->params['frontendBaseUrl'] . Yii::$app->params['uploadUrl'] . '/account_logos/' . $this->logo;
        } else {
            return '';
        }
    } // getAccountLogo

    /**
     * Retrieve all AuditTrail entries for usernames on this account.
     * Useful for showing the activity of all users
     *
     * @return \yii\data\ActiveDataProvider
     */
    public function getUserAuditTrailEntries() {
        $usersOnThisAccount = $this->getUsersAsArray();
        // Seems that we are recording email addresses in the username column when saving audit records
        $usernames = array_map(function($v) {
            return $v['email'];
        }, $usersOnThisAccount);

        if (in_array('russell.hutson@exertis.co.uk', $usernames)) {
            $usernames = array_diff($usernames, ['russell.hutson@exertis.co.uk']);
        }

        $activity = AuditTrail::getActivityForUsernames($usernames, ['digital_product']);


        return $activity;
    }
}
