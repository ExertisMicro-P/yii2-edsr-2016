<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\AccountRule */

$this->title = 'Create Account Rule';
$this->params['breadcrumbs'][] = ['label' => 'Account Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
