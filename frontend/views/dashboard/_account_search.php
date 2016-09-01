<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\AccountSearch $searchModel
 */


$this->title = Yii::t('app', 'Accounts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tab-pane fade in" id="account-search">

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions'         =>
            function ($model, $key, $index, $grid) {
                return ['data-accid' => $model->id];
            },

        'columns' => [

//            'eztorm_user_id',
            'customer_exertis_account_number',

            [
                'attribute' => 'customer',
                'label' => 'Account Name',
                'value' => 'customer.name',
            ],
            [
                'header'=>'# Stock Items',
                'value'=> 'stockItemsCount'
            ],

            [
                'attribute' => 'account',
                'label' => '',
                'value' => 'accountLogo',
                'format' => ['image',['width'=>'100']]
            ],

            [
                'attribute'=>'timestamp',
                'label'         => 'Date',
                'format'         => ['date', 'php: d-M-Y H:i:s'],
                'contentOptions' => ['class' => 'hidden-xs hidden-sm text-center'],
                'headerOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
                'filterOptions'  => ['class' => 'hidden-xs hidden-sm text-center'],
            ],

            [
                'label' => 'Has Account',
                'contentOptions' => ['class' => 'text-center'],
                'format'         => 'raw',
                'value' => function ($model) {
                    if ($model->getUsers()->where(['status' => 1])->count()) {
                        $icon = 'ok ok' ;
                    } else {
                        $icon = 'remove' ;
                    }
                    return '<span class="glyphicon glyphicon-' . $icon . '"></span>' ;
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view'  => function ($url, $model) { return '' ;},
                    'delete'  => function ($url, $model) { return '' ;},
                    'update' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-arrow-right"></span>',
                                            Yii::$app->urlManager->createUrl(['/dashboard/masquerade', 'id' => $model->id]), [
                                            'title' => Yii::t('yii', 'Masquerade'),
                                            'class' => 'masquerade'
                        ]);}

                ],
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>true,

        'toolbar'            => [], // RCH 20150227 - hides the ALl and Export buttons

        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>


    <!-- Default bootstrap modal example -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="bm-save">Save changes</button>
                </div>
            </div>
        </div>
    </div>


</div>
<?php
$this->registerJs(<<< _EOF
    $('#account-search').off('click.masq', 'a.masquerade') ;

    $('#account-search').on('click.masq', 'a.masquerade', function() {
        var accId = $(this).closest('tr').data('accid') ;

        $('#account-search .kv-grid-table').addClass('kv-grid-loading')

        $('#bm-save').hide() ;
        $("#myModal").find(".modal-body").load('/dashboard/masqueradecheck?id=' + accId)
            .end()
            .modal({backdrop: 'static', keyboard: false});

        return false ;
     }) ;

     $('#myModal').on('hidden.bs.modal', function () {
        $('#account-search .kv-grid-table').removeClass('kv-grid-loading')
        $('#bm-save').show() ;
     }) ;

_EOF
);
