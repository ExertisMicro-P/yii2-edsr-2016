<?php
// edsr-dev

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            //'dsn' => 'mysql:host=mb002095;dbname=yii2-edsr-two-dev',
            //'username' => 'root',
            //'password' => '',
            'dsn' => 'mysql:host=ma-intdb-03;dbname=yii2edsr',
            'username' => 'yii2edsr',
            'password' => 'zQu6hwfOgi',
            'charset' => 'utf8',
        ],

        'creditDb' => [
            'class'    => 'yii\db\Connection',
                'dsn'      => 'mysql:host=mb-webserver-02;dbname=dbshadow',
            'username' => 'edsr',
            'password' => 'edsr_l0g!n',
            'charset'  => 'utf8',
        ],

        'invoicesDb' => [
            'class' => 'yii\db\Connection',
            //'dsn' => 'dblib:host=94.236.49.104;dbname=MMAV2;port=1433',
            'dsn' => 'sqlsrv:Server=94.236.49.104;Database=MMAV2',
            'username' => 'yii2-mma',
            'password' => 'rg248qyRG"$*QY',
            'charset' => 'utf8',
        ],
/*
        'customerPricesDb' => [
            'class'    => 'yii\db\Connection',
                'dsn'      => 'mysql:host=localhost;dbname=yii2-edsr-two-dev-mockdb',
            'username' => 'root',
            'password' => '',
            'charset'  => 'utf8',
        ],
*/

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            //'useFileTransport' => true,  // dump to runtime/mail files, rather than sending
            'messageConfig' => [
                'from' => ['russell.hutson@exertis.co.uk' => 'EDSR Admin ('.YII_ENV.')'], // this is needed for sending emails
                'charset' => 'UTF-8',
            ],
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                //'class' => 'Swift_MailTransport',
                'constructArgs' => ['baspop', 25]
            ], // transport
        ], // mailer

    ],
];
