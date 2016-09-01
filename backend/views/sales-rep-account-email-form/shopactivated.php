<?php
use yii\helpers\Html;

$this->title = 'Enable shop';
?>
<div class="gauser-view">
    <div class="page-header">
        <h1><?=$this->title?></h1>
    </div>
        <?php
            echo \kartik\widgets\Alert::widget([
                'type' => \kartik\widgets\Alert::TYPE_INFO,
                'title' => 'Enable shop',
                'icon' => 'glyphicon glyphicon-info-sign',
                'body' => $message,
                'showSeparator' => true,
                'closeButton' => false,
            ]);
        ?>
    
</div>
