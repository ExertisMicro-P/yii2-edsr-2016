<?php
// edsr-dev

return [
    'storeType'                                      =>'TEST',
    'useLiveeZtorm' => FALSE,
    'baggingURL'                                     => 'http://mdfsbg.micro-p.co/bagging/EDSR/order',
    'baggingUser'                                    => 'MX919192',
    'baggingPword'                                   => 'CphVzHxBG47X6efM',
    'backEndServer'                                  => 'http://edsr.exertis.co.uk',

    'StubAPI.GetProductByIdMsgHandler' => true, // true means stub it and don't use eZtorm API

    'GALoginForm.useLDAP' => false, // FALSE = don't lookup user using LDAP

];
