<?php



return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'gauthHelper' => [
            'class' => 'app\components\GauthHelper'
        ],
        'user' => [
            'identityClass' => 'common\models\gauth\GAUser',
            'class' => 'amnah\yii2\user\components\User',
            'enableAutoLogin' => true,
            'identityCookie' => [
                'name' => '_frontendUser', // unique for frontend
                'path' => '/advanced/frontend/web'  // correct path for the backend app.
            ],
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'Y-m-d',
            'datetimeFormat' => 'Y-m-d H:i:s',
            'timeFormat' => 'H:i:s',
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@vendor/amnah/yii2-user/views/default' => '@frontend/views/user/default', // example: @app/views/user/default/login.php
                ],
            ],
        ],


        // @see https://github.com/edvler/yii2-adldap-module
/*
        $conf['settings']['host'] = '172.27.2.81,172.27.2.82'; // comma separated list of ldap servers such as mydomain1,localhost
        $conf['settings']['port'] = '389';      // default ldap port 389 or 636 for ssl.
        $conf['settings']['version'] = '3';      // LDAP protocol version
        $conf['settings']['starttls'] = 'false';   // TLS is started after connecting
        $conf['settings']['binddn'] = 'micro-p\\mploan';   // The distinguished name to bind as (username). If you don't supply this, an anonymous bind will be established.
        $conf['settings']['bindpw'] = 'monday';   // Password for the binddn. If the credentials are wrong, the bind will fail server-side and an anonymous bind will be established instead. An empty bindpw string starts an unauthenticated bind.
        //$conf['settings']['basedn'] = 'CN=Users,DC=micro-p,DC=com';
        $conf['settings']['basedn'] = array('OU=Altham,DC=micro-p,DC=com','OU=Stoke,DC=micro-p,DC=com','OU=Basingstoke,DC=micro-p,DC=com','OU=Stoke Security, DC=micro-p,DC=com','OU=VAD,DC=micro-p,DC=com');
        $conf['settings']['filter'] = '';   // Default search filter
        $conf['settings']['scope'] = '';   // TLS is started after connecting
        $conf['settings']['database.auth.when.ldap.user.not.found'] = 'false';   // if ldap auth fails, authenticate against phpScheduleIt database
        $conf['settings']['ldap.debug.enabled'] = 'true';   // if LDAP2 should use debug logging
        $conf['settings']['attribute.mapping'] = 'sn=sn,givenname=givenname,mail=mail,telephonenumber=telephonenumber,physicaldeliveryofficename=physicaldeliveryofficename,title=title';   // mapping of required attributes to attribute names in your directory
        $conf['settings']['user.id.attribute'] = 'sAMAccountName';   // the attribute name for user identification>
*/


        'ldap' => [
            'class'=>'Edvlerblog\Ldap',
            'options'=> [
                    'ad_port'      => 389,
                    'domain_controllers'    => array('172.27.2.81','172.27.2.82'),
                    'account_suffix' =>  '@micro-p.com',
                    'base_dn' => 'OU=Basingstoke,DC=micro-p,DC=com', //array("OU=Basingstoke,DC=micro-p,DC=com",'OU=Altham,DC=micro-p,DC=com'),
                    // for basic functionality this could be a standard, non privileged domain user (required)
                    'admin_username' => 'email',
                    'admin_password' => 'password',
                    'user_id_key' => 'sAMAccountName'
                ]
        ]

    ],
    'modules' => [

        // begin http://www.yiiframework.com/extension/yii2-kartikgii/
        'datecontrol' => [
            'class' => 'kartik\datecontrol\Module',
            // format settings for displaying each date attribute
            'displaySettings' => [
                'date' => 'd-m-Y',
                'time' => 'H:i:s A',
                'datetime' => 'd-m-Y H:i:s A',
            ],
            // format settings for saving each date attribute
            'saveSettings' => [
                'date' => 'Y-m-d',
                'time' => 'H:i:s',
                'datetime' => 'Y-m-d H:i:s',
            ],
            // automatically use kartik\widgets for each of the above formats
            'autoWidget' => true,
        ], // datecontrol
        'user' => [
            'class' => 'amnah\yii2\user\Module',
            'loginDuration' => 60 * 60 * 4, // 4 hours
            'requireEmail' => true,
            'requireUsername' => false,
            ], // user
            'gridview' => [
                'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
            ],

    ], // modules
];
