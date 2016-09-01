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

$this->title                   = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;

$selectedStockClass = 'selected-stock';

?>
    <div id="order-index">

        <?php 
        
        $flashes = Yii::$app->getSession()->getAllFlashes();
        
        foreach($flashes as $key => $value){

            if(!empty($key)){

                Alert::begin([
                    'options' => [
                        'class' => 'alert-' . $key,
                    ],
                ]);

                echo $value;

                Alert::end();
                
                
            }
            
        }

        echo GridView::widget([
            'tableOptions'      => ['id' => 'orderlistTable'],
            'pjax'              => false,
            'pjaxSettings'      => [
                'replace' => false
            ],
            'dataProvider'      => $dataProvider,
            'filterModel'       => $searchModel,
            'toolbar'           => [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-plus" style="color:#fff !important"></i> Create user', '/useradmin/create', ['title'=>'Create new account', 'class'=>'btn btn-success']),
                ],
            ],
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
                    'attribute' => 'id',
                    'contentOptions'=>['style'=>'width: 100px;'],
                ],
                [
                    'attribute' => 'username',
                    'contentOptions'=>['style'=>'width: 100px;'],
                ],
                [
                    'attribute' => 'email',
                    'contentOptions'=>['style'=>'width: 400px;'],
                ],
                [
                    'label' => 'Spending limit',
                    'value' => function($model){
                        return '£' . $model->spending_limit;
                    },
                    'contentOptions'=>['style'=>'width: 100px;'],
                ],
                [
                    'label' => 'Role',
                    'format' => 'raw',
                    'contentOptions'=>['style'=>'width: 200px;'],
                    'filter' => Html::activeDropDownList($searchModel, 'role_id', \yii\helpers\ArrayHelper::map(common\models\EDSRRole::find()->asArray()->all(), 'id', 'name'),['class'=>'form-control','prompt'=>'Filter by Role']),
                    'value' => function($model){
                        $role = common\models\EDSRRole::find()->where(['id'=>$model->role_id])->one();
                        return $role->name;
                    }
                ],
                        
                [
                    'label' => 'Status',
                    'format' => 'raw',
                    'contentOptions'=>['style'=>'width: 100px;'],
                    'filter' => Html::activeDropDownList($searchModel, 'status', ['1'=>'Enabled', '0'=>'Disabled', '2'=>'Other'], ['class'=>'form-control','prompt'=>'Filter by Status']),
                    'value' => function($model){
                        if($model->status == 0){
                            return Html::a('<span class="glyphicon glyphicon-remove" style="color:#F51E1E !important"></span>', 'javascript:void(0)', ['class'=>'EnableDisableUser', 'user-id'=>$model->id, 'user-status'=>'disabled', 'data-toggle'=>'tooltip', 'title'=>'Click to Enable or Disable user']);
                        } elseif($model->status == 1){
                            return Html::a('<span class="glyphicon glyphicon-ok text-success" style="color:#3c763d !important"></span>', 'javascript:void(0)', ['class'=>'EnableDisableUser', 'user-id'=>$model->id, 'user-status'=>'enabled', 'data-toggle'=>'tooltip', 'title'=>'Click to Enable or Disable user']);
                        } else {
                            return 'Other';
                        }
                    }
                ],
                        
                [
                    'label' => 'Shop Enabled',
                    'attribute' => 'shopEnabled',
                    'format' => 'raw',
                    'contentOptions'=>['style'=>'width: 100px;'],
                    'filter' => Html::activeDropDownList($searchModel, 'shopEnabled', ['1'=>'Enabled', '0'=>'Disabled'], ['class'=>'form-control','prompt'=>'Filter by Status']),
                    'value' => function($model){
                        if($model->shopEnabled == 0){
                            return Html::a('<span class="glyphicon glyphicon-remove" style="color:#F51E1E !important"></span>', 'javascript:void(0)', ['class'=>'EnableDisableShop', 'user-id'=>$model->id, 'shop-status'=>'disabled', 'data-toggle'=>'tooltip', 'title'=>'Click to Enable or Disable the shop for this user']);
                        } elseif($model->shopEnabled == 1){
                            return Html::a('<span class="glyphicon glyphicon-ok text-success" style="color:#3c763d !important"></span>', 'javascript:void(0)', ['class'=>'EnableDisableShop', 'user-id'=>$model->id, 'shop-status'=>'enabled', 'data-toggle'=>'tooltip', 'title'=>'Click to Enable or Disable the shop for this user']);
                        } else {
                            return 'Other';
                        }
                    }
                ],
                        
                [
                    'label' => 'Actions',
                    'format' => 'raw',
                    'contentOptions'=>['style'=>'width: 300px;'],
                    'value' => function($model){
                        $resendUrl = Yii::$app->urlManager->createUrl('/useradmin/resend?id='.$model->id.'');
                        $resendBtn = kartik\helpers\Html::a('<span class="glyphicon glyphicon-envelope"></span> Resend Invitation', $resendUrl, ['class'=>'btn btn-info']);
                        
                        $viewUrl = Yii::$app->urlManager->createUrl('/useradmin/viewuser?id='.$model->id.'');
                        $viewBtn = kartik\helpers\Html::a('<span class="glyphicon glyphicon-eye-open"></span> View User', $viewUrl, ['class'=>'btn btn-primary']);
                        
                        $editUrl = Yii::$app->urlManager->createUrl('/useradmin/update?id='.$model->id.'');
                        $editBtn = kartik\helpers\Html::a('<span class="glyphicon glyphicon-pencil"></span> Edit User', $editUrl, ['class'=>'btn btn-warning']);
                        
                        return $resendBtn . ' ' . $viewBtn . ' ' . $editBtn;
                    }
                ],
//                [
//                    'class'            => 'kartik\grid\CheckboxColumn',
//                    'rowSelectedClass' => $selectedStockClass,
//                ]
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
                'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i>&nbsp;Manage Users</h3>',
                'type'    => 'info',
//            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),
//            'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),
//            'showFooter'=>false
            ],
        ]);
      ?>

    </div>

<?php

$this->registerJs(<<< _EOF


    $('body').tooltip({
        selector: '[data-toggle=tooltip]'
    });

_EOF
);