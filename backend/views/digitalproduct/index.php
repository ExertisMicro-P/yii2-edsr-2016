<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\DigitalProductSearch $searchModel
 */

$this->title = Yii::t('app', 'Digital Products');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="digital-product-index">
    <div class="page-header">
            <h1><?= Html::encode($this->title) ?></h1>
    </div>
    
    <?php echo Html::a('<i class="glyphicon glyphicon-search"></i> View Ztorm Catalogue', ['ztorm-catalogue'], ['class' => 'btn btn-primary']); ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Digital Product',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>
    
    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'format' => 'raw',
                'value' => function($data){
        
                    $product = \common\models\DigitalProduct::find()->where(['id'=>$data->id])->one();
                    $lookup = common\models\ProductcodeLookup::find()->where(['id'=>$data->eztorm_id])->one();
                    
                    return $data->id;
                }
            ],
            'partcode',
            'description',
            [
                    'format'=>'html',
                    'value' => function ($data) {

                        // gets a ProductImage
                        $productimage = $data->mainImage;
                        return empty($productimage->image_url) ? 'no image' : Html::img($productimage->image_url, ['class'=>'digitalproduct_thumb']);
                    }
            ],
            [
                'label' => 'Leaflet',
                'format'=>'html',
                'value' => function ($data) {

                    // gets a ProductImage
                    return $data->getLeafletImageTag() ;
                }
            ],
            'numStockItems',
            [
                'attribute' => 'display_price_as',
                'filter' => Html::activeDropDownList($searchModel, 'display_price_as', yii\helpers\ArrayHelper::map(common\models\DigitalProduct::find()->all(), 'display_price_as', 'display_price_as'), ['class' => 'form-control', 'prompt' => 'Show All'])
            ],
            [
                'attribute' => 'fixed_price',
                'format' => 'raw',
                'value' => function($data){
                    return '£' . number_format($data->fixed_price, 2);
                }
            ],
                    
                    
            [
                'label' => 'Product Mapping',
                'format' => 'raw',
                'value' => function($data){
                    //$productId = \common\models\ProductcodeLookup::findOne(['id'=>$data->eztorm_id]);
                    
                    
                    $catalogue = common\models\ZtormCatalogueCache::find()->where(['RealProductId'=>$data->productCode_Lookup->product_id])->exists();
                    
                    if(!$catalogue){
                        $title = 'Warning, this product is no longer available in the catalogue.';
                        $warning = '<img src="/img/warning.png" width="25" height="25" alt="Warning" data-toggle="tooltip" data-placement="right" title="'.$title.'" class="img-responsive" />';
                    } else {
                        $warning = '';
                    }
                    
                
                    $url = Yii::$app->urlManager->createUrl(['/productcode-lookup/view', 'id' => $data->productCode_Lookup->id]);
                    $btn = Html::a('View Mapping', $url, ['class' => 'btn btn-info']);
                    
                    return $btn . ' &nbsp; ' . $warning;
                }
            ],


            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                'update' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['digitalproduct/view','id' => $model->id,'edit'=>'t']), [
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
