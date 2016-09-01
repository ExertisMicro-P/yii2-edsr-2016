<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\AccountRuleMapping */

$this->title = 'Update Account Rule Mapping: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Accounts', 'url' => ['/account']];
$this->params['breadcrumbs'][] = ['label' => $model->account_id, 'url' => ['/account/view', 'id' => $model->account_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="account-rule-mapping-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
