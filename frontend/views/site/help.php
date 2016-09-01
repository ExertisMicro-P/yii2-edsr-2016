<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Help';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about container">
    <div class="row helpsection">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="col-md-6">
            <?= $this->render('_help_faq'); ?>
        </div>


        <div class="col-md-6">
            <?= $this->render('_help_support'); ?>
        </div>
    </div>
    <div class="row helpsection">
        <h1>Getting Started</h1>

        <div class="col-md-12">
            <?= $this->render('_help_getting_started'); ?>
        </div>
    </div>
</div>
