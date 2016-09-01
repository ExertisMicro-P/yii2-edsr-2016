<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var amnah\yii2\user\models\Role $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="role-form">

    <?php $form = ActiveForm::begin(['type'=>ActiveForm::TYPE_HORIZONTAL]); echo Form::widget([

    'model' => $model,
    'form' => $form,
    'columns' => 1,
    'attributes' => [

'name'=>['type'=> Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter Name...', 'maxlength'=>255]],

'can_admin'=>['type'=> Form::INPUT_TEXT, 'value'=>$model->can_admin, 'options'=>['placeholder'=>'Enter Can Admin...']],
'can_setupuseremail'=>['type'=> Form::INPUT_TEXT, 'value'=>$model->can_setupuseremail, 'options'=>['placeholder'=>'Enter Can Admin...']],

    ]


    ]);
    echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
    ActiveForm::end(); ?>

</div>
