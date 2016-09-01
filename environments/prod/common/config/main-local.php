<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=ma-intdb-03;dbname=yii2edsr',
            'username' => 'yii2edsr',
            'password' => 'zQu6hwfOgi',
            'charset' => 'utf8',
        ],
        'creditDb' => [
            'class'    => 'yii\db\Connection',
                'dsn'      => 'mysql:host=mb-webserver-01;dbname=dbshadow',
            'username' => 'edsr',
            'password' => 'edsr_l0g!n',
            'charset'  => 'utf8',
        ],
'invoicesDb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlsrv:Server=94.236.49.104;Database=MMAV2',
            'username' => 'yii2-mma',
            'password' => 'rg248qyRG"$*QY',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            //'useFileTransport' => true,  // dump to runtime/mail files, rather than sending
            'useFileTransport' => false,  // dump to runtime/mail files, rather than sending
            'messageConfig' => [
                'from' => ['esd@exertis.co.uk' => 'Exertis Digital Stock Room'], // this is needed for sending emails
                'charset' => 'UTF-8',
            ],
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                //'class' => 'Swift_MailTransport',
                //'constructArgs' => ['localhost', 25]
            'class' => 'Swift_SmtpTransport',
            'host' => 'localhost',
            'port' => '25',
            ], // transport
        ], // mailer

    ],
];
