<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

/**
 * @var yii\web\View $this
 * @var common\models\Customer $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-view">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>


    <?= DetailView::widget([
            'model' => $model,
            'condensed'=>false,
            'hover'=>true,
            'mode'=>Yii::$app->request->get('edit')=='t' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
            'panel'=>[
            'heading'=>$this->title,
            'type'=>DetailView::TYPE_INFO,
        ],
        'attributes' => [
            'exertis_account_number',
            'status',
            'name',
            'invoicing_address_line1',
            'invoicing_address_line2',
            'invoicing_address_line3',
            'invoicing_address_line4',
            'invoicing_postcode',
            'invoicing_city',
            'invoicing_country_code',
            'delivery_address_line1',
            'delivery_address_line2',
            'delivery_address_line3',
            'delivery_address_line4',
            'delivery_postcode',
            'delivery_city',
            'delivery_country_code',
            'vat_code',
            'fixed_shipping_flag',
            'fixed_shipping_charge',
            'payment_terms',
            'phone_number',
            'shipping_method',
            'unknown1',
            'unknown2',
            [
                'attribute'=>'timestamp',
                'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
                'type'=>DetailView::INPUT_WIDGET,
                'widgetOptions'=> [
                    'class'=>DateControl::classname(),
                    'type'=>DateControl::FORMAT_DATETIME
                ]
            ],
        ],
        'deleteOptions'=>[
        'url'=>['delete', 'id' => $model->exertis_account_number],
        'data'=>[
        'confirm'=>Yii::t('app', 'Are you sure you want to delete this item?'),
        'method'=>'post',
        ],
        ],
        'enableEditMode'=>true,
    ]) ?>

</div>
