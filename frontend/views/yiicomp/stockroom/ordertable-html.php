<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\tabs\TabsX;

/**
 * @var yii\web\View                  $this
 * @var yii\data\ActiveDataProvider   $dataProvider
 * @var common\models\StockroomSearch $searchModel
 */

$this->title                   = Yii::t('app', 'Stockrooms');
$this->params['breadcrumbs'][] = $this->title;


$selectedStockClass = 'selected-stock';

?>
    <div id="stockroom-index">

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


        <?= Html::button('Help / Demo', ['class' => 'btn btn-primary', 'onclick' => '(function ( $event ) { bootstro.start(); })();']); ?>


        <?php Pjax::begin();

        echo GridView::widget([
            'tableOptions'      => ['id' => 'stocklevelTable'],
            'pjax'              => true,
            'pjaxSettings'      => [
                'replace' => false
            ],
            'dataProvider'      => $dataProvider,
            'filterModel'       => $searchModel,
            'toolbar'           => [], // RCH 20150227
            'options'           => ['id'                      => 'stockLevels', 'class' => 'grid-view bootstro',
                                    'data-bootstro-title'     => "Stock Room",
                                    'data-bootstro-content'   => 'All your digital products get delivered into your Stock Room. Click on the checkboxes to choose products you want to deliver e.g. by email to your customer.',
                                    'data-bootstro-width'     => "600px",
                                    'data-bootstro-placement' => "top",
                                    'data-bootstro-step'      => "0",
                                    'data-original-title'     => "", 'title' => ""
            ],
            'toggleDataOptions' => [
                'all' => [
                    'icon'  => '',
                    'label' => ''
                ],
            ],
//        'export'    => false,             // Supposed to hide the export button, but causes javascript error

            'rowOptions'        =>
                function ($model, $key, $index, $grid) {
                    return ['id' => 'prow_' . $model->digitalProduct->id];
                },

            'pager'             => [
            ],
            'columns'           => [

                [
                    'class'  => 'kartik\grid\ExpandRowColumn',
                    'value'  => function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detail' => function ($model, $key, $index, $column) {


                        $items = [
                            [
                                'label'   => '<i class="glyphicon glyphicon-home"></i> Product Details',
                                'content' => Yii::$app->controller->renderPartial('/yiicomp/stockroom/_product-details', ['model' => $model]),
                                'active'  => true
                            ],
                            [
                                'label'   => '<i class="glyphicon glyphicon-user"></i> Order History',
                                'content' => "Order History",
//                            'linkOptions'=>['data-url'=>\yii\helpers\Url::to(['/yiicomp/stockroom/orders?pid=' . $model->productcode])]
                            ],
                        ];


                        return TabsX::widget([
                            'items'        => $items,
//                        'position'=>TabsX::POS_RIGHT,
                            'bordered'     => true,
                            'sideways'     => true,
                            'encodeLabels' => false,
                            'align'        => TabsX::ALIGN_RIGHT,
                        ]);
                    },
                ],

                array(
                    //'header' => 'Photo',
                    'format' => 'raw',
                    'vAlign' => 'middle',

                    'value'  => function ($model, $key, $index, $widget) {
                        return $model->digitalProduct->getMainImageThumbnailTag();
                    },
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
                    'attribute'      => 'description',
                    'contentOptions' => ['class' => 'hidden-xs hidden-sm'],
                    'headerOptions'  => ['class' => 'hidden-xs hidden-sm'],
                    'filterOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
                ],
                [
                    'header' => 'Quantity',
                    'format' => 'raw',
                    'value'  => function ($model, $key, $index, $widget) {
                        return '<span class="lead">' . $model->totalAvailableofThisProduct() . '</span>';
                    }
                ],
                [
                    'header'         => '',
                    'format'         => 'raw',
                    'headerOptions'  => ['class' => !$canBuy ? 'hidden' : ''],
                    'contentOptions' => ['class' => 'text-center' . (!$canBuy ? ' hidden' : '')],
                    'filter'         => false,
                    'mergeHeader'    => !$canBuy,
                    'value'          => function ($model, $key, $index, $widget) use ($canBuy) {
                        if (!$canBuy) {
                            return '';
                        }
                        
                        if(!Yii::$app->user->identity->shopEnabled){
                            return '';
                        }

                        return Html::button(print_r($canBuy,true).'Buy More', ['class' => 'btn btn-primary btn-xs', 'onclick' => 'alert("Feature coming soon")']) . '</div>';
                    }


                ],

                [
                    'class'            => 'kartik\grid\CheckboxColumn',
                    'rowSelectedClass' => $selectedStockClass,
                    'checkboxOptions'  => ['title' => 'Select to add to basket'],
                ]
            ],
            'responsive'        => true,
            'hover'             => true,
            'condensed'         => true,
            'floatHeader'       => true,
            'floatHeaderOptions'=> ['scrollingTop' => 0,
                                'floatTableClass'       => 'kv-table-float slevel-float',
                                'useAbsolutePositioning' => false,
//                                'autoReflow' => true          // Doesn't help scrolling problem
            ],


            'panel'             => [
                'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i>&nbsp;Stock Items</h3>',
                'type'    => 'info',
//            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),
//            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),
//            'showFooter'=>false
            ],
        ]);
        Pjax::end(); ?>

    </div>

    <div id="keyoverlay">
        <h4>Please update the quantities you want to deliver:
            <a class="glyphicon glyphicon-ok-sign deliver" title="Deliver The Keys to Your Customer"
               data-toggle="tooltip"></a>
            <a class="glyphicon glyphicon-remove-sign cancel" title="Cancel This Delivery" data-toggle="tooltip"></a>
        </h4>

    </div>


<?php


// ---------------------------------------------------------------------------
// todo ** Move the following into an external js file
// ---------------------------------------------------------------------------
$this->registerJs(<<< _EOF

   $("document").ready(function(){
        require(['stockroom'],
            function (sroom) {
                var stockroomHanderl = new keyHandler('#stocklevelTable', '$selectedStockClass') ;
            }
        ) ;

        // -------------------------------------------------------------------
        // The following only gets tripped on random occasions (ususally the first)
        // -------------------------------------------------------------------
        $("#stockLevels-pjax").on("pjax:complete", function() {


       });
    });


    $('body').tooltip({
        selector: '[data-toggle=tooltip]'
    });


    $(function(){
        $("[data-toggle=popover]").popover({
            viewport: '#stockLevels',
            html : true,
            content: function() {
              var content = $(this).attr("data-popover-content");
              return $(content).children(".popover-body").html();
            },
            title: function() {
              var title = $(this).attr("data-popover-content");
              return $(title).children(".popover-heading").html();
            }
        });
    });


_EOF
);



