<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var common\models\gauth\GAUser $model
 */

$this->title = 'Create Gauser';
$this->params['breadcrumbs'][] = ['label' => 'Gausers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gauser-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
