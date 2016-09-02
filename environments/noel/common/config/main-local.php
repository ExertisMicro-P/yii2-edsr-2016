<?php
Yii::$classMap['GAuthify'] = '@vendor/GAuthify-PHP/gauthify.php' ;

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn'      => 'mysql:host=localhost;dbname=yii2edsr',
            'username' => 'edsr',
            'password' => 'p455w0rd',
            'charset' => 'utf8',
        ],

        'customerPricesDb' => [
            'class'    => 'yii\db\Connection',
                'dsn'      => 'mysql:host=localhost;dbname=yii2edsrcredit',
            'username' => 'edsr',
            'password' => 'p455w0rd',
            'charset'  => 'utf8',
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
        ],
//        'gauthHelper' => [
//            'class' =>  'app\components\GauthHelper'
//        ]

    ],
];
