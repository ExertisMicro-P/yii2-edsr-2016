<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use kartik\datecontrol\DateControl;

use kartik\grid\GridView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var common\models\StockItem $model
 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-item-view">
    <div class="page-header">
        <h1>Stock Item <?= Html::encode($this->title) ?></h1>

        <?php
/*            $flashes = $session->getFlash('error');
            if ($flashes) {
                    //echo('here='.print_r($flashes,true));
                    //die();
                    $flashes = is_array($flashes) ? $flashes : array($flashes);
                foreach ($flashes as $alert) {
                    echo Alert::widget([
                        'type' => Alert::TYPE_DANGER,
                        'icon' => 'glyphicon glyphicon-ok-sign',
                        'body' => $alert,
                        'showSeparator' => true,
                        'delay' => 20000
                    ]);
                }  // foreach
            } // if
*/
        ?>



    </div>

<div class="row">
   <div class="col-md-8">

    <?= DetailView::widget([
            'model' => $model,
            'condensed'=>false,
            'hover'=>true,
            'mode'=>Yii::$app->request->get('edit')=='t' ? DetailView::MODE_EDIT : DetailView::MODE_VIEW,
            'panel'=>[
            'heading'=>$this->title,
            'type'=>DetailView::TYPE_INFO,
        ],
        'attributes' => [
            'id',
            'stockroom_id',
            'productcode',
'key',
            'status',
            'spare',
            'send_email:boolean',
            [
                'attribute'=>'tagNames',
                'options' => ['data-role'=>'tagsinput'],
            ],
            'downloadURL:url',
            [
                'attribute'=>'timestamp_added',
                'format'=>['datetime',(isset(Yii::$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::$app->modules['datecontrol']['displaySettings']['datetime'] : 'Y-m-d H:i:s A'],
                'type'=>DetailView::INPUT_WIDGET,
                'widgetOptions'=> [
                    'class'=>DateControl::classname(),
                    'type'=>DateControl::FORMAT_DATETIME
                ]
            ],
            [
                'label' => 'Customer PO',
                'attribute' => 'orderdetails',
                'value' => $model->orderdetails->po,
            ],
            'sop'
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

   </div>
   <div class="col-md-4">
<?= Html::img($model->digitalProduct->image_url, ['class'=>'digitalproduct_thumb']) ?>
       <div class="row">
    <div class="col-md-12">
        <?= Html::a('<span class="glyphicon glyphicon-envelope"></span> Send Notification', ['/stockitem/send-notification', 'id'=>$model->id], ['class'=>'btn btn-xs btn-info kv-btn-send', 'title'=>'Send Notification']) ?>
    </div>
</div>
</div>
</div> <!-- row -->





       <?php Pjax::begin(); echo GridView::widget([
        'dataProvider' => $emailedItemDataProvider,
        'filterModel' => $emailedItemSearchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'label' => 'Name',
                'attribute' => 'emailedUser',
                'value' => 'emailedUser.name'
            ],
            [
                'label' => 'Email',
                'attribute' => 'emailedUserEmail',
                'value' => 'emailedUserEmail.email'
            ],
            [
                'label' => 'Sent at',
                'attribute' => 'created_at'
            ],
        ],
        'responsive'=>true,
        'hover'=>true,
        'condensed'=>true,
        'floatHeader'=>true,




        'panel' => [
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-envelope"></i> Emails sent for Stock Item '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>



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
            'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> Audit Trail for Stock Item '.Html::encode($this->title).' </h3>',
            'type'=>'info',
            'showFooter'=>false
        ],
    ]); Pjax::end(); ?>

</div>
