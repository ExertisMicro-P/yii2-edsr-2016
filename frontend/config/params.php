<?php
return [
    'adminEmail' => 'russell.hutson@exertis.co.uk',
    'defaultController' => 'site',
    'useGauthify' => FALSE,  // FALSE = Disable Google Authenticator isgnup and login process
    'security.ENCRYPTION_KEY' => 'Bxo123eXo1exO123',
    'security.IVSTR' => "fedcba9876543210",
    
    // see SiteController and views/site/maintenancemode.php...
    // Beware of time zones or DST issues!
    //'maintenanceMode' => [ 'start' => '2016-05-19 00:00:00', 'end' => '2016-05-19 23:59:59', 'message' => 'Sorry. This site is currently unavailable while we perform some maintenance.'],
];
