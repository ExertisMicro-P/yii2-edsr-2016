<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class LeafletUploaderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/js/Jcrop-master/css/jquery.Jcrop.min.css',
        '/css/leaflet_uploader.css',
    ];
    public $js = [
        '/js/Jcrop-master/js/jquery.Jcrop.min.js',
        '/js/leafleteditor.js',
        '/js/leafletutils.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
    ];
}
