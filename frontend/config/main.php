<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id'                  => 'Exertis Digital Stock Room',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'layout'              => 'mainnw',
    'defaultRoute'        => 'srflat',
    //'defaultRoute'        => 'shop',
    'controllerNamespace' => 'frontend\controllers',
    'components'          => [

        'request'      => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '4jWSbGUBjBKopuFMuIf63pvKb7i640tv',
        ],

        'session'      => [
            'name'     => 'ex6HF9li206yq4u9MLjuU3ee',
            'savePath' => '@frontend/runtime/tmp/',
            'timeout'  => 20 * 60 * 60
        ],

        'i18n'         => [
            'translations' => [
                'user' => [
                    'class'    => 'yii\i18n\PhpMessageSource',
                    'basePath' => 'amnah\yii2\user\messages\uk\user.php', // example: @app/messages/fr/user.php
                ]
            ],
        ],


        'log'          => [
            'targets' => [
                'file'  => [
                    'class'   => 'yii\log\FileTarget',
                    //'levels'  => ['trace', 'info', 'error'],
                    'levels'  => ['info', 'error'],
                    //'categories' => ['order'],
                    'logFile' => '@frontend/runtime/logs/app.log',
                    'logVars' => [], // RCH 20150624 stop logging all the damn Super Globals! see http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
                     'except' => ['yii\db\*'],
                ],
                'email' => [
                    'class'   => 'yii\log\EmailTarget',
                    'levels'  => ['error', 'warning'],
                    'logVars' => [], // RCH 20150624 stop logging all the damn Super Globals! see http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
                    'message' => [
                        'to' => 'russell.hutson@exertis.co.uk',
                    ],
                ],
            ]
        ],


        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager'   => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => [
                'checkuser' => 'gauth/check',
                'login'     => 'gauth/validate-login',
                'authuser'  => 'gauth/validate-authentication'
            ]
        ],
        'view'         => [
            'theme' => [
                'pathMap' => [
                    '@vendor/amnah/yii2-user/views' => '@app/views/gauth', //
                ]
            ]
        ],

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


    ],
    'modules'             => [
        'user'     => [
            'class'           => 'amnah\yii2\user\Module',
            'requireEmail'    => true,
            'requireUsername' => false,

            // set custom module properties here ...
            'loginDuration'   => 60 * 60 * 4,                             // 4 hours
            'loginEmail'      => true,
            'loginUsername'   => false,

            'controllerMap'   => [
                'default' => 'frontend\controllers\GauthController',
            ],
            'modelClasses'    => [
                'LoginForm' => 'common\models\gauth\forms\GALoginForm',
                'UserKey'   => 'common\models\gauth\GAUserKey',
                'User'      => 'common\models\gauth\GAUser',
            ],
            //'emailViewPath'   => '@app/views/gauth/emails',
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

        'markdown' => [
            'class' => 'kartik\markdown\Module',
        ]

    ],

    'params'              => $params,
];
