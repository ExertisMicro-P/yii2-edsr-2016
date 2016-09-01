<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\AccountRule */
/* @var $form yii\widgets\ActiveForm */

backend\assets\QueryBuilderAsset::register($this);
?>

<div class="account-rule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ruleName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ruleQuery')->hiddenInput() ?>
    
    <div id="queryBuilder">
        
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id'=>'saveBtn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
