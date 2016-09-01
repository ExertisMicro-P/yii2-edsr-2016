<?php

return [
    'components' => [
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            //'useFileTransport' => true,  // dump to runtime/mail files, rather than sending
            'messageConfig' => [
                'from' => ['esd@exertis.co.uk' => 'Exertis Digital Stock Room'], // this is needed for sending emails
                'charset' => 'UTF-8',
            ],
            'useFileTransport' => false,
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
