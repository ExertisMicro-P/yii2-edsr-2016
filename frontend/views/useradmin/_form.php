<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Useradmin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="useradmin-form">
    
    <h3><?= Html::a('<< Back', '/useradmin') ?></h3>
    
    <br><br>

    <?php $form = ActiveForm::begin(); ?>

    <?php
        // echo $form->field($model, 'role_id')->dropDownList(\yii\helpers\ArrayHelper::map(common\models\EDSRRole::find()->asArray()->all(), 'id', 'name'), ['prompt'=>'Select Role']) 
        echo $form->field($model, 'role_id')->hiddenInput(['value'=>\common\models\EDSRRole::ROLE_SUBUSER])->label(false);
    ?>

    <?php
        echo($model->isNewRecord)? $form->field($model, 'email')->textInput(['maxlength' => true]) : null;
    ?>

    <?php
        // RCH 20160209 
        // not needed
        // echo $form->field($model, 'new_email')->textInput(['maxlength' => true]) 
    ?>

    <?php
        // RCH 20160209 
        // We should derive the username from the email address to keep things unique
        // echo $form->field($model, 'username')->textInput(['maxlength' => true]) 
    ?>

    <?= $form->field($model, 'spending_limit')->textInput(['maxlength'=>'50', 'style'=>'width:300px', 'placeholder'=>'E.g.: 99.00']) ?>

    <?php
        // RCH 20160209
        // Password will be set by the user when the click on the lionk in the email
        //echo $form->field($model, 'newPassword')->passwordInput(['value'=>'']);
        //echo $form->field($model, 'newPasswordConfirm')->passwordInput(['value'=>'']); 
    ?>

<!--
    <?= $form->field($model, 'auth_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'api_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'login_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'login_time')->textInput() ?>

    <?= $form->field($model, 'create_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'update_time')->textInput() ?>

    <?= $form->field($model, 'ban_time')->textInput() ?>

    <?= $form->field($model, 'ban_reason')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uuid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'account_id')->textInput() ?>
-->

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Cancel', '/useradmin', ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
