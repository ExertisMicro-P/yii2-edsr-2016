<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\tabs\TabsX;
use yii\bootstrap\Button;

/*
 * Load the terms and conditions first as they set the title and breadcrumb as
 * though we're on the legals page. Once loaded, we can reset it to ours
 */

frontend\assets\BasketAsset::register($this);
?>
    <!-- Modal -->
    <div id="termsmodal" class="container">

        <a href="#" onclick="$('#termsmodal').fadeOut(); $('#checkout').fadeIn();" class="btn btn-default pull-right">
            Return to the Checkout
        </a>
        <?php // include_once __DIR__ . '/terms.php';  ?>
        <?php echo $this->render('//site/legal') ; ?>
    </div>


<?php


$this->title                   = Yii::t('app', 'Checkout');
$this->params['breadcrumbs'][] = $this->title;
$grandTotal                    = 0;

?>




    <div id="Nowt" class="panel text-center" style="display:none">
        <p><br /></p>
        <h1>Your basket is now empty</h1>
        <?= Html::a('<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Return to Shop', ['/shop'], ['class'=>'btn btn-primary btn-lg']) ?>
        
        <p><br /></p>
    </div>

    <div id="allpurchased" class="panel text-center" style="display:none">
        <p><br /></p>
        <h1>Congratulations, your purchases are now complete.</h1>
        <p>You will shortly be returned to the shop, or click <a href="" >here</a> to return now</p>
        <p><br /></p>
    </div>

    <div id="checkout-index" style="display:none">
<?php

//        echo Html::button('Help / Demo', ['class' => 'btn btn-primary', 'onclick' => '(function ( $event ) { bootstro.start(); })();']);

        //                Pjax::begin();

        echo '<form action="/checkout/purchase" id="checkout-form">';
        echo '<h4 class="pleaseWait text-center"></h4>';
        echo GridView::widget([
            'tableOptions'       => ['id' => 'stocklevelTable'],
//            'pjax'               => true,
            'pjaxSettings'       => [
                'replace' => false
            ],
            'dataProvider'       => $dataProvider,
            'toolbar'            => [], // RCH 20150227
            'options'            => ['id'                      => 'checkout', 'class' => 'grid-view bootstro',
                                     'data-bootstro-title'     => "Stock Room",
                                     'data-bootstro-content'   => 'All your digital products get delivered into your Stock Room. Click on the checkboxes to choose products you want to deliver e.g. by email to your customer.',
                                     'data-bootstro-width'     => "600px",
                                     'data-bootstro-placement' => "top",
                                     'data-bootstro-step'      => "0",
                                     'data-original-title'     => "", 'title' => ""],
            'toggleDataOptions'  => [
                'all' => [
                    'icon'  => '',
                    'label' => ''
                ],
            ],
//        'export'    => false,             // Supposed to hide the export button, but causes javascript error

//            'rowOptions'         =>
//                function ($model, $key, $index, $grid) {
//                    return ['id'         => 'prow_' . $model->digitalProduct->id . '-' . $model->id,
//                            'data-digId' => $model->digitalProduct->id,
//                            'class'      => $model->status == \common\models\StockItem::STATUS_NOT_PURCHASED ? 'disabled' : ''
//                    ];
//                },

            'columns'            => [

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

                        return '<input id="quantity" type="text" size="3" name="quant[' . $model->partcode . ']"
                                    class="price text-center"
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

//                [
//                    'class' => 'kartik\grid\CheckboxColumn',
//                    'rowSelectedClass' => $selectedStockClass,
//                    'checkboxOptions'  => function ($model, $key, $index, $column) {
//                        return [
//                            'title'    => 'Select to add to basket',
//                            'disabled' => $model->status == 'NOT PURCHASED'
//                        ];
//                    },
//                ]
            ],
            'responsive'         => true,
            'hover'              => true,
            'condensed'          => true,
//            'floatHeader'        => true,
            'floatHeaderOptions' => ['scrollingTop'           => 100,
                                     'useAbsolutePositioning' => false,
                                     'floatTableClass'        => 'kv-table-float slevel-float',
//                                        'autoReflow' => true          // Doesn't help scrolling problem

            ],

            'showPageSummary'    => true,
            'panel'              => [
                'heading'    => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i>&nbsp;Checkout</h3>',
                'type'       => 'info',
                'before'     =>//  Html::a('Stock Summary', ['summary'], ['class' => 'btn btn-success']) .
                    '<div class="pull-right"><div class="summary">Credit Remaining: &pound;&nbsp;<span class="cbalance">' . number_format($credit['balance'], 2) . '</span>&nbsp;(of    &nbsp;&pound;&nbsp;' . number_format($credit['limit'], 2) . ')</div></div>',                                    //            //
//                'before'     => Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),

                'after'      =>
                    '<div class="pull-right" id="porow"><h4>Please enter a purchase order number : <input type="text" name="po" size="10"
                            onkeydown="return chkPO(event)"
                            onkeyup="return setPO(event)" maxlength="27" /></h4></div>' .
                    '<div class="clearfix"></div>' .

                    '<div class="pull-right">

                        <h4>Please confirm you have <a href="#" onclick="$(\'#checkout\').fadeOut();$(\'#termsmodal\').fadeIn()">read</a> the terms and conditions:&nbsp;
                            <span class="pull-right">
                                <span class="onoffswitch" rel="tooltip" title="Slide to Read once you have agreed to the terms and conditions">
                                    <input type="hidden" name="readtsandcs" value="0" />
                                    <input type="checkbox" name="readtsandcs" class="onoffswitch-checkbox" id="readtsands" onchange="setPO()">
                                    <label class="onoffswitch-label" for="readtsands">
                                        <div class="onoffswitch-inner" data-swchon-text="Read" data-swchoff-text="Unread" style=""></div>
                                        <div class="onoffswitch-switch"></div>
                                    </label>
                                </span>
                            </span>
                        </h4>
                    </div>' .
                    '<div class="clearfix"></div>' .
                    '<div class="pull-left">' .
                    Html::a('<i class="glyphicon glyphicon-repeat"></i> Continue Shopping',
                        ['/shop'],
                        ['class'    => 'btn btn-success',
                         'id'       => 'continue-button'
                        ]) .
                    '</div>' .
                    '<div class="pull-right">' .
                    Html::a('<i class="glyphicon glyphicon-repeat"></i> Purchase',
                        ['index'],
                        ['class'    => 'btn btn-info',
                         'disabled' => true,
                         'id'       => 'purchase-button',
                         'onclick'  => 'return purchase();'
                        ]) .
                    '</div><div class="clearfix"></div>',
                'showFooter' => false
            ]
        ]);
        //                Pjax::end();
        ?>

        </form>
    </div>


    <script type="text/html" id="confirm-removeall">
        Click to <a href="#">Remove Item</a>
    </script>

