<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Orderdetails;
use yii\widgets\Pjax;
use yii\helpers\Url;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\OrderdetailsSearch $searchModel
 */

$this->title = Yii::t('app', 'Order Search');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orderdetails-index">
    <div class="page-header">
            <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            [
                'attribute' => 'customer',
                'value' => 'customer.exertis_account_number'
            ],
            'sop',
            'po',
            [
                'label' => 'Stock Items',
                'value' => function($data){
                    return Orderdetails::find()->where(['po' => $data->po])->count();
                }
            ],
            [
                'label' => '',
                'format' => 'raw',
                'value' => function($data){
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view-order', 'id'=>$data->id]);
                }
            ]
            
        ],
        'responsive'=>true,
        'hover'=>true,




        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>
    

</div>
