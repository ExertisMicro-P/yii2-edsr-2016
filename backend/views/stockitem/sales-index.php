<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\StockItemSearch $searchModel
 */

$this->title = Yii::t('app', 'Stock Items');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-item-index">
    <div class="page-header">
        <h1><?= Yii::t('app','SOP/Keys Tracker') ?></h1>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Stock Item',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            //'stockroom_id',
            [
                'format'=>'html',
                'value' => function ($data) {

                    // Write your formula below
                    return !filter_var($data->digitalProduct->image_url, FILTER_VALIDATE_URL) ? 'no image' : Html::img($data->digitalProduct->image_url, ['class'=>'digitalproduct_thumb']);
                }
            ],

            'productcode',
            'description',
            //'key',
            'status',

            [ 'attribute' => 'sop',
                'label' => 'SOP'
            ],
            [ 'attribute' => 'orderdetailspo',
                'label' => 'PO'
            ],


            [ 'attribute' => 'customerExertisAccountNumber',
                'label' => 'Account',
                'format' => 'raw',
                'value' => function($data) {
                                     return Html::a($data->customerExertisAccountNumber, Yii::$app->urlManager->createUrl(['account/view','id' => $data->stockroom->account_id]), [
                                                    'title' => Yii::t('yii', 'View Account'),
                                                  ]);}

                
            ],
            [ 'attribute' => 'customerName',
                'label' => 'Account Name'
            ],
/*            [
                'attribute'=>'tagNames',
                'options' => ['data-role'=>'tagsinput'],
            ],
 *
 */
            ['attribute'=>'timestamp_added'/*, 'format'=>'datetime'*/],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                'update' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['stockitem/view','id' => $model->id,'edit'=>'t']), [
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
            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),
            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['sales-index'], ['class' => 'btn btn-info']),
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>

</div>
