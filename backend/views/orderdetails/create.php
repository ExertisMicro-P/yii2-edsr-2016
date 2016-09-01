<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\Orderdetails $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Orderdetails',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Orderdetails'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orderdetails-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
