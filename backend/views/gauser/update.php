<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\gauth\GAUser $model
 */

$this->title = 'Update Gauser: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gausers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gauser-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
