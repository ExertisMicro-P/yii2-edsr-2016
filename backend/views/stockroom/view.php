<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

use yii\data\ActiveDataProvider;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

use common\models\StockItem;
use common\models\StockItemSearch;

/**
 * @var yii\web\View $this
 * @var common\models\Stockroom $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stockrooms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stockroom-view">
    <div class="page-header">
        <h1>Stockroom: <?= Html::encode($this->title) ?></h1>
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
            'id',
            'name',
            'account_id',
            ['attribute' => 'account',
                'label' => 'Owner',
                'value' => $model->account->customer->name,
                ],
            ['attribute' => 'account',
                'label' => '',
                'value' => $model->account->accountLogo,
                'format' => ['image',['width'=>'100']]
                ],

        ],
        'deleteOptions'=>[
        'url'=>['delete', 'id' => $model->id],
        'data'=>[
        'confirm'=>Yii::t('app', 'Are you sure you want to delete this item?'),
        'method'=>'post',
        ],
        ],
        'enableEditMode'=>true,
    ]);


    $provider = new ActiveDataProvider([
        'query' => $model->getStockItems(),
        'pagination' => [
            'pageSize' => 20,
        ],
    ]);
    $provider->setSort([
        'defaultOrder' => [
            'timestamp_added' => SORT_DESC
        ]
    ]) ;

    $stockItemSearchModel = new StockItemSearch();

     Pjax::begin(); echo GridView::widget([
        'dataProvider' => $provider,
        'filterModel' => $stockItemSearchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],


            [
             'label'=>'Stock Item',
             'format' => 'raw',
             'value'=>function ($data) {
                        return Html::a($data->id,Url::to(['stockitem/view','id'=>$data->id]), array('data-pjax'=>'0'));
                      },
             ],
            //'stockroom_id',
            'productcode',
            'digitalProduct.description',
            'eztorm_order_id',
            'status',
            'spare', // RCH 20160819
//            ['attribute'=>'timestamp_added','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s']],
            ['attribute'=>'timestamp_added'],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                'view' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Yii::$app->urlManager->createUrl(['stockitem/view','id' => $model->id]), [
                                                    'title' => Yii::t('yii', 'View'),
                                                    'data-pjax'=>0,
                                                  ]);},
                'update' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['stockitem/view','id' => $model->id,'edit'=>'t']), [
                                                    'title' => Yii::t('yii', 'Edit'),
                                                    'data-pjax'=>0,
                                                  ]);}

                ],
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>true,




        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> Stock Items in '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),                                                                                                                                                          'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>


       <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $audittrailDataProvider,
        'filterModel' => $audittrailSearchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
           //'table_name',
           //'record_id',
            'message',
            'timestamp',
            'username',
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>true,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> Audit Trail for Account '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>
</div>
