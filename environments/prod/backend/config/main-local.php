<?php

return [
    'components' => [
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'messageConfig' => [
                'from' => ['webteam@exertis.co.uk' => 'EDSR Backend'], // this is needed for sending emails
                'charset' => 'UTF-8',
            ]
        ],
    ],
];
