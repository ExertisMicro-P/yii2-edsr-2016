<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\CustomerSearch $searchModel
 */

$this->title = Yii::t('app', 'Customers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">
    <div class="page-header">
            <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Customer',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'exertis_account_number',
                'format' => 'raw',
                'value' => function($data){
                    $account = \common\models\Account::find()->where(['customer_exertis_account_number' => $data->exertis_account_number])->one();
                    if($account){
                        return Html::a($data->exertis_account_number, ['/account/view', 'id'=>$account->id]);
                    } else {
                        return $data->exertis_account_number;
                    }
                }
            ],
            'status',
            'name',
            'invoicing_address_line1',
            'invoicing_address_line2',
//            'invoicing_address_line3', 
//            'invoicing_address_line4', 
//            'invoicing_postcode', 
//            'invoicing_city', 
//            'invoicing_country_code', 
//            'delivery_address_line1', 
//            'delivery_address_line2', 
//            'delivery_address_line3', 
//            'delivery_address_line4', 
//            'delivery_postcode', 
//            'delivery_city', 
//            'delivery_country_code', 
//            'vat_code', 
//            'fixed_shipping_flag', 
//            'fixed_shipping_charge', 
//            'payment_terms', 
//            'phone_number', 
//            'shipping_method', 
//            'unknown1', 
//            'unknown2', 
//            ['attribute'=>'timestamp','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']], 

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                'update' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['customer/view','id' => $model->exertis_account_number,'edit'=>'t']), [
                                                    'title' => Yii::t('yii', 'Edit'),
                                                  ]);}

                ],
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>true,




        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),                                                                                                                                                          'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>

</div>
