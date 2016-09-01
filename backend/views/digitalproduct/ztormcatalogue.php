<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

backend\assets\ZtormAsset::register($this);

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\DigitalProductSearch $searchModel
 */

$this->title = Yii::t('app', 'Ztorm Catalogue');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="digital-product-index">
    <div class="page-header">
            <h1><?= Html::encode($this->title) ?></h1>
    </div>
    
    <?php
        \yii\bootstrap\Modal::begin([
            'header' => '<h2 id="modalHeader"></h2><p id="prodName"></p>',
            'toggleButton' => ['label' => 'Close', 'class'=>'hidden', 'id'=>'modalForm']
        ]);
        
    ?>
    
    <div class="row">

        <div class="col-sm-12 col-md-6">
            <div class="form-group">
                <input type="text" name="exertis_pc" placeholder="Exertis Part Code" class="form-control">
            </div>

            <div class="form-group">
                
                <?=  Html::dropDownList('display_price', null, $displayPriceDropdown, ['class'=>'form-control _displayPrice', 'prompt'=>'Select Display Price'])?>
                
            </div>

            <div class="form-group">
                <input type="text" name="fixed_price" placeholder="Fixed Price" class="form-control" style="display:none">
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-success _saveProduct">Save</button>
            </div>
        </div>

        <div class="col-sm-12 col-md-6">
            <input type="text" name="searchForPartCode" placeholder="Search For Part Code" class="form-control" />
            <p id="partCodes">
                
            </p>
        </div>
        
    </div>
        
    <?php
        
        \yii\bootstrap\Modal::end();
    ?>
    
    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $products,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'RealProductId',
            'Name',
            [
                'label' => 'Action',
                'format' => 'raw',
                'value' => function($data){
        
                    $productcodeLookup = new common\models\ProductcodeLookup();
                    $eztormId = $productcodeLookup->find()->where(['product_id'=>$data->RealProductId])->one();
                    
                    $digitalProducts = new common\models\DigitalProduct();

                    if($eztormId){
                        $alreadyInDigitalProducts = $digitalProducts->find()->where(['eztorm_id'=>$eztormId->id])->one();
                    }

                    if($eztormId && $alreadyInDigitalProducts){
                        if($alreadyInDigitalProducts->enabled == 1){
                            $btn = Html::button('Disable product', ['class'=>'btn btn-danger _enableDisableProduct', 'id'=>$data->RealProductId, 'data-action'=>'disable']);
                        } else {
                            $btn = Html::button('Enable product', ['class'=>'btn btn-success _enableDisableProduct', 'id'=>$data->RealProductId, 'data-action'=>'enable']);
                        }
                    } else {
                        $btn = Html::button('Add product to EDSR', ['class'=>'btn btn-success _addToStockroom', 'id'=>$data->RealProductId, 'product-desc'=>$data->InformationFull, 'product-name'=>$data->Name]);
                    }
                    return $btn;
                },
            ],

        ],
        'pjax'=>true,
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>true,




        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type'=>'info',                                                                                                                                                       'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['ztorm-catalogue'], ['class' => 'btn btn-info']),
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>

</div>
