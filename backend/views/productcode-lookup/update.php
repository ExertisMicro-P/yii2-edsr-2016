<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProductcodeLookup */

$this->title = 'Update Productcode Lookup: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Productcode Lookups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="productcode-lookup-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
