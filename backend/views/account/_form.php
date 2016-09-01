<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var common\models\Account $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="account-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [

'timestamp'=>['type'=> Form::INPUT_WIDGET, 'widgetClass'=>DateControl::classname(),'options'=>['type'=>DateControl::FORMAT_DATETIME]],

'eztorm_user_id'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter eZtorm User ID...', 'maxlength'=>45]],

'customer_exertis_account_number'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Exertis Account Number...', 'maxlength'=>20]],

'key_limit'=>['type'=> Form::INPUT_TEXT],

    ]


    ]);
    echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
