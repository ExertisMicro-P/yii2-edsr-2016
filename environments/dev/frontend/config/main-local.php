<?php
// dev

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
                'from' => ['russell.hutson@exertis.co.uk' => 'EDSR Admin ('.YII_ENV.')'], // this is needed for sending emails
                'charset' => 'UTF-8',
            ]
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning' , 'trace'],
                ],
            ],
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
