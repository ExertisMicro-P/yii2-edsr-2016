<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'News';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about container">
    <div class="row helpsection">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="col-md-12">
            <?= $this->render('_news_changes'); ?>
        </div>


    </div>
</div>
