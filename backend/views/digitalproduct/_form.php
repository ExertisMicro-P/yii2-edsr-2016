<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var common\models\DigitalProduct $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="digital-product-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [

'partcode'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Partcode...', 'maxlength'=>45]],

'is_digital'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Is Digital...']],

'description'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Description...', 'maxlength'=>45]],
'eztorm_id'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Description...', 'maxlength'=>45]],

    ]


    ]);
    echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
