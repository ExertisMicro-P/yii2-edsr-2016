<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\CupboardItem $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Cupboard Item',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cupboard Items'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cupboard-item-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
