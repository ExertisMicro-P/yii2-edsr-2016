<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

// RCH
Yii::setAlias('tests', __DIR__ . '/../../tests');

return [
    'id' => 'EDSR-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'console\controllers',
    'modules' => [
        'gii' => 'yii\gii\Module',
        'user' => [
            'class' => 'amnah\yii2\user\Module',
            'requireEmail' => false,
            'requireUsername' => true,
        ],
    ],
    'components' => [
        'user' => [
            'class' => 'amnah\yii2\user\components\User',
        ],

        'urlManager'=> [
            'hostInfo' => 'https://stockroom.exertis.co.uk',
            'baseUrl' => 'https://stockroom.exertis.co.uk',
        ],

/*
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            //'useFileTransport' => true,  // dump to runtime/mail files, rather than sending
            'messageConfig' => [
                'from' => ['russell.hutson@exertis.co.uk' => 'Admin'], // this is needed for sending emails
                'charset' => 'UTF-8',
            ],
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                //'class' => 'Swift_MailTransport',
                'constructArgs' => ['localhost', 25]
            ], // transport
        ], // mailer
*/
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
                        'to' => ['russell.hutson@exertis.co.uk', 'dominik.jaross@exertis.co.uk'],
                    ],
                ],
            ],
        ],
 ],


    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\faker\FixtureController', // RCH see https://github.com/yiisoft/yii2-faker
        ],
    ],


    'params' => $params,
];
