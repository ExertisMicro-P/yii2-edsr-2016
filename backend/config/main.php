<?php

$params = array_merge(
        require(__DIR__ . '/../../common/config/params.php'), require(__DIR__ . '/../../common/config/params-local.php'), require(__DIR__ . '/params.php'), require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'EDSR',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],

    'components' => [

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'session' => [
            'name' => 'PHPBACKSESSID',
            'savePath' => '@backend/runtime/tmp/',
        ],
        'i18n' => [
            'translations' => [
                'user' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => 'amnah\yii2\user\messages\uk\user.php', // example: @app/messages/fr/user.php
                ],
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Kv4vi3B4pjF7fB6KbWGjb0MS6tuuopIU',
        ],
        'user' => [
            'class' => 'amnah\yii2\user\components\User',
            'identityClass' => 'common\models\gauth\GAUser',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_backendUser', // unique for backend
                'path' => '/advanced/backend/web'  // correct path for the backend app.
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
         * 
         */

        'view'  => [
            'theme' => [
                'pathMap'   => [
                    '@vendor/amnah/yii2-user/views' => '@app/views/user', //
                ]
            ]
        ],
        
        
        // RCH 20160216
        // @see http://www.yiiframework.com/doc-2.0/guide-structure-assets.html#customizing-asset-bundles
        // Need to use jQuery 1.11 as jQuery 2 doesn't support IE8 (JLP have complained)
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,   // do not publish the bundle
                    'js'         => [
                        '//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js',
                    ]
                ],
            ],
        ],

    ], // components


    'modules' => [
        'user' => [
            'emailViewPath'   => '@common/mail',
        ], // user


        'gridview' => [
            'class' => '\kartik\grid\Module'
        // enter optional module parameters below - only if you need to
        // use your own export download action or custom translation
        // message source
        // 'downloadAction' => 'gridview/export/download',
        // 'i18n' => []
        ],
    ], // modules
    'params' => $params,
];
