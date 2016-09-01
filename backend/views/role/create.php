<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var amnah\yii2\user\models\Role $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Role',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="role-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
