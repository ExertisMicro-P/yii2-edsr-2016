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

        <div id="doreg" xstyle="display:none">
            <div class="short-top">
                <h2>Email Address Confirmed : Step 2 of <?php echo ($usegauthify ? '3' : '2') ?></h2>
                <p>Thank you for confirming your email address. You are now ready to create your password.</p>
                <?php if ($usegauthify) { ?>
                <p class="smaller">Once this is complete, we will send you another email with details of the final step, your Google Authentication signup</p>
                <?php } ?>


                <div class="col-md-12 loader" align="center"><img src="/img/ajax-loader.gif" title="Please wait..." /></div>
            </div>


            <div class="row">
                <div class="col-lg-12">
                    <input type="hidden" id="gauser-dummy" />

                    <?php $form = ActiveForm::begin(['id' => 'reset-form', 'layout' => 'horizontal',
                                                     'enableClientValidation' => true,
                                                     'enableAjaxValidation' => false]); ?>

                    <?= Html::HiddenInput('key', $userKey) ?>
                    <?=
                        $form->field($user, 'newPassword')->widget(PasswordInput::classname(), ['size'=>'sm',
                                        'pluginOptions' => ['toggleMask' => false,
                                                            'userField' => 'dummy',             // the js breaks without a real input
                                                            'hasUser'   => false,
                                                            'hasEmail'  => false
                                                         ]
                                ]);
                    ?>
                    <?= $form->field($user, 'newPasswordConfirm')->passwordInput() ?>

                    <div id="rerrs" class="has-error">
                        <div class="help-block"></div>
                    </div>

                    <div class="well">
                        <p>Please read and agree to our terms and conditions</p>
                        <div id="terms">
                            <?= $this->render('/site/_legal_terms.php'); ?>
                        </div>
                        <blockquote>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="" title="Scroll through terms to enable" disabled id="agreecheckbox">
                              I agree to the EDSR Terms and Conditions
                            </label>
                        </div>
                        </blockquote>
                    </div>


                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('user', 'Create'), ['id'=>'submitbtn', 'class' => 'btn submit', 'disabled'=>'disabled']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <div id="rdone" style="display:none">
            <div class="short-top">
                <h2>Account Confirmed :<?php echo ($usegauthify ? 'Step 3 of 3' : 'Success') ?></h2>
                <p>Thanks for setting your password. Please keep it safe.</p>

                <?php if ($usegauthify) { ?>

                <p class="smaller col-md-8">Your final step is to install Google Authenticator on your mobile phone
                    If you don't already have the app, please view the
                    <a href="https://support.google.com/accounts/answer/1066447?hl=en"  title="Instructions for Google Authenticator">Google Authenticator installation instructions here.</a>
                    <br /><br />
Google Authenticator uses your mobile phone to generate an extra password which you will need every time you log into your digital stockroom. Google Authenticator works with iOS, Android, and Blackberry devices
                    <br /><br />
                    MS Windows Phone users should use <a href="http://www.windowsphone.com/en-us/store/app/authenticator/e7994dbc-2336-4950-91ba-ca22d653759b">Microsoft Authenticator</a>.
                </p>

                <p class="col-md-4">
                    <img id="gaqrurl" src="/img/ajax-loader.gif" width="250" height="250" />
                </p>
                <p>(this QR will disappear 10 minutes after the email is generated)</p>

                <p class="smaller col-md-12">
                If you can't use, or see, the QR Code, you can visit the following page in your mobile browser
                    <br /><br />
                    <span id="gaezurl"></span>

                    <br /><br />
                Alternatively, you can manually set up your account in the Google Authenticator app by entering the following details:
                    <br /><br />
                <strong>GA Account Name :</strong> <span id="gaaccount"><?= $ga->account ?></span>
                    <br /><br />
                <strong>GA Key:</strong> <span id="gakey"><?= $ga->key ?></span>

                    <br /><br />
                    Once your Google Authenticator account is created, you can proceed to login to your Exertis Digital Stock Room account.

                    <br /><br />

                    <?php } else { // if gauthify ?>
                <p>You may now login to see your digital products.</p>
                    <?php } ?>

                    To find out more about the Exertis Digital Stock Room, please visit

                    <a href="https://stockroom.exertis.co.uk" title="Exertis Digital Stock Room "/>https://stockroom.exertis.co.uk</a>





                <div style="clear:both"></div>
            </div>
        </div>
    </article>
</div>
<?php
