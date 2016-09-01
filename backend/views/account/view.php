<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\file\FileInput;
use kartik\datecontrol\DateControl;
use yii\helpers\ArrayHelper;

use yii\widgets\Pjax;
use kartik\grid\GridView;

/**
 * @var yii\web\View $this
 * @var common\models\Account $model
 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

if($model->credit){
    $creditLimit = number_format($model->credit->overall_credit_limit, 2);
    $creditBalance = number_format($model->credit->available_credit, 2);
} else {
    $creditLimit = number_format(0, 2);
    $creditBalance = number_format(0, 2);
}

$adminUsers = \common\models\gauth\GAUser::find()->where(['role_id' => 2])->andWhere(['<', 'id', 11])->andWhere(['!=', 'account_id', $model->id])->all();

\backend\assets\EnableDisableAsset::register($this);
?>
<div class="account-view">
    <div class="page-header">
        <?php
            echo Html::a('Set [[name]] To This Account.', 'javascript:void(0)', ['class' => 'btn btn-info pull-right _setTestUser', 'data-account' => $model->id]);    
            echo Html::dropDownList('adminuser', '', ArrayHelper::map($adminUsers, 'email', 'email'), ['class' => 'pull-right', 'prompt' => 'Select User']);
        ?>
        <p class="helper"></p>
        <h1>Account <?= Html::encode($this->title) ?></h1>
    </div>
    

    <?= DetailView::widget([
            'model' => $model,
            'condensed'=>false,
            'hover'=>true,
            'mode'=>Yii::$app->request->get('edit')=='t' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
            'panel'=>[
            'heading'=>$this->title,
            'type'=>DetailView::TYPE_INFO,
        ],
            'formOptions'=>['options' => ['enctype'=>'multipart/form-data']], // important
        'attributes' => [
            'id',
            'eztorm_user_id',
            'customer_exertis_account_number',
            'uuid',
            //'customer.name',
            [
                'class' => 'kartik\grid\DataColumn',
                'attribute'=>'customerName',
                'readonly' => true,
                ],

             [
                'attribute'=>'include_key_in_email',
                'type'=>DetailView::INPUT_SWITCH,
            ],
             [
                'attribute'=>'use_retail_view',
                'type'=>DetailView::INPUT_SWITCH,
            ],
            [
                'attribute'=>'dont_raise_sop',
                'type'=>DetailView::INPUT_SWITCH,
            ],
            
            [
                'label'=>'Credit Limit / Balance',
                'value' => '£' . $creditLimit . ' / ' . '£' . $creditBalance,
            ],
            
            'key_limit',

            [
                'attribute'=>'timestamp',
                //'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
                //'format'=>['datetime','d-m-Y H:i:s A'],
                'type'=>DetailView::INPUT_WIDGET,
                'widgetOptions'=> [
                    'class'=>DateControl::classname(),
                    'type'=>DateControl::FORMAT_DATETIME
                ]
            ],
            [
                'label' => 'Account Logo',
                'format' => 'raw',
                'value' => empty($model->accountLogo)? 
                            '<img src="'.$companyLogo.'" class="img-responsive" width="100px">' : 
                            '<img src="https://res.cloudinary.com/exertis-uk/image/upload/edsr/account_logos/'.$model->logo.'" class="img-responsive" width="100px">'
                            ,
            ],
//            Yii::$app->request->get('edit')=='t' ? [
//                'attribute'=>'image',
//                'type'=>  DetailView::INPUT_FILEINPUT,
//                'options' => ['accept' => 'image/*'],
//                'pluginOptions'=>[
//                    'allowedFileExtensions'=>['jpg'/*,'gif','png'*/],
//                    'mainClass' => 'input-group-lg'
//                    ]
//                ] : [ 'attribute'=>'accountLogo', 'format' => ['image',['width'=>'100']]],
        ],
        'deleteOptions'=>[
        'url'=>['delete', 'id' => $model->id],
        'data'=>[
        'confirm'=>Yii::t('app', 'Are you sure you want to delete this item?'),
        'method'=>'post',
        ],
        ],
        'enableEditMode'=>true,
    ]);





    /*$basketLimitSearchModel = new \common\models\BasketLimitSearch();

     Pjax::begin(); echo GridView::widget([
        'dataProvider' => $basketLimitDataProvider,
        'filterModel' => $basketLimitSearchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'limit',
            'products',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                'view' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Yii::$app->urlManager->createUrl(['basket-limit/view','id' => $model->id]), [
                                                    'title' => Yii::t('yii', 'View'),
                                                    'data-pjax'=>0,
                                                  ]);},
                'update' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['basket-limit/view','id' => $model->id,'edit'=>'t']), [
                                                    'title' => Yii::t('yii', 'Edit'),
                                                    'data-pjax'=>0,
                                                  ]);}

                ],
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>true,




        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> Basket Limits for Account '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['/basket-limit/create', 'account' => $this->title], ['class' => 'btn btn-success']),                                                                                                                                                          'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>false
        ],
    ]); Pjax::end();
    */
    

    $stockroomSearchModel = new common\models\StockroomSearch();
    // Handle more than one Gridview on the page
    // @see http://www.yiiframework.com/doc-2.0/guide-output-data-widgets.html#multiple-gridviews-on-one-page
    $stockroomsDataProvider->pagination->pageParam = 'stockroom-page';
    $stockroomsDataProvider->sort->sortParam = 'stockroom-sort';

     Pjax::begin(); echo GridView::widget([
        'dataProvider' => $stockroomsDataProvider,
        'filterModel' => $stockroomSearchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute'=>'StockItemsCount',
                'header'=>'# Stock Items',
                ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                'view' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Yii::$app->urlManager->createUrl(['stockroom/view','id' => $model->id]), [
                                                    'title' => Yii::t('yii', 'View'),
                                                    'data-pjax'=>0,
                                                  ]);},
                'update' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['stockroom/view','id' => $model->id,'edit'=>'t']), [
                                                    'title' => Yii::t('yii', 'Edit'),
                                                    'data-pjax'=>0,
                                                  ]);}

                ],
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>true,




        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> Stockrooms for Account '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),                                                                                                                                                          'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>



    <?php
    
        $accountRuleMappingModelSearch = new backend\models\AccountRuleMappingSearch();
        
        Pjax::begin(); echo GridView::widget([
            'dataProvider' => $accountRuleMappingDataProvider,
            'filterModel' => $accountRuleMappingModelSearch,
            'columns' => [
                'id',
                [
                    'label' => 'Account Rule',
                    'format' => 'raw',
                    'value' => function($data){
                        $accountRule = \backend\models\AccountRuleMapping::find()->where(['account_id'=>$this->title])->one()->account_rule_id;
                        
                        $rules = explode(',', $accountRule);
                        $accountRules = '';
                                                
                        foreach($rules as $rule){
                            $accountRules .= '<a href="/account-rule/view?id='.$rule.'">' . \backend\models\AccountRule::find()->where(['id'=>$rule])->one()->ruleName . '</a>, ';
                        }
                                                
                        
                        //return Html::a($accountRules, ['account-rule/view', 'id'=>$ruleName->id]);
                        return $accountRules;
                    }
                ],
                'assigned',
                        
                [
                    'format' => 'raw',
                    'value' => function($model){
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['account-rule-mapping/update','id' => $model->id]), [
                                  'title' => Yii::t('yii', 'Edit'),
                                  'data-pjax'=>0,
                                ]);      
                    }
                ], 
            ],
            'responsive'=>true,
            'hover'=>true,
            'condensed'=>true,
            'floatHeader'=>true,




            'panel' => [
                'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> Rules for account '.Html::encode($this->title).' </h3>',
                'type'=>'info',
                'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),
                'showFooter'=>false
            ],
        ]);
    
    ?>
    
    

