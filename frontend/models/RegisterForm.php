<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class RegisterForm extends Model
{
    public $recentsop;
    public $email;
    public $account;
    public $verifyCode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['recentsop', 'email', 'account', 'verifyCode'], 'required'],
            ['recentsop', 'number', 'integerOnly'=>true, 'max'=>9999999, 'min'=>1700000],
            // email has to be a valid email address
            ['email', 'email'],
          ['account', 'match', 'pattern' => '/^(E[CETXZ])|(G[CEV])|(M[CDELMNSTVXZ])[0-9]{6}$/i'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'recentsop' => 'Recent Order Number',
            'email' => 'Email',
            'verifyCode' => 'Verification Code',
        ];
    }

    
}
