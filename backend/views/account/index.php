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

backend\assets\EnableDisableAsset::register($this);
?>
<div class="account-index">
    <div class="page-header">
            <h1><span class="glyphicon glyphicon-user"></span> <?= Html::encode($this->title) ?></h1>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php /* echo Html::a(Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Account',
]), ['create'], ['class' => 'btn btn-success'])*/  ?>
    </p>

    <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'eztorm_user_id',
            'customer_exertis_account_number',

            [
                'label' => 'Account Name',
                'value' => function($data){
                    $customer = common\models\CustomerT::find()->where(['account_number'=>$data->customer_exertis_account_number])->one();
                    
                    return $customer->company_name;
                }
            ],
            [
                'header'=>'# Stock Items',
                'value'=> 'stockItemsCount'
            ],

            
            [
                'label' => 'Account Logo',
                'format' => 'raw',
                'value' => function($model){
                    if(empty($model->accountLogo)){
                        $account = common\models\Account::findOne($model->id);
                        
                        if($account->findMainUser()){
                            $companyEmail = $account->findMainUser()->email;
                            
                            if($companyEmail==null){
                                return '<img src="http://stockroomdev.exertis.co.uk/img/no-boxshot.jpg" class="img-responsive" width="100px">';
                            }
                            
                            $companyDomain = explode('@', $companyEmail)[1];
                            $companyLogo = 'http://logo.clearbit.com/'.$companyDomain;

                            if(!common\components\LogoHelper::cURLImage($companyLogo)){
                                $companyLogo = 'http://stockroomdev.exertis.co.uk/img/no-boxshot.jpg';
                            }
                        } else {
                            $companyLogo = 'http://stockroomdev.exertis.co.uk/img/no-boxshot.jpg';
                        }
                        
                        return '<img src="'.$companyLogo.'" class="img-responsive" width="100px">';
                    } else {
                        return '<img src="https://res.cloudinary.com/exertis-uk/image/upload/edsr/account_logos/'.$model->logo.'" class="img-responsive" width="100px">';
                    }
                }
            ],
            /*[
                'attribute' => 'account',
                'label' => '',
                'value' => 'accountLogo',
                'format' => ['image',['width'=>'100']]
            ],*/
            [
                'attribute' => 'include_key_in_email',
                'class' => '\kartik\grid\BooleanColumn',
                'trueLabel' => 'Yes',
                'falseLabel' => 'No'
            ],
                    
            [
                'label' => 'Credit Limit (£)',
                'attribute' => 'credit.overall_credit_limit',
                'format' => ['decimal', 2],
            ],
                    
            'key_limit',
            [
                'label' => 'Shop Enabled',
                'format' => 'raw',
                'value' => function($data){
                    $mainUser = $data->findMainUser();
                    
                    if($mainUser){
                        if($mainUser->shopEnabled == false){
                            $class = "glyphicon glyphicon-remove text-danger";
                            $action = 'enable';
                        } else {
                            $class = "glyphicon glyphicon-ok text-success";
                            $action = 'disable';
                        }
                    } else {
                            $class = "glyphicon glyphicon-ok text-success";
                            $action = 'disable';
                    }
                    
                    return Html::a('<span class="'.$class.'"></span>', 'javascript:void(0)', ['class'=>'_enableDisableShop', 'data-action'=>$action, 'data-account'=>$data->id]);
                }
            ],

            ['attribute'=>'timestamp'],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                'update' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Yii::$app->urlManager->createUrl(['account/view','id' => $model->id,'edit'=>'t']), [
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
