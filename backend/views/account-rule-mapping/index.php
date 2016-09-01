<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AccountRuleMappingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Account Rule Mappings';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-rule-mapping-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => 'Account',
                'attribute' => 'account_id',
                'value' => function($data){
                    return common\models\Account::find()->where(['id'=>$data->account_id])->one()->customer_exertis_account_number;
                }
            ],
            [
                'label' => 'Account Rules',
                'attribute' => 'account_rule_id',
                'format' => 'raw',
                'value' => function($data){
                    $rules = explode(',', $data->account_rule_id);
                    $ruleNames = '';
                    
                    foreach($rules as $rule){
                        
                        $ruleName = \backend\models\AccountRule::find()->where(['id'=>$rule])->one();
                        
                        $ruleNames .= Html::a($ruleName->ruleName, ['/account-rule/view', 'id'=>$ruleName->id]) . ', ';
                    }
                    
                    return $ruleNames;
                }
            ],
            'assigned',

            ['class' => 'yii\grid\ActionColumn'],
        ],
        'responsive' => true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>true,




        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'before'=>Html::a('<i class="glyphicon glyphicon-plus"></i> Add', ['create'], ['class' => 'btn btn-success']),                                                                                                                                                          'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset List', ['index'], ['class' => 'btn btn-info']),
            'showFooter'=>false
        ],
    ]); ?>

</div>
