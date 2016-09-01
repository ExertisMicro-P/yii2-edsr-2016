<?php

use yii\bootstrap\Alert;
use yii\bootstrap\Progress;

/*
print_r($maintenancemodeparams);
echo '<br>';
echo strtotime($maintenancemodeparams['start']).'<br>';
echo strtotime($maintenancemodeparams['end']).'<br>';
echo time().'<br>';
*/
echo Alert::widget([
    'options' => [
        'class' => 'alert-warning',
    ],
    'body' => $maintenancemodeparams['message'].'<br>We plan to resume normal service at '.date('l jS \of F Y h:i:s A', strtotime($maintenancemodeparams['end'])),
]);

$progress = 100* ((time()-strtotime($maintenancemodeparams['start'])) / (strtotime($maintenancemodeparams['end']) - strtotime($maintenancemodeparams['start'])));

echo Progress::widget([
    'percent' => $progress,
    'barOptions' => ['class' => 'progress-bar-warning'],
    'options' => ['class' => 'progress-striped'],
    'label' => floor($progress).'%',
]);

