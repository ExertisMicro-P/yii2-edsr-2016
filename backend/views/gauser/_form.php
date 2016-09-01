<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

use kartik\builder\TabularForm;

/**
 * @var yii\web\View $this
 * @var common\models\gauth\GAUser $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="gauser-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [

'email'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Email...', 'maxlength'=>255]],

'username'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Username...', 'maxlength'=>255]],

'newPassword'=>['type'=> TabularForm::INPUT_TEXT, 'options'=>['placeholder'=>'Enter New Password...']],

'newPasswordConfirm'=>['type'=> TabularForm::INPUT_TEXT, 'options'=>['placeholder'=>'Enter ...']],

'role_id'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Role ID...', 'maxlength'=>2]],

    ]


    ]);
    echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
