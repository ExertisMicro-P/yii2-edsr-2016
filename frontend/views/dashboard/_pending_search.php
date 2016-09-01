<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\tabs\TabsX;

$selectedStockClass = 'sales-rep-order';
$grandTotal                    = 0;
?>



    <div class="tab-pane<?= isset($active) && $active ? ' active' : ''?>" id="<?= $divId ?>">

        <?php Pjax::begin();

        echo GridView::widget([
            'tableOptions'      => ['id' => '<?= $divId ?>-table'],
            'pjax'              => true,
            'pjaxSettings'      => [
                'replace' => false,
                'id'    => 'fred',
            ],
            'dataProvider'      => $pendingDataProvider,
            'filterModel'       => $pendingSearchModel,
            'toolbar'           => [], // RCH 20150227
            'options'           => ['id'                      => $divId . '-data', 'class' => 'grid-view bootstro',
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

            'columns'           => [

                'accountName',

                array(
                    //'header' => 'Photo',
                    'format'         => 'raw',
                    'vAlign'         => 'middle',
                    'width'          => '100px',
                    'value'          => function ($model, $key, $index, $widget) {
                        return $model->product->getMainImageThumbnailTag();
                    },
                    'contentOptions' => ['class' => 'text-center']
                ),
                [
                    'attribute'      => 'partcode',
                    'contentOptions' => ['class' => 'text-center']

                ],
                [
                    'label'              => 'description',
                    'attribute'          => 'description',
                    'headerOptions'      => ['class' => 'hidden-xs hidden-sm'],
                    'contentOptions'     => ['class' => 'hidden-xs hidden-sm'],
                    'pageSummaryOptions' => ['class' => 'hidden-xs hidden-sm text-right']
                ],
                [
                    'label'              => 'Cost Price',
                    'format'             => 'raw',
                    'value'              => function ($model, $key, $index, $widget) {

                        return '&pound;&nbsp;' . number_format($model->cost, 2);
                    },
                    'headerOptions'      => ['class' => 'hidden-xs hidden-sm text-right'],
                    'contentOptions'     => ['class' => 'hidden-xs hidden-sm text-right'],
                    'pageSummary'        => 'Grand Total',
                    'pageSummaryOptions' => ['class' => 'hidden-xs hidden-sm text-right']
                ],

                [
                    'label'          => 'Quantity',
                    'format'         => 'raw',
                    'value'          => function ($model, $key, $index, $widget) {

                        return '<input type="text" size="3" name="quant[' . $model->partcode . ']"
                                    maxlength="3" class="price text-center"
                                    value="' . $model->quantity . '"
                                    data-partcode="' . htmlspecialchars($model->partcode) . '"
                                    data-price="' . $model->cost . '"
                                    data-total="' . ($model->quantity * $model->cost) . '"
                                    />';
                    },
                    'headerOptions'  => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'pageSummary'    => false,
                ],
                [
                    'label'              => 'Total',
                    'format'             => 'raw',
                    'value'              => function ($model, $key, $index, $widget) use ($grandTotal) {
                        $total = $model->quantity * $model->cost;
                        $grandTotal += $total;                          // Doesn't work. Always results in 0

                        return '&pound;&nbsp;' . number_format($total, 2);
                    },
                    'headerOptions'      => ['class' => 'text-right'],
                    'contentOptions'     => ['class' => 'text-right'],
                    'pageSummary'        => function ($summary, $data, $widget) {
                        $grandTotal = 0;
                        foreach ($data as $value) {
                            $value = str_replace(['&pound;&nbsp;', ','], '', $value);
                            $grandTotal += floatval($value);
                        }

                        return '&pound;&nbsp;<span id="grandTotal" data-total="' . $grandTotal . '">' . number_format($grandTotal, 2) . '</span>';
                    },
                    'pageSummaryOptions' => ['class' => 'text-right']

                ],

                [
                    'format'         => 'raw',
                    'value'          => function ($model, $key, $index, $widget) {
                        return '<a href="#" class="remall" rel="tooltip" title="Totally remove this item" tabindex="-1">
                                    <img src="/img/delete.png"></a>';
                    },
                    'contentOptions' => ['class' => 'text-center'],
                ],


                [
                    'class' => 'yii\grid\ActionColumn',
                    'buttons' => [
                        'view'  => function ($url, $model) { return '' ;},
                        'delete'  => function ($url, $model) { return '' ;},
                        'update' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-arrow-right"></span>',
                                Yii::$app->urlManager->createUrl(['/dashboard/masquerade', 'id' => $model->created_by]), [
                                    'title' => Yii::t('yii', 'Masquerade'),
                                    'class' => 'masquerade'
                                ]);}

                    ],
                ],
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
                'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i>&nbsp;Pending Client Orders</h3>',
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



    <script type="text/html" id="confirm-removeall">
        Click to <a href="#">Remove Item</a>
    </script>

<?php



$js = <<< _EOF

    $('#sales-rep-order-table a.remall').popover({
        placement:  'auto right',
        trigger: 'hover',
        delay: { show: 200, hide: 3000 },
        html : true,
        content: function() {
            $('.remall').not(this).popover('hide');
            return $('#confirm-removeall').html();
        }
    }).on('shown.bs.popover', function() {
        // set what happens when user clicks on the button
        var popover = $(this).parent().find('.popover') ;
        $("a", popover).data('trigger', $(this)).on('click', function(){
            var row = $(this).data('trigger').parents('tr:eq(0)') ;
            var table = row.parents('table:eq(0)') ;
            row.find('input.price').val(0).change() ;
            row.fadeOut(750, function () {
                row.remove();
                if ($('tbody tr', table).length == 0) {
                    $('#checkout-index').fadeOut() ;
                    $('#Nowt').fadeIn() ;
                }
            }) ;
        });
    });
_EOF;


$this->registerJs($js);
