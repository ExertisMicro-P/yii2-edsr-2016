<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var common\models\gauth\GAUserSearch $searchModel
 */

$this->title = 'Gausers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gauser-index">
    <div class="page-header">
            <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a('Create Gauser', ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'role_id',
            'status',
            'email:email',
//            'new_email:email',
            'username',
//            'password',
//            'auth_key',
//            'api_key',
            'login_ip',
            ['attribute'=>'login_time'],
//            'create_ip',
            ['attribute'=>'create_time'],
//            ['attribute'=>'update_time','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],
//            ['attribute'=>'ban_time','format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],
//            'ban_reason',
//            'uuid',
            'account_id',
            [
             'label'=>'Account',
             'format' => 'raw',
             'value'=>function ($data) {
                        if (!empty($data->account)) {
                            return Html::a($data->account->customer_exertis_account_number,Url::to(['account/view','id'=>$data->account_id]), ['data-pjax'=>'0']);
                        }
                      },
             ],
            [
             'label'=>'Account Name',
             'format' => 'raw',
             'value'=>function ($data) {
                        if (!empty($data->account)) {
                            return Html::a($data->account->customer->name,Url::to(['customer/view','id'=>$data->account->customer->id]), ['data-pjax'=>'0']);
                        }
                      },
             ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                'update' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['gauser/view','id' => $model->id,'edit'=>'t']), [
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
