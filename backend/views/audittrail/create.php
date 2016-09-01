<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var exertis\savewithaudittrail\models\Audittrail $model
 */

$this->title = Yii::t('app', 'Create {modelClass}', [
    'modelClass' => 'Audittrail',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Audittrails'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audittrail-create">
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
