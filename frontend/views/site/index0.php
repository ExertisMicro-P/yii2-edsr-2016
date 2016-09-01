<?php
use yii\helpers\Html;
//use yii\bootstrap\ActiveForm;
use kartik\widgets\ActiveForm;
use kartik\widgets\ActiveField;
use yii\widgets\MaskedInput;
use yii\captcha\Captcha;

use yii\helpers\Url;


/* @var $this yii\web\View */
$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Welcome!</h1>

        <p class="lead">Manage all your Digital Products in one place. Safely.</p>

        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Learn more</a></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Already Registered?</h2>

                 <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                    <?= $form->field($model, 'username', [
                    'addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-user"></i>']]
                  ]) ?>
                    <?= $form->field($model, 'password', [
                    'addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-lock"></i>']]
                  ])->passwordInput() ?>
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                    <div style="color:#999;margin:1em 0">
                        If you forgot your password you can <?= Html::a('reset it', ['user/forgot']) ?>.
                    </div>
                    <div class="form-group">
                        <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                    </div>
                <?php ActiveForm::end(); ?>

            </div>
            <div class="col-lg-6 col-lg-offset-2">
                <h2>Sign Up</h2>
                <?php $registerform = ActiveForm::begin(['id' => 'register-form', 'action' =>'signup']); ?>
                <div class="row">
                  <div class="col-lg-6">
                    <?= $registerform->field($registermodel, 'recentsop', [
                    'addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-list-alt"></i>']]
                  ]); ?>
                  </div>

                  <div class="col-lg-6">
                     <?= $registerform->field($registermodel, 'account', [
                    'addon' => ['prepend' => ['content'=>'<i class="glyphicon glyphicon-bookmark"></i>']]
                  ]); ?>
                  </div>
                </div> <!-- row -->
                <div class="row">
                   <div class="col-lg-12">
                  <?php
                  echo $registerform->field($registermodel, 'email', [
                    'addon' => ['prepend' => ['content'=>'@']]
                  ]);
                  ?>
                  </div>
                </div> <!-- row -->

                <div style="color:#999;margin:1em 0">
                        You should receive an email from us with 1 working day.
                    </div>


                <?php //= $registerform->field($registermodel, 'verifyCode')->widget(Captcha::className()); ?>

                <div class="form-group">
                        <?= Html::submitButton('Create', ['class' => 'btn btn-primary', 'name' => 'register-button']) ?>
                </div>
                <?php ActiveForm::end(); ?>

            </div>

        </div>

    </div>
</div>
