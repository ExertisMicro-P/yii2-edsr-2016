<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\tabs\TabsX;


$this->title                   = Yii::t('app', 'Stockrooms');
$this->params['breadcrumbs'][] = $this->title;

$statusLookup = [
    ''                                             => 'All',
    \common\models\StockItem::STATUS_PURCHASED     => 'Available',
    \common\models\StockItem::STATUS_NOT_PURCHASED => 'Pending'
];

$creditBalance      = $credit['balance'];
$selectedStockClass = 'selected-stock';


$keyLimit = (int) \common\models\Account::findOne(['id' => Yii::$app->user->identity->account_id])->key_limit;
/*
echo \kartik\widgets\Alert::widget([
    'options' => [
        'class' => 'alert-success lead',
    ],
    'body' => '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> RESOLVED: 2016-04-08 11:40 - '
    . 'Emailing multiple codes should now work. Order History will now correctly show multiple keys also.'
    . 'Sorry for the inconvenience.',
]);
*/


/*
echo \kartik\widgets\Alert::widget([
    'options' => [
        'class' => 'alert-success lead',
    ],
    'body' => '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> INVESTIGATING: 2016-04-25 15:44 - '
    . 'Sending keys by email is not currently working. Please Cut & Paste them into your own emails, while we find a fix.'
    . 'Sorry for the inconvenience.',
]);
*/

//echo \kartik\widgets\Alert::widget([
//    'options' => [
//        'class' => 'alert-success lead',
//    ],
//    'body' => '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> RESOLVED: 2016-04-25 16:30 - '
//    . 'Sending keys by email should now be working again. Problem only affected accounts which did not have their Logo setup under Settings.'
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
    

$delivery = (int) \common\models\SessionDelivering::find()->where(['account_id' => Yii::$app->user->identity->account_id])->sum('quantity');

if($delivery >= $keyLimit) {
    $hidden = true;
} else {
    $hidden = false;
}

