<?php
return [
    'components' => [
        'mailer' => [
            'class'            => 'yii\swiftmailer\Mailer',
            'viewPath'         => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
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

    ]
];
