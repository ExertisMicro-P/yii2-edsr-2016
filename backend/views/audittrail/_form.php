<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var exertis\savewithaudittrail\models\Audittrail $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="audittrail-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [

'record_id'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Record...']], 

'table_name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Table...', 'maxlength'=>255]], 

'message'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Message...', 'maxlength'=>255]], 

'username'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Username...', 'maxlength'=>255]], 

    ]


    ]);
    echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