<?php

$js = <<< _EOF

    document.chkPO = function (event) {
        var keyCode = event.keyCode ;
        var shiftKey = event.shiftKey ;

        // -------------------------------------------------------------------
        // Allow: backspace, delete, tab, escape, and enter
        // and hyphen, slash and fullstop, plus any alphanumeric
        // Upper/lower is defined by a shiftKey setting so don't check explictly
        // though this does allow all control key presses
        // -------------------------------------------------------------------
        if (keyCode == 46 || keyCode == 8 || keyCode == 9 ||
            keyCode == 27 || keyCode == 13 ||
            (!shiftKey && keyCode >= 48 && keyCode <= 57) ||        // 48 == 0, 57 == 9
            (keyCode >=65  && keyCode <= 90) ||                     // 65 == a, 90 == z
            (shiftKey && keyCode == 189) ||                         // Underscore
            (shiftKey && keyCode == 186) ||                         // Colon
            keyCode == 37 || keyCode == 39                          // Left/right arrows
           ){
                return true ;
        }
        event.preventDefault();
        event.stopPropagation();
        return false;
    }

    document.setPO = function (evnt) {
        var po = $.trim($('input[name="po"]').val().toUpperCase()) ;

        // -------------------------------------------------------------------
        // If any form of printable character, convert the display to uppercase
        // Do a double check as this still allows some keys prohibited above,
        // and don't want to clear any selection if avoidable.
        // -------------------------------------------------------------------
        if (evnt && evnt.keyCode >= 48) {
            if (po !== $('input[name="po"]').val()) {
                $('input[name="po"]').val(po) ;
            }
        }
        
        if((po.length == 0 || !$('input[type=checkbox][name=readtsandcs]').prop('checked'))) {
            $('#purchase-button').attr('disabled', true);
        } else {
            $('#purchase-button').removeAttr("disabled");
        }
    }


    document.purchase = function() {
        var form = $('#checkout-form') ;

        if (!$('input[type=checkbox][name=readtsandcs]').prop('checked')) {
            return false ;
        }

        $("#checkout-container").addClass('kv-grid-loading')

        $.post(form.attr('action'), form.serialize())
            .done(function (response) {
                $("#checkout-container").removeClass('kv-grid-loading')
                response = $.parseJSON(response) ;
                if (response.status) {

                    $('#checkout').fadeOut() ;
                    $('#allpurchased').fadeIn()

                    setTimeout(function () {
                        document.location.href = '/' ;
                    }, 1500) ;

                } else if (response.errors) {
                    $('#porow').showMessageBelow(response.errors) ;
                } else {
                    $('#porow').showMessageBelow(response.error) ;
                }
              //  document.location.href = '/' ;
            })
            .fail (function (xhr) {
                $("#checkout-container").removeClass('kv-grid-loading')
                console.log(xhr) ;
            }) ;
        return false ;
    }

    /**
    * PRICE CHANGE
    * ============
    */
    $('#checkout-index input.price').change(function () {
        var input = $(this) ;
        var td = $(this).parent() ;
        var pcode = input.data('partcode') ;
        var quant = parseInt(input.val()) ;

        var cost = input.data('price') ;
        var curTotal = input.data('total') ;
        var gTotal = $('#grandTotal') ;
        var gTValue = gTotal.data('total') ;

        if (isNaN(quant)) {
            quant =  0;
        }
        ko.postbox.publish('product.quantity.change', {pcode: pcode, quant: quant}) ;
        var subs = ko.postbox.subscribe('product.quantity.' + pcode, function (newValue) {
            quant = newValue.quantity ;
            //cost  = newValue.cost ;
            //input.val(quant) ;
        
            var total = Number(quant * cost) ;
            var totalFormatted  = '&pound;&nbsp;' + total.formatMoney(2) ;

            td.next().html(totalFormatted) ;

            gTValue += (total-curTotal) ;
            gTotal.text(gTValue.formatMoney(2)) ;
            //gTotal.data('total', gTValue) ; // RCH Fix total calculation
gTotal.attr('data-total', gTValue);

//            subs.dispose() ;
        }, null, true) ;
    })


    $('#checkout-index a.remall').popover({
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


//    $('#checkout-index a.remall').click(function () {
//        var input = $(this) ;
//        var td = $(this).parent() ;
//
//        td.parents('tr:eq(0)').find('input.price').val(0).change() ;
//    }) ;

    var intval = setInterval(function () {
        if ($('.srname').length) {
            clearInterval(intval);
            $('#checkout-index').fadeIn();
        }
    }, 20)

_EOF;


$this->registerJs($js);




