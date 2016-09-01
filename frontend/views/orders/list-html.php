<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\tabs\TabsX;
use yii\bootstrap\Alert;

/**
 * @var yii\web\View                  $this
 * @var yii\data\ActiveDataProvider   $dataProvider
 * @var common\models\StockroomSearch $searchModel
 */
\frontend\assets\InstallKeyAsset::register($this);

$this->title                   = Yii::t('app', 'Order History');
$this->params['breadcrumbs'][] = $this->title;


$selectedStockClass = 'selected-stock';

$browser = $_SERVER['HTTP_USER_AGENT'];;

if(strpos($browser, 'Firefox') !== false){
    
    Alert::begin([
        'options' => [
            'class' => 'alert-warning',
            'style' => 'text-align:center'
        ],
    ]);

    echo '<h3>To print your key, please use Adobe PDF Reader.<br>';
    echo Html::a('Click here', 'https://get.adobe.com/uk/reader/'). ' to install, if you don\'t have it yet.</h3>';

    Alert::end();
    
}

?>
    <div id="order-index">

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <?php Pjax::begin();
        
        //die(print_r($dataProvider,true));
        
        echo GridView::widget([
            'tableOptions'      => ['id' => 'orderlistTable'],
            'pjax'              => true,
            'pjaxSettings'      => [
                'replace' => false
            ],
            'dataProvider'      => $dataProvider, // StockItem
            'toolbar'           => [],
            'options'           => ['id' => 'orderlist', 'class' => 'grid-view'],


            'toggleDataOptions' => [
                'all' => [
                    'icon'  => '',
                    'label' => ''
                ],
            ],
//        'export'    => false,             // Supposed to hide the export button, but causes javascript error


            'pager'             => [
            ],
            'columns'           => [

               [
                   'label' => 'Image',
                   'format' => 'raw',
                   'value' => function($data){
                        return Html::img($data->getBoxShotUrl(), ['class'=>'responsive', 'style'=>'width:75px;']);
                   }
               ],
               'productcode',
               [
                   'label' => 'Your Ref #',
                   'attribute' => 'status'
               ],
               [
                   'attribute' => 'digitalProduct.description',
                   'format' => 'raw',
                   'value' => function($data){
                        $showKey = Html::button('Show key', ['class' => 'btn btn-info _showKey', 'data-id'=>$data->id]);
                        return $data->digitalProduct->description . '<br>' . $showKey . '<p class="showKey-'.$data->id.' text-danger"></p>';
                   }
               ],
               [
                   'label' => 'PO',
                   'value' => function($data){
                        $po = common\models\Orderdetails::find()->where(['stock_item_id'=>$data->id])->one()->po;
                        
                        return strpos($po, "EDR") ? substr($po, 0, strpos($po, "EDR")) : $po; 
                   }
               ],
               //'emailedUser.id', // RCH 20160504
               'timestamp_added',
                       
               [
                   'label' => 'Actions',
                   'format' => 'raw',
                   'value' => function($data){
                       //yii\helpers\VarDumper::dump($data->eztorm_product_id, 99, true); die();
                         
                       $emailedItem = common\models\EmailedItem::find()->where(['stock_item_id'=>$data->id])->one();
                       
                       if($emailedItem){
                           $emailedItemId = $emailedItem->id;
                       } else {
                           $emailedItemId = 0;
                       }
                   
                        $urlParams = [
                            'euser'     => $data->send_email,
                            'eitem'     => $emailedItemId,
                            'onumber'   => substr($data->eztorm_order_id, 1),
                            'stockroom' => $data->stockroom_id,
                            'itemId'    => $data->id
                        ];
                   
                        $emailBtn = Html::button('<span class="glyphicon glyphicon-envelope"></span>',
                                                [
                                                    'rel' => Yii::$app->urlManager->createUrl(array_merge(['yiicomp/stockroom/viewkeys'], $urlParams)),
                                                    'class' => 'keyact btn btn-default',
                                                    'data-name' => '',
                                                    'data-email' => '',
                                                    'title' => 'Resend the key',
                                                    'onClick' => 'resendEmails(event)',
                                                    'data-toggle' => 'tooltip',
                                                    'data-original-title' => 'View the keys'
                                                ]
                                                );
                        
                        $catalogue = common\models\ZtormCatalogueCache::find()->where(['RealProductId'=>$data->eztorm_product_id])->exists();
                        $hidden = '';
                        if(!$catalogue){
                            $hidden = 'hidden';
                        }
                        
                        $printButton = Html::a('<span class="glyphicon glyphicon-print" style="color:#3A3A3A"></span>',
                                                [Yii::$app->urlManager->createUrl(array_merge(['printkeys/reprint'], ['pdfkeys' => $data->id]))],
                                                [
                                                    'target' => '_blank',
                                                    'class' => 'keyact btn btn-default ' . $hidden,
                                                    'title' => 'Re-print the key',
                                                    'data-pjax' => 0,
                                                    'data-toggle' => 'tooltip',
                                                    'data-original-title' => 'Re-print the key'
                                                ]
                                                );
                        
                   
                        return '<div class="btn-group-horizontal btn-group-SM" role="group" aria-label="krajee-book-detail-buttons">' . $emailBtn . ' ' . $printButton . '</div>';
                   }
               ]
            ],
            'responsive'        => true,
            'hover'             => true,
            'condensed'         => true,
            'floatHeader'       => true,
            'floatHeaderOptions' => ['scrollingTop'           => 0,
                                     'useAbsolutePositioning' => true,
                                     'floatTableClass'        => 'kv-table-float slevel-float hidden-xs hidden-sm',
//                                        'autoReflow' => true          // Doesn't help scrolling problem

            ],


            'panel'             => [
                'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i>&nbsp;Order History</h3>',
                'type'    => 'info',
//            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),
//            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),
//            'showFooter'=>false
            ],
        ]);
        Pjax::end(); ?>

    </div>



