<?php
use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
?>
<div class="confirm">
    <article class="contact">

        <div class="short-top">
            <h2>Account Confirmation Error</h2>
            <p>Thank you for your interest in Exertis Digital Stock Room</p>
            <?php if (!($flash = Yii::$app->session->getFlash('Resend-success'))): ?>
            <p class="smaller">Unfortunately the key you provided has not been recognised, possibly because it has expired,
                so please reenter your details below and we will send another link</p>
            <?php endif; ?>
        </div>

        <div class="content clearfix">

            <?php $form = ActiveForm::begin(['action' => '/user/resend', 'id' => 'resend-form', 'class' => 'clearfix']); ?>


            <?php if ($flash = Yii::$app->session->getFlash('Resend-success')): ?>

                <div class="alert alert-success">
                    <p><?= $flash ?></p>
                </div>
                <p>&nbsp;</p>
                <p>Please use the link in the email to return here to confirm your acount.</p>
                <p>&nbsp;</p>
            <?php else: ?>


            <figure><img src="http://www.exertis.com/wp-content/themes/exertis/assets/img/icon-form.png"></figure>
                <div class="clearfix">
                    <h3>Send another registration email</h3>
                </div>

            <div class="form-group kv-fieldset-inline">
                <div class="clearfix colonly">
                    <?= $form->field($user, 'email')->input('email', ['type' => 'email', 'placeholder' => 'your email'])->label('') ?>
                </div>
            </div>
            <div class="form-group clearfix">
                <?= Html::submitButton(Yii::t('user', 'Submit'), ['class' => 'btn submit']) ?>
            </div>
            <?php endif; ?>

            <?php ActiveForm::end(); ?>




            <div class="required-fields">.</div>
        </div>

    </article>
</div>