?>
    <div id="stockroom-index" style="display:none">
        <p>Try to keep your Stock Room empty. If you deliver or give a key to a customer, 
            select the checkbox and click on the [Items Picked for Delivery] button above. 
            From there you can email or print the keys, or just move them into your <?= Html::a('Order History', 'orders')?>.</p>
        <?php

        echo Html::button('Help / Demo', ['class' => 'btn btn-primary', 'onclick' => '(function ( $event ) { bootstro.start(); })();']);

        Pjax::begin();

        echo GridView::widget([
            'tableOptions'       => ['id' => 'stocklevelTable'],
            'pjax'               => true,
            'pjaxSettings'       => [
                'replace' => false
            ],
            'dataProvider'       => $dataProvider,
            'filterModel'        => $searchModel,
            'toolbar'            => [], // RCH 20150227
            'options'            => ['id'                      => 'stockLevels', 'class' => 'grid-view bootstro ordertable',
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
//        'export'    => false,             // Supposed to hide the export button, but causes javascript error of $(...).tooltip is not a function

            'rowOptions'         =>
                function ($model, $key, $index, $grid) {
                    return ['id'         => 'prow_' . $model->digitalProduct->id . '-' . $model->id,
                            'data-digId' => $model->digitalProduct->id,
                            'class'      => $model->status == \common\models\StockItem::STATUS_NOT_PURCHASED ? 'disabled' : ''
                    ];
                },

            'pager'              => [
            ],
            'columns'            => [

                array(
                    //'header' => 'Photo',
                    'format'         => 'raw',
                    'vAlign'         => 'middle',
                    'width'          => '100px',
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
                    'attribute'      => 'digitalProduct.description',
                    'contentOptions' => ['class' => 'text-center'],
                    'contentOptions' => ['class' => 'hidden-xs hidden-sm text-center'],
                    'headerOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
                    'filterOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
                    //'width'          => '10em'
                ],
                        /*
                [
                    'attribute'      => 'productName',
                    'contentOptions' => ['class' => 'hidden-xs xhidden-sm'],
                    'headerOptions'  => ['class' => 'hidden-xs xhidden-sm'],
                    'filterOptions'  => ['class' => 'hidden-xs xhidden-sm text-center'],
                ],
                         * 
                         */
                [
                    'attribute' => 'orderdetailspo',
                    'label'     => 'Your Ref',
//                    'contentOptions' => ['class' => 'hidden-xs hidden-sm text-center'],
//                    'headerOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
//                    'filterOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
                ],
                [
                    //'attribute' => 'sop',
                    //'header'         => '',
                    'format'         => 'raw',
                    'label'     => 'Exertis #',
                    'headerOptions'  => ['class' => 'col-md-1'],
                    'value'          => function ($model, $key, $index, $widget) use ($canBuy, $creditBalance) {
                        return implode('-',[$model->sop,$model->id]);
                    }
                ],

                [
                    'attribute'      => 'timestamp_added',
                    'label'          => 'Date Added',
                    'format'         => ['date', 'php: d-M-Y'],
                    'contentOptions' => ['class' => 'text-center hidden-xs hidden-sm'],
                    'headerOptions'  => ['class' => 'hidden-xs hidden-sm'],
                    'filterOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
                    'width'          => '10em'
                ],

                        

                [
                    'header'         => '',
                    'format'         => 'raw',
                    'headerOptions'  => ['class' => !$canBuy ? 'hidden' : ''],
                    'contentOptions' => ['class' => 'text-center' . (!$canBuy ? ' hidden' : '')],
                    'filter'         => false,
                    'mergeHeader'    => !$canBuy,
                    'value'          => function ($model, $key, $index, $widget) use ($canBuy, $creditBalance) {
                        //if (!$canBuy || $model->itemPrice==0) {
                        //    return '';
                        //}

                        $attrs = ['class'      => 'btn btn-primary btn-xs buymore',
                                  'data-price' => $model->itemPrice,
                                  //'onclick'    =>
                                      //'ko.postbox.publish(\'buy.more\', $(this).parents("tr:eq(0)").attr("id"))'
                        ];

                        if (floatval($model->itemPrice)>0) {
                            $attrs['onclick'] = 'ko.postbox.publish(\'buy.more\', $(this).parents("tr:eq(0)").attr("id"))';
                        }

                        if ($creditBalance < $model->itemPrice || (floatval($model->itemPrice)==0 || !$canBuy)) {
                            $attrs['disabled'] = 'disabled';
                        }
                        
                        if(!Yii::$app->user->identity->shopEnabled){
                            return '';
                        }
               
                        //return Html::button(print_r($model->itemPrice,true).'XBuy More', $attrs);
                        return Html::button('Buy More', $attrs);
                    },
                    'width'          => '8em'
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
                    'label'          => 'Status',
                    'attribute'      => 'status',
                    'contentOptions' => ['class' => 'hidden-xs hidden-sm text-center'],
                    'headerOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
                    'filterOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],

                    //'filterType'     => GridView::FILTER_SELECT2, // RCH20150622 temp get rid of select2 issue
                    //
//            'filter'=>ArrayHelper::map(Author::find()->orderBy('name')->asArray()->all(), 'id', 'name'),
                    'filter'         => $statusLookup,
//            'filterWidgetOptions'=>[
//                'pluginOptions'=>['allowClear'=>true]
//
//            ],
//            'filterInputOptions' => [
//                'placeholder'=>'Any author',
//                'readonly'  => true
//            ],

                    'format'         => 'raw',
                    'value'          => function ($model, $key, $index, $widget) use ($statusLookup) {
                            switch ($model->spare) {
                                case common\models\StockItem::KEY_HIDDEN_FROM_ALL_EXCEPT_RUSSELL:
                                    return 'TEST_HIDDEN_FROM_USER ('.$model->id.')';
                                    break;
                                default:
                                    return $statusLookup[$model->status];
                            } // switch

                        }
                ],

                [

                    'class'            => 'kartik\grid\CheckboxColumn',
                    'rowSelectedClass' => $selectedStockClass,
                    'hidden' => $hidden,
                    'checkboxOptions'  => function ($model, $key, $index, $column) {
                        return [
                            'title'       => 'Select to add to delivery',
                            'data-toggle' => 'tooltip',
                            'disabled'    => $model->status == 'NOT PURCHASED'
                        ];
                    },

                ]
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
                'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i>&nbsp;Stock Items</h3>',
                'type'    => 'info',

                'before'  => Html::a('Stock Summary', ['summary'], ['class' => 'btn btn-success']) .
                    '<div class="pull-right"><div class="summary">Credit Remaining: &pound;&nbsp;<span class="cbalance">' . number_format($credit['balance'], 2) . '</span>&nbsp;(of    &nbsp;&pound;&nbsp;' . number_format($credit['limit'], 2) . ')</div></div>',                                    //            //
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
                // -----------------------------------------------------------
                // Need to wait until the <stock-selected> tag has been processed
                // and so loaded selected.js
                // -----------------------------------------------------------
                var sInt = setInterval(function () {
                    if ($('*', 'stock-selected').length > 0) {
                        clearInterval(sInt) ;
                        var stockroomHanderl = new keyHandler('#stocklevelTable', '$selectedStockClass', 9) ;
                    }
                }, 10) ;
            }
        ) ;

        // -------------------------------------------------------------------
        // The following only gets tripped on random occasions (ususally the first)
        // -------------------------------------------------------------------
        $("#stockLevels-pjax").on("pjax:end", function() {

            setTimeout(function () {
                document.initGrid() ;
            }, 100) ;
       });


    document.initGrid = function () {
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

        $('.kv-thead-float').scroll();
        $('#stockroom-index').fadeIn('slow');
    }

    var intval = setInterval(function () {
        if ($('.srname').length) {
            clearInterval(intval);
             document.initGrid() ;
           // $('#stockroom-index').fadeIn('slow');
        }
    }, 20)


_EOF
);



