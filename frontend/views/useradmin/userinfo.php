<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Useradmin */

$this->title = 'User info';
$this->params['breadcrumbs'][] = ['label' => 'Useradmins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//var_dump($activity); die();

?>
<div class="useradmin-view formbg">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <h3><?= Html::a('<< Back', '/useradmin') ?></h3>

    
    <?php
    
        echo DetailView::widget([
            'model'=>$model,
            'condensed'=>true,
            'hover'=>true,
            'mode'=>DetailView::MODE_VIEW,
            'panel'=>[
                'heading'=>'User # ' . $model->id,
                'type'=>DetailView::TYPE_PRIMARY,
            ],
            'attributes'=>[
                'email',
                'username',
                'spending_limit'
            ]
        ]);
        
        echo '<br><br>';
            
        echo GridView::widget([
            'pjax'              => true,
            'pjaxSettings'      => [
                'replace' => false
            ],
            'dataProvider'      => $dataProvider,
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

                'message',
                'timestamp'
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
