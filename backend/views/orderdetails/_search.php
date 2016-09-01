<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\models\OrderdetailsSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="orderdetails-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'stock_item_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'contact') ?>

    <?= $form->field($model, 'street') ?>

    <?php // echo $form->field($model, 'town') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'postcode') ?>

    <?php // echo $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'sop') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
