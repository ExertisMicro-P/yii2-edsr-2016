<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\tabs\TabsX;
use kartik\alert\Alert;


$this->title                   = Yii::t('app', 'Exertis Digital Shop');
$this->params['breadcrumbs'][] = $this->title;


$creditBalance = $credit['balance'] ;
$selectedStockClass = 'selected-stock';

$user = \Yii::$app->user->getIdentity() ;
if (!$user || !$user->account) {
    return ;
}
$account = $user->account ;
$accountNumber = $account->customer_exertis_account_number ;

    ?>


            <?php
            /*
echo \kartik\widgets\Alert::widget([
    'options' => [
        'class' => 'alert-success lead',
    ],
//    'body' => '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> ISSUE: 2016-03-30 15:45 - '
  //  . 'We are currently experiencing an issue displaying the correct credit limit. We hope to have this resolved shortly. '
    //. 'Sorry for the inconvenience.',
    'body' => '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> RESOLVED: 2016-03-30 16:14 - '
    . 'Credit limits should now be okay. '
    . 'Sorry for the inconvenience.',
]);
             * 
             */
         
//echo \kartik\widgets\Alert::widget([
//    'options' => [
//        'class' => 'alert-success lead',
//    ],
////    'body' => '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> ISSUE: 2016-03-30 15:45 - '
//  //  . 'We are currently experiencing an issue displaying the correct credit limit. We hope to have this resolved shortly. '
//    //. 'Sorry for the inconvenience.',
//    'body' => '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> RESOLVED: 2016-04-27 14:48 - '
//    . 'Purchasing digital products failed. If you did not receive a key in your stockroom then your order was automatically cancelled and you have not been charged. '
//    . 'The issue has now been resolved. '
//    . 'Sorry for the inconvenience.',
//]);
   
            /*
echo \kartik\widgets\Alert::widget([
    'options' => [
        'class' => 'alert-success lead',
    ],
    /*
    'body' => '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> NOTICE: 2016-07-28 15:56 - '
    . 'EDSR will be unavailable from 18.00 on Thursday 28th July until 09.00 Friday 29th July'
    . ' while we perform essential service upgrades.'
    . 'Sorry for the inconvenience.',
     * 
     */
            /*
    'body' => '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> RESOLVED: 2016-07-29 08:53 - '
    . 'Our upgrades are now complete. All services are now available. '
    . 'Sorry for the inconvenience.',
]);  
             * 
             */

echo \kartik\widgets\Alert::widget([
    'options' => [
        'class' => 'alert-success lead',
    ],
    'body' => '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> NEW PRODUCTS: 2016-08-12 - '
    . 'We are pleased to announce that McAfee Security products are now available.'
]);  
                         
