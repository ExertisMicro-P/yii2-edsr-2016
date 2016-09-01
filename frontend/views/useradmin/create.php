<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Useradmin */

$this->title = 'Create New User';
$this->params['breadcrumbs'][] = ['label' => 'Useradmins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="useradmin-create formbg col-md-6 col-xs-12 col-md-offset-3">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
