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
            'dataProvider'      => $recentOrderDataProvider,
            'filterModel'       => $recentOrderSearchModel,
            'toolbar'           => [], // RCH 20150227
            'options'           => ['id'                      => $divId . '-data', 'class' => 'grid-view bootstro',
                                    'data-bootstro-title'     => "Stock Room",
                                    'data-bootstro-content'   => '',
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
                [
                    'class'  => 'kartik\grid\ExpandRowColumn',
                    'value'  => function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detailUrl' => '/dashboard/recentorder',
                ],



                'accountNumber',
                'accountName',
                'po',
                [
                    'label'         => 'Order Date',
                    'attribute'     => 'created_at',
                    'format'         => ['date', 'php: d-M-Y'],
                    'contentOptions' => ['class' => 'hidden-xs hidden-sm text-center'],
                    'headerOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
                    'filterOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
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
                'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i>&nbsp;Recent Client Orders</h3>',
                'type'    => 'info',
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
