<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\StockItem $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Stock Item',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-item-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
