<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProductcodeLookup */

$this->title = 'Create Productcode Lookup';
$this->params['breadcrumbs'][] = ['label' => 'Productcode Lookups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="productcode-lookup-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
