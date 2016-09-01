<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\AccountRuleMapping */
/* @var $form yii\widgets\ActiveForm */

backend\assets\QueryBuilderAsset::register($this);
?>

<div class="account-rule-mapping-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'account_id')->textInput(['value'=>(isset($_GET['account']))? $_GET['account'] : null]) ?>
    
    <?php
        $rules = explode(',', $model->account_rule_id);
        echo $form->field($model, 'account_rule_id')->checkboxList(\yii\helpers\ArrayHelper::map(\backend\models\AccountRule::find()->where(['<>', 'id', '32'])->all(), 'id', 'ruleName'), ['checked'=>(in_array($model->account_rule_id, $rules))? true : false]);
    ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>