<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\tabs\TabsX;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\StockroomSearch $searchModel
 */

$this->title = Yii::t('app', 'Stockrooms');
$this->params['breadcrumbs'][] = $this->title;


$creditBalance      = $credit['balance'];
$selectedStockClass = 'selected-stock' ;

$statusLookup = [
    \common\models\StockItem::STATUS_PURCHASED  => 'Available',
    \common\models\StockItem::STATUS_NOT_PURCHASED  => 'Pending'
] ;

?>
    <div id="stockroom-index">

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


        <?= Html::button('Help / Demo', [ 'class' => 'btn btn-primary', 'onclick' => '(function ( $event ) { bootstro.start(); })();' ]); ?>



        <?php Pjax::begin();

        echo GridView::widget([
            'tableOptions'    => ['id' => 'stocklevelTable'],
            'pjax'=>true,
            'pjaxSettings'      => [
                'replace'   => false
            ],
            'dataProvider'      => $dataProvider,
            'filterModel'       => $searchModel,
            'toolbar' =>  [], // RCH 20150227
            'options'           => ['id' => 'stockLevels', 'class' => 'grid-view bootstro' ,
                                    'data-bootstro-title'=>"Stock Room",
                                    'data-bootstro-content'=>'All your digital products get delivered into your Stock Room. Click on the checkboxes to choose products you want to deliver e.g. by email to your customer.',
                                    'data-bootstro-width'=>"600px",
                                    'data-bootstro-placement'=>"top",
                                    'data-bootstro-step'=>"0",
                                    'data-original-title'=>"", 'title'=>""],
            'toggleDataOptions' => [
                'all'   => [
                    'icon'  => '',
                    'label' =>''
                ],
            ],
//        'export'    => false,             // Supposed to hide the export button, but causes javascript error

            'rowOptions'    =>
                function ($model, $key, $index, $grid) {
                    return ['id' => 'prow_' . $model->digitalProduct->id];
                },

            'pager' => [
            ],
            'columns' => [

                array (
                    //'header' => 'Photo',
                    'format' => 'raw',
                    'vAlign' => 'middle',

                    'value'          => function ($model, $key, $index, $widget) {
                        //return $model->digitalProduct->getMainImageThumbnailTag(false);
                        return Html::img($model->getBoxShotUrl());
                    },
                    'contentOptions' => ['class' => 'text-center list-image']
                ),

                [
                    'attribute'      => 'productcode',
                    'contentOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'hidden-xs hidden-sm text-center'],
                    'headerOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
                    'filterOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
                    'width'          => '10em'
                ],
                [
                    'attribute'      => 'productName',
                    'contentOptions' => ['class' => 'hidden-xs xhidden-sm'],
                    'headerOptions'  => ['class' => 'hidden-xs xhidden-sm'],
                    'filterOptions'  => ['class' => 'hidden-xs xhidden-sm text-center'],
                ],

                [
                    'format' => 'raw',
                    'vAlign' => 'middle',
                    'label' => $statusLookup[\common\models\StockItem::STATUS_PURCHASED],
                    'contentOptions' => ['class' => 'text-center'],
                    'headerOptions'  => ['class' => 'text-center'],

                    'value'  => function ($model, $key, $index, $widget) use($statusLookup) {
                        $itemsCounts = $model->totalOfThisProductByStatus(array_keys($statusLookup)) ;
                        $total = 0 ;

                        foreach ($itemsCounts as $item) {
                            if ($item['status'] == \common\models\StockItem::STATUS_PURCHASED) {
                                $total += $item->num;
                            }
                        }
                        return $total ;
                    },
                    'width'          => '6em'
                ],


                [
                    'format' => 'raw',
                    'vAlign' => 'middle',
                    'label' => $statusLookup[\common\models\StockItem::STATUS_NOT_PURCHASED],
                    'headerOptions'  => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],

                    'value'  => function ($model, $key, $index, $widget) use($statusLookup) {
                        $itemsCounts = $model->totalOfThisProductByStatus(array_keys($statusLookup)) ;

                        $total = 0 ;
                        foreach ($itemsCounts as $item) {
                            if ($item['status'] == \common\models\StockItem::STATUS_NOT_PURCHASED) {
                                $total += $item->num;
                            }
                            return $total;
                        }
                    },
                    'width'          => '6em'
                ],

                [
                    'label'          => 'Price',
                    'format'         => 'raw',
                    'value'          => function ($model, $key, $index, $widget) {

                        return '&pound;' . number_format($model->itemPrice, 2) .
                        '<input type="hidden" class="price" value="' . $model->itemPrice . '" />';
                    },
                    'contentOptions' => ['class' => 'hidden-xs hidden-sm text-center'],
                    'headerOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
                    'filterOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
                ],

                [
                    'header'         => '',
                    'format'         => 'raw',
                    'headerOptions'  => ['class' => !$canBuy ? 'hidden' : ''],
                    'contentOptions' => ['class' => 'text-center' . (!$canBuy ? ' hidden' : '')],
                    'filter'         => false,
                    'mergeHeader'    => !$canBuy,
                    'value'          => function ($model, $key, $index, $widget) use ($canBuy, $creditBalance) {
                        if (!$canBuy || $model->itemPrice==0) {
                            return '';
                        }

                        $attrs = ['class'      => 'btn btn-primary btn-xs buymore',
                                  'data-price' => $model->itemPrice,
                                  //'onclick'    =>
                                  //    'ko.postbox.publish(\'buy.more\', $(this).parents("tr:eq(0)").attr("id"))'
                        ];

                        if (floatval($model->itemPrice)>0) {
                            $attrs['onclick'] = 'ko.postbox.publish(\'buy.more\', $(this).parents("tr:eq(0)").attr("id"))';
                        }


                        if ($creditBalance < $model->itemPrice || (floatval($model->itemPrice)==0)) {
                            $attrs['disabled'] = 'disabled';
                        }

                        return Html::button(print_r($canBuy,true).'Buy More', $attrs);
                    },
                    'width'          => '8em'
                ],

            ],
            'responsive'=>true,
            'hover'=>true,
            'condensed'=>true,
            'floatHeader'=>true,
            'floatHeaderOptions' => ['scrollingTop'           => 0,
                                     'useAbsolutePositioning' => true,
                                     'floatTableClass'        => 'kv-table-float slevel-float hidden-xs hidden-sm',
            ],

            'panel' => [
                'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i>&nbsp;Stock Items</h3>',
                'type'=>'info',
                'before'=>Html::a('Order Details', ['/'], ['class' => 'btn btn-success']) .
                '<div class="pull-right"><div class="summary">Credit Remaining: &pound;&nbsp;<span class="cbalance">' . number_format($credit['balance'], 2) . '</span>&nbsp;(of    &nbsp;&pound;&nbsp;' . number_format($credit['limit'], 2) . ')</div></div>',                                    //            //
            ],
        ]); Pjax::end(); ?>

    </div>

    <div id="keyoverlay">
        <h4>Please update the quantities you want to deliver:
            <a class="glyphicon glyphicon-ok-sign deliver" title="Deliver The Keys to Your Customer" data-toggle="tooltip"></a>
            <a class="glyphicon glyphicon-remove-sign cancel" title="Cancel This Delivery" data-toggle="tooltip"></a>
        </h4>

    </div>