<script type="text/html" id="resendEmails">
    <div class="row row-centered" style="xdisplay:none"
         data-bind="slideVisible : showForm, deleteOnClose: 'tr:eq(0)', css: {'kv-grid-loading': resending}">
        <div class="col-xs-12 col-centered" id="emailKeys">

            <button type="button" class="close"
                    data-bind="click: closeForm"
                    xdata-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
            </button>

            <h3>You are about to re-send the key for your selected product</h3>
        </div>

        <form class="form-inline row-centered" role="form" action="#" data-bind="submit: emailToRecipient">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>"/>

            <div class="col-xs-12">Message Body</div>
            <div class="col-xs-2"></div>
            <div class="col-xs-8 center-block text-center">
                <textarea class="form-control" cols="80" rows="4" name="message" style="width: 100%"
                          data-bind="value: message"></textarea>
            </div>
            <div class="col-xs-2"></div>

            <div class="col-xs-12">
                <hr/>
            </div>
            <div class="col-xs-6">
                <div class="form-group required">
                    <label for="rname">Recipient's Name:</label>
                    <input type="text" class="form-control" data-bind="value: recipient, css: {err: badrname}"
                           name="rname" id="rname"/>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group required">
                    <label for="remail">Email address:</label>
                    <input type="email" class="form-control"
                           data-bind="value: email, css: {err: !emailOk()}, valueUpdate: 'afterkeydown'" name="email"
                           id="remail"/>
                </div>
                <div class="indic" data-bind="css: {ok: emailOk, bad : !emailOk()}"></div>
            </div>

            <div class="col-xs-12 text-center alert">
                <button type="submit" class="btn btn-default"
                        data-bind="attr:{disabled: invalid}, visible: !errormsg(),"><span
                        class="glyphicon glyphicon-envelope"></span> Send
                </button>
                <div class="alert alert-info fade in"
                     data-bind="text: errormsg, visible: errormsg, css: msgClass"
                ">
            </div>
    </div>

    </form>

    <div class="col-xs-12 row-centered form-group required">
        <br/>Fields marked <label></label> are required
    </div>
    <div class="col-xs-12 row-centered note">
        <!--                NOTE: The keys will be logged in a 'cupboard' identified by the email address.<br />
                                If this address has already been used, they will be added to an exising cupboard -->
    </div>
    </div>
</script>

<script type="application/javascript">
    function resendEmails(event) {
        var tkn     = $('meta[name="csrf-token"]').attr("content");
        var element = $(event.target);
        if (element[0].tagName != 'BUTTON') {
            element = element.parents('button:eq(0)');
        }

        // -------------------------------------------------------------------
        // Jquery uniqueId not working....
        // -------------------------------------------------------------------
        var id = element.attr('id');
        if (!id) {
            id = new Date().getTime();
            element.attr('id', id);
        }

        ko.postbox.publish('resend.emails', id);
    }
</script>


<?php

$this->registerJs(<<< _EOF


    $('body').tooltip({
        selector: '[data-toggle=tooltip]'
    });

_EOF
);