<?php

    $userSearchModel = new common\models\gauth\GAUserSearch();
    // Handle more than one Gridview on the page
    // @see http://www.yiiframework.com/doc-2.0/guide-output-data-widgets.html#multiple-gridviews-on-one-page
    $usersDataProvider->pagination->pageParam = 'user-page';
    $usersDataProvider->sort->sortParam = 'user-sort';

     Pjax::begin(); echo GridView::widget([
        'dataProvider' => $usersDataProvider,
        'filterModel' => $userSearchModel,
        'columns' => [
            'id',
            'username',
            'role.name',
            'status',
            'email',
            'login_ip',
            'login_time',
            [
              'label' => 'Password',
              'value' => function ($model, $key, $index, $column) { return isset($model["password"]) ? "Set" : "Not Set";}
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                'view' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Yii::$app->urlManager->createUrl(['gauser/view','id' => $model->id]), [
                                                    'title' => Yii::t('yii', 'View'),
                                                    'data-pjax'=>0,
                                                  ]);},
                'update' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['gauser/view','id' => $model->id,'edit'=>'t']), [
                                                    'title' => Yii::t('yii', 'Edit'),
                                                    'data-pjax'=>0,
                                                  ]);}

                ],
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>true,




        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> Users for Account '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),                                                                                                                                                          'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>




       <?php
        // Handle more than one Gridview on the page
        // @see http://www.yiiframework.com/doc-2.0/guide-output-data-widgets.html#multiple-gridviews-on-one-page
        $audittrailDataProvider->pagination->pageParam = 'audittrail-page';
        $audittrailDataProvider->sort->sortParam = 'audittrail-sort';

       Pjax::begin(); echo GridView::widget([
        'dataProvider' => $audittrailDataProvider,
        'filterModel' => $audittrailSearchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
           //'table_name',
           //'record_id',
            'message',
            'timestamp',
            'username',
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>true,
        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> Audit Trail for Account '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>

</div>
