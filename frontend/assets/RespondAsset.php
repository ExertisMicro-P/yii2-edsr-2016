<?php
namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Try to fix IE8 support (assumes we have dropped jQuery down to 1.11.* (not 2.*)
 * Iclude respond.js and HTML5shiv
 * Both of these should be installed from npm bower - @see http://www.yiiframework.com/doc-2.0/guide-structure-assets.html#bower-and-npm-assets
 * These have been manually added to the composer.json
 * e.g. composer require bower-asset/html5shiv
 */
class RespondAsset extends AssetBundle
{
    //public $basePath = '@bower/respond';
    public $sourcePath = '@bower';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $jsOptions = ['condition' => 'lte IE 8', 'position' => \yii\web\View::POS_HEAD];
    //public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

    public $js = [
        'html5shiv/dist/html5shiv.min.js',
        'respond/dest/respond.min.js'
    ];

    public $depends = [
        
    ];
}
