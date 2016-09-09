<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Kv4vi3B4pjF7fB6KbWGjb0MS6tuuopIU',
        ],

        'user' => [
            'class' => 'amnah\yii2\user\components\User',
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
            'messageConfig' => [
                'from' => ['noel@crewe-it.co.uk' => 'EDSR Admin ('.YII_ENV.')'], // this is needed for sending emails
                'charset' => 'UTF-8',
            ]
        ],

        'log' => [
            'targets' => [
                'file' => [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace', 'info', 'error', 'profile'],
                    //'categories' => ['order'],
                    'logFile' => '@console/runtime/logs/app-nw.log',
                    'logVars' => [], // RCH 20150624 stop logging all the damn Super Globals! see http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
//                    'except' => ['yii\db\*'],
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



        'session'      => [
            'name'     => 'ex6HF9li206yq4u9MLjuU3ee',
            'savePath' => '/var/lib/php/session',
            'timeout'  => 20 * 60 * 60
        ],

    ],
    'modules' => [
        'user' => [
            'class' => 'amnah\yii2\user\Module',

            // set custom module properties here ...
            'loginDuration' => 60*60*4,                             // 4 hours
            'loginEmail'    => true,
            'loginUsername' => false
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    //$config['modules']['gii'] = 'yii\gii\Module';
    $config['modules']['gii']['class'] = 'yii\gii\Module'; //http://www.yiiframework.com/extension/yii2-kartikgii/
}

return $config;
