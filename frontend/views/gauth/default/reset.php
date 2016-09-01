<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\password\PasswordInput;

$this->registerJsFile('/js/registration.js', ['depends' => 'yii\web\YiiAsset']);

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var amnah\yii2\user\models\User $user
 * @var bool $success
 * @var bool $invalidKey
 *
 */

$this->title = Yii::t('user', 'Reset');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="confirm row">
    <article class="contact">

        <div id="doreg">
            <div class="short-top">
                <h2>Account Confirmed : Step 2 of 3</h2>
                <p>Thank you for confirming your email address. You are now ready to create your site password.</p>
                <p class="smaller">Once this is complete, we will send you another email with details of the final step, your Google Authentication signup</p>

            </div>

            <div class="row">
                <div class="col-lg-12">
                    <input type="hidden" id="gauser-dummy" />

                    <?php $form = ActiveForm::begin(['id' => 'reset-form', 'layout' => 'horizontal',
                                                     'enableClientValidation' => true,
                                                     'enableAjaxValidation' => false]); ?>

                    <?= Html::HiddenInput('key', $userKey) ?>
                    <?= $form->field($user, 'newPassword')->widget(PasswordInput::classname(), ['size'=>'sm',
                                        'pluginOptions' => ['toggleMask' => false,
                                                            'userField' => 'dummy',             // the js breaks without a real input
                                                            'hasUser'   => false,
                                                            'hasEmail'  => false
                                                         ]
                                ]); ?>
                    <?= $form->field($user, 'newPasswordConfirm')->passwordInput() ?>

                    <div id="rerrs" class="has-error">
                        <div class="help-block"></div>
                    </div>

                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('user', 'Create'), ['class' => 'btn submit']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        <div id="rdone" style="display:none">
            <div class="short-top">
                <h2>Account Confirmed : Step 3 of 3</h2>
                <p>Thanks for setting your password. Please keep it safe.</p>
                <p class="smaller">We have sent you an email with the final instructions on how to configure your Google Authentication application.</p>


               <p>Your final step is to install Google Authenticator on your mobile phone</p>

                <a href="https://support.google.com/accounts/answer/1066447?hl=en"  title="Instructions for Google Authenticator">Google Authenticator installation instructions here.</a>

                <p>Google Authenticator uses your mobile phone to generate an extra password which you will need every time you log into your digital stockroom. Google Authenticator works with iOS, Android, and Blackberry devices.</p>
                <p>Once Google Authenticator is installed, you need to tell it about your Digital Stock Room. Setup a new account in Google Authenticator by using you phone's camera and the QR Code below.</p>


                <div class="col-md-4"></div>


            </div>
        </div>
    </article>
</div>
