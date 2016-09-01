<?php
return [
    'adminEmail'                                     => 'russell.hutson@exertis.co.uk',
    'supportEmail'                                   => 'webteam@exertis.co.uk',
    'user.passwordResetTokenExpire'                  => 3600,
//    'authify'    => 'd56ae7fe1c784186ae2b429aceef00a0',
    'authify'                                        => '68fd9d14d7a24ee483907a72cca56d16',
    'audittrail.table'                               => '{{%tbl_audit_trail}}',
    'uploadPath'                                     => 'uploads/',
    'uploadUrl'                                      => '/uploads',
    'frontendBaseUrl'                                => 'https://stockroom.exertis.co.uk/', // used for logos and email links- keep a slash on the end!

    'account.emailPath'                              => Yii::getAlias('@console') . '/mail',
    'account.AccountCreatedEmailRecipients'          => ['russell.hutson@exertis.co.uk', 'dominik.jaross@exertis.co.uk', 'hellen.balco@exertis.co.uk', 'dominic.hoskins@exertis.co.uk'],
    'account.StockItemCreatedEmailToSalesRecipients' => ['russell.hutson@exertis.co.uk', 'dominik.jaross@exertis.co.uk', 'hellen.balco@exertis.co.uk', 'dominic.hoskins@exertis.co.uk'],

    'account.copyAllEmailsTo'                        => ['russell.hutson@exertis.co.uk', 'dominik.jaross@exertis.co.uk'],

];
