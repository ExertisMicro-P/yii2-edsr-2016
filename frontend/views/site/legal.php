<?php
use yii\helpers\Html;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
$this->title = 'Legal Info';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about container">

    <div class="row legalsection">
    <h1><?= Html::encode($this->title) ?></h1>
<?php
   echo Tabs::widget([
    'items' => [
        [
            'label' => 'Terms & Conditions',
            'content' => $this->render('//site/_legal_terms'),
            'active' => true
        ],
        [
            'label' => 'Privacy & Cookie Policy',
            'content' => $this->render('//site/_legal_privacy'),
            //'headerOptions' => [...],
            //'options' => ['id' => 'myveryownID'],
        ],
    ],
]);
?>
</div>
</div>
