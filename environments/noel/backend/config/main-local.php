<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Kv4vi3B4pjF7fB6KbWGjb0MS6tuuopIU',
        ],
        'user'    => [
            'class' => 'amnah\yii2\user\components\User',
        ],
        'mailer'  => [
            'class'            => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
            'messageConfig'    => [
                'from'    => ['russell.hutson@exertis.co.uk' => 'EDSR'], // this is needed for sending emails
                'charset' => 'UTF-8',
            ]
        ],
    ],
    'modules'    => [
        'user' => [
            'class'         => 'amnah\yii2\user\Module',
            // set custom module properties here ...
            'loginDuration' => 60 * 60 * 4, // 4 hours
        ],

        'gridview' => [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
    ],
    'log' => [
        'targets' => [
            'file' => [
                'class' => 'yii\log\FileTarget',
                'levels' => ['trace', 'info', 'error'],
                //'categories' => ['order'],
                'logFile' => '@console/runtime/logs/app.log',
                'logVars' => [], // RCH 20150624 stop logging all the damn Super Globals! see http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
                'except' => ['yii\db\*'],
            ],
            'email' => [
                'class' => 'yii\log\EmailTarget',
                'levels' => ['error', 'warning'],
                'logVars' => [], // RCH 20150624 stop logging all the damn Super Globals! see http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
                'message' => [
                    'to' => ['edsr@crewe-it.co.uk'],
                ],
            ],
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][]      = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    //$config['modules']['gii'] = 'yii\gii\Module';
    $config['modules']['gii']['class'] = 'yii\gii\Module';

    $config['modules']['gii']['generators'] = [
        'kartikgii-crud' => ['class' => 'warrence\kartikgii\crud\Generator'],
    ];
}

return $config;
