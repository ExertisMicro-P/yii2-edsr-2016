<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\DigitalProduct $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Digital Product',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Digital Products'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="digital-product-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
