<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var common\models\AccountSopLookup $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="account-sop-lookup-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [

'created'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Created...']], 

'account'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Account...', 'maxlength'=>50]], 

'sop'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Sop...', 'maxlength'=>50]], 

'contact'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Contact...', 'maxlength'=>50]], 

'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Name...', 'maxlength'=>100]], 

'street'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Street...', 'maxlength'=>200]], 

'town'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Town...', 'maxlength'=>200]], 

'city'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter City...', 'maxlength'=>200]], 

'country'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Country...', 'maxlength'=>200]], 

'postcode'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Postcode...', 'maxlength'=>200]], 

    ]


    ]);
    echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
