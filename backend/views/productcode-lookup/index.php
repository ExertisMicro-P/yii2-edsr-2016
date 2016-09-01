<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ProductcodeLookupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Productcode Lookups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="productcode-lookup-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Productcode Lookup', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'product_id',
            'name',
            'eztorm_store_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