?>

    <div id="shopgrid-index" style="display:none">
        
        <?php

        echo Html::button('Help / Demo', ['class' => 'btn btn-primary', 'onclick' => '(function ( $event ) { bootstro.start(); })();']);
        
        echo '<div class="pull-right">Your spending limit is <b>£'.number_format(Yii::$app->user->identity->spending_limit, 2).'</b></div>';

        Pjax::begin();

        echo GridView::widget([
            'containerOptions'   => ['class' => 'hidden'],
            'tableOptions'       => ['id' => 'stocklevelTable'],
            // RCH 20151203 removed Pjax
            //'pjax'               => true, 
            //'pjaxSettings'       => [
              //  'replace' => false
            //],
            'dataProvider'       => $dataProvider,
            'filterModel'        => $searchModel,
            'toolbar'            => ['content' =>
                                         Html::a('<i class="glyphicon glyphicon-align-justify"></i>', ['#'], ['class' => 'selected showlist']) .
                                         Html::a('<i class="glyphicon glyphicon-th"></i>', ['#'], ['class' => 'showgrid']) .
                                         '<shop-list class="row"></shop-list>'
                                    ],
            'options'            => ['id'                      => 'shopgrid', 'class' => 'grid-view bootstro',
                                     'data-bootstro-title'     => "Shop",
                                     'data-bootstro-content'   => 'This is a list of available digital products',
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

            'rowOptions'         =>
                function ($model, $key, $index, $grid) {
                    return ['id'         => 'prow_' . $model->id . '-' . $model->id,
                            'data-digId' => $model->id,
                    ];
                },

            'pager'              => [
            ],
            'columns'            => [
                [

                    'format'         => 'raw',
                    'vAlign'         => 'middle',
                    'width'          => '100px',
                    'value'          => function ($model, $key, $index, $widget) {
                        //return $model->digitalProduct->getMainImageThumbnailTag(false);
                        return Html::img($model->getBoxShotUrl());
                    },
                    'contentOptions' => ['class' => 'text-center list-image']
                ],
                [
                    'attribute'      => 'partcode',
                    'contentOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'hidden-xs text-center partcode'],
                    'headerOptions'  => ['class' => 'hidden-xs text-center'],
                    'filterOptions'  => ['class' => 'hidden-xs text-center'],
                    'width'          => '10em'
                ],
                [
                    'attribute'      => 'productName',
                    'contentOptions' => ['class' => 'hidden-xs text-left description'],
                    'headerOptions'  => ['class' => 'hidden-xs'],
                    'filterOptions'  => ['class' => 'hidden-xs'],
                ],
                            
                            
                [

                    'format'         => 'raw',
                    'vAlign'         => 'middle',
                    'width'          => '100px',
                    'value'          => function ($model, $key, $index, $widget) {
                        //return $model->digitalProduct->getMainImageThumbnailTag(false);
                        return $model->publisher;
                    },
                    'contentOptions' => ['class' => 'text-center list-image']
                ],

                [
                    'header' => 'Tags',
                    'format'         => 'raw',
                    'vAlign'         => 'middle',
                    'width'          => '100px',
                    'value'          => function ($model, $key, $index, $widget) {
                        //return $model->digitalProduct->getMainImageThumbnailTag(false);
                        return '<span class="badge">'.implode('</span><span class="badge">',$model->getGenres()).'</span>' ;
                    },
                    'contentOptions' => ['class' => 'text-center list-image']
                ],

                            

                [
                    'label'          => 'Price',
                    'format'         => 'raw',
                    'value'          => function ($model, $key, $index, $widget) use($accountNumber,$account) {
                        
                        try{
                            $price = $model->getItemPrice($accountNumber) ;
                        } catch (Exception $e) {
                            // if we had issues, set price to 0 
                            // (We automatically hide the button if price is 0)
                            $price = '0.00'; 
                        }
                        
                        // RCH 20151005
                        // Hide Price in Retail View
                        if ($account->use_retail_view) {
                            return '<span title="Price hidden in Retail View. Change Settings to reveal">??.??</span>' .
                            '<input type="hidden" class="price" value="' . $price . '" />';
                        } else {
                        
                            return '&pound;' . number_format($price, 2) .
                            '<input type="hidden" class="price" value="' . $price . '" />';
                        }
                    },
                    'contentOptions' => ['class' => 'hidden-xs text-center price'],
                    'headerOptions'  => ['class' => 'hidden-xs text-center'],
                    'filterOptions'  => ['class' => 'hidden-xs text-center'],
                    'width'          => '8em'
                ],

                [
                    'header'    => '',
                    'format'    => 'raw',
                    'headerOptions'  => ['class' => !$canBuy ? 'hidden' : ''],
                    'contentOptions' => ['class' => 'text-center canbuy' . (!$canBuy ? ' hidden' : '')],
                    'filter'         => false,
                    'value'          => function ($model, $key, $index, $widget) use ($canBuy, $creditBalance, $accountNumber) {
                        $attrs = ['class'      => 'btn btn-info btn-xs viewdetails',
                                  'onclick' =>
                                      'ko.postbox.publish(\'view.details\', $(this).parents("tr:eq(0)").attr("id"))'
                        ] ;
                        return Html::button('Details', $attrs);
                    },
                    'width'          => '8em'

                ],

                [
                    'header'         => '',
                    'format'         => 'raw',
                    'headerOptions'  => ['class' => !$canBuy ? 'hidden' : ''],
                    'contentOptions' => ['class' => 'text-center canbuy' . (!$canBuy ? ' hidden' : '')],
                    'filter'         => false,
                    'mergeHeader'    => !$canBuy,
                    'value'          => function ($model, $key, $index, $widget) use ($canBuy, $creditBalance, $accountNumber, $isRetailView) {
                                                
                        try{
                            $price = $model->getItemPrice($accountNumber) ;
                        } catch (Exception $e) {
                            // if we had issues, set price to 0  
                            // (We automatically hide the button if price is 0)
                            $price = '0.00'; 
                        }
                        
                        if (!$canBuy || (floatval($price)==0)) {
                            return '';
                        }

                        $attrs = ['class'      => 'btn btn-primary btn-xs buymore',
                              'data-price' => $price,
                              //'onclick' =>
                              //    'ko.postbox.publish(\'buy.more\', $(this).parents("tr:eq(0)").attr("id"))'
                        ] ;
                        
                        if (floatval($price)>0) {
                            $attrs['onclick'] = 'ko.postbox.publish(\'buy.more\', $(this).parents("tr:eq(0)").attr("id"))';
                        }
                                              
                        if ($creditBalance < $model->getItemPrice($accountNumber) || (floatval($price)==0) ) {
                            $attrs['disabled'] = 'disabled';
                        }
                        
                        if(floatval($price) > Yii::$app->user->identity->spending_limit){
                            $attrs['disabled'] = 'disabled';
                        }
                        
                        if($isRetailView){
                            $result = Html::button('Buy & Print', $attrs);
                        } else {
                            $result = Html::button('Add to Basket', $attrs);
                        }

                        return $result;
                    },
                    'width'          => '8em'
                ],



            ],
            'responsive'         => true,
            'hover'              => true,
            'condensed'          => true,
            'floatHeader'        => true,
            'floatHeaderOptions' => ['scrollingTop'           => 0,
                                     'useAbsolutePositioning' => true,
                                     'floatTableClass'        => 'kv-table-float slevel-float hidden-xs hidden-sm',
//                                        'autoReflow' => true          // Doesn't help scrolling problem

            ],


            'panel'              => [
                'heading' =>   '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i>&nbsp;Available Products</h3>',
                'type'    => 'info',

                'before'  => '<div class="pull-right"><div class="summary">Credit Remaining: &pound;&nbsp;<span class="cbalance">' . number_format($credit['balance'], 2) . '</span>&nbsp;(of    &nbsp;&pound;&nbsp;' . number_format($credit['limit'], 2) . ')</div></div>',
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

$this->registerJs(<<< _EOF

        require(['stockroom'],
            function (sroom) {
                var stockroomHanderl = new keyHandler('#stocklevelTable', '$selectedStockClass', 8) ;
            }
        ) ;

        // -------------------------------------------------------------------
        // The following is activated after all pjax calls on the grid
        // -------------------------------------------------------------------
        $("#shopgrid-pjax").on("pjax:complete", function() {

            $('#shopgrid-container').addClass('hidden');
            ko.postbox.publish('shop.applyBindings', true) ;
            setTimeout(function () {
                document.initShop() ;
                ko.postbox.publish('shop.showlist', document.showingList) ;
            }, 100) ;
       });

    /*
     * Add a flag for detecting which view to show - grid or list
     */
    document.showingList = true ;

    document.initShop = function () {
        $('body').tooltip({
            selector: '[data-toggle=tooltip]'
        });

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

        $('.showlist').click(function() {
            ko.postbox.publish('shop.showlist', true) ;
            $(this).addClass('selected')
                    .css('pointer-events', 'none') ;
            $('.showgrid').removeClass('selected')
                    .css('pointer-events', 'auto') ;
        })
        $('.showgrid').click(function () {
            ko.postbox.publish('shop.showlist', false) ;
            $(this).addClass('selected')
                    .css('pointer-events', 'none') ;
            $('.showlist').removeClass('selected')
                    .css('pointer-events', 'auto') ;
        })

        $('.kv-thead-float').scroll();
        $('#shopgrid-index').fadeIn('slow');

        ko.postbox.publish('shop.initlist', true) ;
    }
    $('.kv-thead-float').scroll();
    $('#shopgrid-container').removeClass('hidden');

    var intval = setInterval(function () {
        if ($('.srname').length) {
            clearInterval(intval);

            document.initShop() ;
        }
    }, 20);

_EOF
);



