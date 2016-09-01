<?php
/**
 * Created by PhpStorm.
 * User: noelwalsh
 * Date: 05/12/2014
 * Time: 16:03
 */

namespace common\models\gauth;

use Yii;
use common\models\Account;
use common\models\Stockroom;
use kartik\password\StrengthValidator;
use exertis\savewithaudittrail\SaveWithAuditTrailBehavior;
use exertis\savewithaudittrail\models\Audittrail;

class GAUser extends \amnah\yii2\user\models\User
{

    const STATUS_UNCONFIRMED_EMAIL_VALUE_10 = 10;

    public $dummy;                                             // Required to keep the password validator happy

    public function behaviors()
    {
        return [
            [
                'class'     => SaveWithAuditTrailBehavior::className(),
                'userClass' => '\common\models\gauth\GAUser',
            ],
        ];
    }


    public function rules()
    {

        // set initial rules
        $rules   = parent::rules();
        $rules[] = [['newPassword'], StrengthValidator::className(),
            'userAttribute' => 'dummy',         // ***** NEED to create an empty input tag id=gauser-dummy
            'lower'         => 1,               // At least one lower case
            'upper'         => 1,               //      1 upper case
            'special'       => 0,               //      1 non-alphanumeric
            'digit'         => 1,               //      1 numeric
            'min'           => 8,               // At least 8 characters long
            'hasUser'       => false,           //
            'hasEmail'      => true,
            'except' => ['ldap']
        ];
        $rules[] = [['newPassword', 'newPasswordConfirm', 'role_id'], 'required', 'except' => ['emailsetup', 'edituser']];
        $rules[] = [['newPassword', 'newPasswordConfirm', 'username'], 'filter', 'filter' => 'trim', 'skipOnArray' => true]; // RCH 20150818

        $rules[] = ['account_id', 'safe'];

        // admin crud rules
        $rules[] = [['status'], 'default', 'value' => GAUser::STATUS_INACTIVE, 'on' => ['admin']];
        $rules[] = [['spending_limit'], 'number']; // RCH 20160209
        $rules[] = [['spending_limit'], 'default', 'value' => '0.00'];
        $rules[] = [['spending_limit', 'new_email', 'role_id', 'shopEnabled'], 'safe'];
        return $rules;
    }

    /**
     * Update login info (ip and time)
     *
     * @return bool
     */
    public function updateLoginMeta()
    {

        $result    = parent::updateLoginMeta();
        $tableName = self::tableName();
        $recordId  = $this->id;

        if ($result) {
            $msg = 'Successful login by ' . $this->email;

        } else {
            $msg = 'Failed login attempt by ' . $this->email;
        }

        $msg .= " ($recordId) from IP " . $this->login_ip;

        // -------------------------------------------------------------------
        // If this user is linked to an account, use that for the audit trail
        // -------------------------------------------------------------------
        if (($account = $this->account)) {
            $msg .= ' for account ' . $account->customer_exertis_account_number;
            $tableName = $account->tableName();
            $recordId  = $account->id;
        }

        $auditentry = new Audittrail();
        $auditentry->log($msg, $tableName, $recordId);

        $auditentry->save();
    }

    /**
     * FIND BY ID
     * ==========
     * @param $id
     *
     * @return null|static
     */
    public static function findById($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }


    /**
     * Finds user by email
     *
     * @param string $username
     *
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStockrooms()
    {
        return $this->hasMany(Stockroom::className(), ['account_id' => 'account_id']);
    }


    /**
     * GET STOCK ROOM DETAILS
     * ======================
     * Locates all stockrooms for the current user and returns then details,
     * the id and the user assigned name, for display
     *
     * @return array
     */
    public function getStockroomDetails()
    {
        $result        = [];
        $stockRooms    = $this->stockrooms;
        $accountStatus = '';

        foreach ($stockRooms as $sroom) {
            if (!$accountStatus) {
                $accountStatus = (isset($sroom->account->customer->status))? $sroom->account->customer->status : '';
            }
            $result[] = [
                'id'          => $sroom->id,
                'name'        => $sroom->name,
                'accountlogo' => 'https://res.cloudinary.com/exertis-uk/image/upload/edsr/account_logos/' . $sroom->account->logo,
                'cid'         => $sroom->account->customer->id

            ];
        }
        if (!$accountStatus) {
            //$accountStatus = 'T';               // if it wasn't set, default to on hold
            $accountStatus = \common\models\Customer::STATUS_HOLD;               // if it wasn't set, default to on hold RCH 20160308
        }

        if (count($stockRooms) > 0) {
            \Yii::$app->session['currentStockRoomId'] = $stockRooms[0]['id'];
        }

        return ['stockrooms' => $result, 'accountStatus' => $accountStatus];
    }


    /**
     * Send email confirmation to user
     *
     * @param UserKey $userKey
     *
     * @return int
     */
    public function sendEmailConfirmation($userKey)
    {
        /** @var Mailer $mailer */
        /** @var Message $message */

        // modify view path to module views
        $mailer           = Yii::$app->mailer;
        $oldViewPath      = $mailer->viewPath;
        $mailer->viewPath = Yii::$app->getModule("user")->emailViewPath;

        // send email
        $user    = $this;
        $profile = $user->profile;
        $email   = $user->new_email !== null ? $user->new_email : $user->email;
        $subject = 'Exertis Digital Stock Room: ' . Yii::t("user", "Email Confirmation");
        $message = $mailer->compose('confirmEmail', compact("subject", "user", "profile", "userKey"))
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
    }


}
