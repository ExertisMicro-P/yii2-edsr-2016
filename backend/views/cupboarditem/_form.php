<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var common\models\CupboardItem $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="cupboard-item-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [

'cupboard_id'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Cupboard ID...']], 

'digital_product_id'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Digital Product ID...']], 

'timestamp_added'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>DateControl::classname(),'options'=>['type'=>DateControl::FORMAT_DATETIME]], 

    ]


    ]);
    echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
