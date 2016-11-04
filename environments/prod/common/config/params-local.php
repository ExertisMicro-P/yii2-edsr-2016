<?php
return [
    'storeType'                        => 'LIVE',
    'gauthify.skipSSL'                 => true,
    'useLiveeZtorm'                    => true,
    'baggingURL'                       => 'http://mdfsbg.micro-p.co/bagging/EDSR/order',

    // RCH 20160425
    // MDFS is migrating away from webproxy2 to ma-webproxy-06
    //'baggingURL'                                     => 'http://mdfsbg.exertis.io/bagging/EDSR/order',
    'baggingUser'                      => 'MX919192',
    'baggingPword'                     => 'CphVzHxBG47X6efM',
    'backEndServer'                    => 'http://edsr.exertis.co.uk',

    'StubAPI.GetProductByIdMsgHandler' => false, // true means stub it and don't use eZtorm API
    'GALoginForm.useLDAP'              => false, // FALSE = don't lookup user using LDAP

    'EDItoMDFSBasketEmailAddress'      => 'webteam@exertis.co.uk',

];
