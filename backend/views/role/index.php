<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\RoleSearch $searchModel
 */

$this->title = Yii::t('app', 'Roles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-index">
    <div class="page-header">
            <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Role',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            ['attribute'=>'create_time','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],
            ['attribute'=>'update_time','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],

    [
        'attribute' => 'can_admin',
        'class' => '\kartik\grid\BooleanColumn',
        'trueLabel' => 'Yes',
        'falseLabel' => 'No'
    ],
    [
        'attribute' => 'can_setupuseremail',
        'class' => '\kartik\grid\BooleanColumn',
        'trueLabel' => 'Yes',
        'falseLabel' => 'No'
    ],
    [
        'attribute' => 'can_user',
        'class' => '\kartik\grid\BooleanColumn',
        'trueLabel' => 'Yes',
        'falseLabel' => 'No'
    ],

    [
        'attribute' => 'can_customer',
        'class' => '\kartik\grid\BooleanColumn',
        'trueLabel' => 'Yes',
        'falseLabel' => 'No'
    ],

    [
        'attribute' => 'can_monitor_sales',
        'class' => '\kartik\grid\BooleanColumn',
        'trueLabel' => 'Yes',
        'falseLabel' => 'No'
    ],

    [
        'attribute' => 'can_buy_for_customer',
        'class' => '\kartik\grid\BooleanColumn',
        'trueLabel' => 'Yes',
        'falseLabel' => 'No'
    ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                'update' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['role/view','id' => $model->id,'edit'=>'t']), [
                                                    'title' => Yii::t('yii', 'Edit'),
                                                  ]);}

                ],
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>true,




        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),                                                                                                                                                          'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>

</div>
