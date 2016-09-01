<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\models\AccountSopLookupSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="account-sop-lookup-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'account') ?>

    <?= $form->field($model, 'sop') ?>

    <?= $form->field($model, 'created') ?>

    <?= $form->field($model, 'contact') ?>

    <?php // echo $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'street') ?>

    <?php // echo $form->field($model, 'town') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'postcode') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
