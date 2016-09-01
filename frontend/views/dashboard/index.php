<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\tabs\TabsX;

/**
 * @var yii\web\View                  $this
 * @var yii\data\ActiveDataProvider   $dataProvider
 * @var common\models\StockroomSearch $searchModel
 */

$this->title                   = Yii::t('app', 'Client Orders');
$this->params['breadcrumbs'][] = $this->title;

?>

<div id="sr-dashboard">
    <div class="page-header">
        
        <div class="sfe-buttons">
            <span>Switch View:</span>
            <li class="btn btn-primary">
               <a data-toggle="pill" href="#account-search"><span class="glyphicon glyphicon-user"></span> <?= Html::encode('Accounts') ?></a>
            </li>
            <li class="btn btn-primary">
                <a data-toggle="pill" href="#sales-rep-pending"><span class="glyphicon glyphicon-user"></span> <?= Html::encode('Pending Orders') ?></a>
            </li>
            <li class="btn btn-primary">
                <a data-toggle="pill" href="#sales-rep-order"><span class="glyphicon glyphicon-user"></span> <?= Html::encode('Recent Orders') ?></a>
            </li>
        </div>
    </div>

    <div class="tab-content">
        <?php
        echo Yii::$app->controller->renderPartial('_account_search', ['searchModel' => $accountModel, 'dataProvider' => $accountDataProvider]) ;
        echo Yii::$app->controller->renderPartial('_pending_search', ['pendingSearchModel' => $pendingSearchModel, 'pendingDataProvider' => $pendingDataProvider,
                                                                        'divId' => 'sales-rep-pending']) ;
        echo Yii::$app->controller->renderPartial('_recent_orders', ['recentOrderSearchModel' => $recentOrderSearchModel, 'recentOrderDataProvider' => $recentOrderDataProvider,
                                                                        'divId' => 'sales-rep-order']) ;

        ?>
    </div>
</div>

<?php

$this->registerJs(<<< _EOF
    $('.kv-thead-float').scroll();

    $('a[data-toggle="pill"]:eq(0)').tab('show');

    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        e.target // newly activated tab
        e.relatedTarget // previous active tab
        $('.kv-thead-float', $(e.target).attr('href')).scroll()
    })

_EOF
) ;
