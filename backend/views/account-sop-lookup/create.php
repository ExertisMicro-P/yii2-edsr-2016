<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\AccountSopLookup $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Account Sop Lookup',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Account Sop Lookups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-sop-lookup-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
