<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\models\CustomerSearch $model
 * @var yii\widgets\ActiveForm $form
 */
?>

<div class="customer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'exertis_account_number') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'invoicing_address_line1') ?>

    <?= $form->field($model, 'invoicing_address_line2') ?>

    <?php // echo $form->field($model, 'invoicing_address_line3') ?>

    <?php // echo $form->field($model, 'invoicing_address_line4') ?>

    <?php // echo $form->field($model, 'invoicing_postcode') ?>

    <?php // echo $form->field($model, 'invoicing_city') ?>

    <?php // echo $form->field($model, 'invoicing_country_code') ?>

    <?php // echo $form->field($model, 'delivery_address_line1') ?>

    <?php // echo $form->field($model, 'delivery_address_line2') ?>

    <?php // echo $form->field($model, 'delivery_address_line3') ?>

    <?php // echo $form->field($model, 'delivery_address_line4') ?>

    <?php // echo $form->field($model, 'delivery_postcode') ?>

    <?php // echo $form->field($model, 'delivery_city') ?>

    <?php // echo $form->field($model, 'delivery_country_code') ?>

    <?php // echo $form->field($model, 'vat_code') ?>

    <?php // echo $form->field($model, 'fixed_shipping_flag') ?>

    <?php // echo $form->field($model, 'fixed_shipping_charge') ?>

    <?php // echo $form->field($model, 'payment_terms') ?>

    <?php // echo $form->field($model, 'phone_number') ?>

    <?php // echo $form->field($model, 'shipping_method') ?>

    <?php // echo $form->field($model, 'unknown1') ?>

    <?php // echo $form->field($model, 'unknown2') ?>

    <?php // echo $form->field($model, 'timestamp') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
