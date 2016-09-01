<?php
namespace frontend\assets;

use yii\web\AssetBundle;

class InstallKeyAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $js = [
        'js/getInstallKey.js',

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'frontend\assets\RespondAsset',
    ];

}
