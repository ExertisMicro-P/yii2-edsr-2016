<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var common\models\gauth\GAUser $model
 */

backend\assets\ResetPasswordAsset::register($this);

$this->title = $model->email;
$this->params['breadcrumbs'][] = ['label' => 'Gausers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gauser-view">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    
    <?=  Html::a('<< Back To The Account', ['/account/view', 'id'=>$model->account_id])?>
    
    <?php
    
    Modal::begin([
        'header' => '<h3>Resend invitation for '.$this->title.'</h3>',
        'toggleButton' => ['label' => 'Resend Invitation', 'class' => 'btn btn-info pull-right', 'style'=>'margin-left:10px'],
    ]);    
    
        echo Html::hiddenInput('userId', $model->id);
    
        echo 'Email address:';
        echo Html::textInput('newEmailResend', $model->email, ['class' => 'form-control']) . '<br>';
        
        echo '<p class="message-resend"></p>';
        
        echo Html::button('Send', ['class' => 'btn btn-primary _resendInvitation', 'type'=>'button']);

    Modal::end();
    
    Modal::begin([
        'header' => '<h3>Password reset for '.$this->title.'</h3>',
        'toggleButton' => ['label' => 'Reset Password', 'class' => 'btn btn-info pull-right'],
    ]);    
    
        echo Html::hiddenInput('userId', $model->id);
    
        echo 'Email address:';
        echo Html::textInput('newEmail', $model->email, ['class' => 'form-control']) . '<br>';
        
        echo '<p class="message-reset"></p>';
        
        echo Html::button('Send', ['class' => 'btn btn-primary _sendResetPassword', 'type'=>'button']);

    Modal::end();
    
    ?>
    
    <br><br>

    <?= DetailView::widget([
            'model' => $model,
            'condensed'=>false,
            'hover'=>true,
            'mode'=>Yii::$app->request->get('edit')=='t' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
            'panel'=>[
            'heading'=>$model->email,
            'type'=>DetailView::TYPE_INFO,
        ],
        'attributes' => [
            'id',
            'role_id',
            'status',
            [
                'attribute' => 'shopEnabled',
                'value' => ($model->shopEnabled)? 'Enabled' : 'Disabled',
                'type' => DetailView::INPUT_CHECKBOX
            ],
            [
                'attribute' => 'spending_limit',
                'format' => 'raw',
                'value' => '£'.  number_format($model->spending_limit, 2),
            ],
            'email:email',
            'new_email:email',
            'username',
            'password',
            'auth_key',
            'api_key',
            'login_ip',
            [
                'attribute'=>'login_time',
                'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
                'type'=>DetailView::INPUT_WIDGET,
                'widgetOptions'=> [
                    'class'=>DateControl::classname(),
                    'type'=>DateControl::FORMAT_DATETIME
                ]
            ],
            'create_ip',
            [
                'attribute'=>'create_time',
                'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
                'type'=>DetailView::INPUT_WIDGET,
                'widgetOptions'=> [
                    'class'=>DateControl::classname(),
                    'type'=>DateControl::FORMAT_DATETIME
                ]
            ],
            [
                'attribute'=>'update_time',
                'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
                'type'=>DetailView::INPUT_WIDGET,
                'widgetOptions'=> [
                    'class'=>DateControl::classname(),
                    'type'=>DateControl::FORMAT_DATETIME
                ]
            ],
            [
                'attribute'=>'ban_time',
                'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A'],
                'type'=>DetailView::INPUT_WIDGET,
                'widgetOptions'=> [
                    'class'=>DateControl::classname(),
                    'type'=>DateControl::FORMAT_DATETIME
                ]
            ],
            'ban_reason',
            'uuid',
            'account_id',
        ],
        'deleteOptions'=>[
        'url'=>['delete', 'id' => $model->id],
        'data'=>[
        'confirm'=>Yii::t('app', 'Are you sure you want to delete this item?'),
        'method'=>'post',
        ],
        ],
        'enableEditMode'=>true,
    ]) ?>




       <?php Pjax::begin(); echo GridView::widget([
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
