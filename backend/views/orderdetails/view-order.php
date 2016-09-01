<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;
use kartik\grid\GridView;
use \common\models\StockItem;
use common\models\Orderdetails;

/**
 * @var yii\web\View $this
 * @var common\models\Orderdetails $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Order Search'), 'url' => ['search']];
$this->params['breadcrumbs'][] = $this->title;
$stockIds = [];

$sck = Orderdetails::find()->where(['po' => $model->po])->all();
foreach($sck as $s){
	array_push($stockIds, $s->stock_item_id);
}

?>
<div class="orderdetails-view">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>


    <?= DetailView::widget([
            'model' => $model,
            'condensed'=>false,
            'hover'=>true,
            'panel'=>[
            'heading'=>$this->title,
            'type'=>DetailView::TYPE_INFO,
        ],
        'attributes' => [
            'id',
            'name',
            'street',
            'town',
            'city',
            'postcode',
            'country',
            'sop',
            'po'
        ],
    ]) ?>


    <h2>Stock Items</h2>

    <?php
    	$stockItems = new \yii\data\ActiveDataProvider([
		    'query' => StockItem::find()->where(['in', 'id', $stockIds]),
		    'pagination' => [
		        'pageSize' => 20,
		    ],
		]);

		echo GridView::widget([
			'dataProvider' => $stockItems,
			'columns' => [
				'id',
				[
					'label' => 'Boxshot',
					'format' => 'raw',
					'value' => function($data){
						return Html::img($data->boxShotUrl, ['class' => 'img-responsive img-thumbnail digitalproduct_thumb']);
					}
				],
				'productName',
				'productcode',
			],
	        'panel' => [
	            'type'=>'success',
	            'before'=>Html::a('<i class="glyphicon glyphicon-floppy-disk"></i> Export as CSV', ['export-stock-items', 'id'=>$model->id], ['class' => 'btn btn-success']),     
	            'showFooter'=>false
	        ],
	 	]);
    ?>

</div>
