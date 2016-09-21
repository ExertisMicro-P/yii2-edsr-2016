<?php
return [
    'CSVProcesser.isRelative'    => true,
    'CSVProcesser.filepath'      => '/../uploads/in/', // Requires trailing /
    'CSVProcesser.movetopath'    => '/../uploads/archive/archive/', // Requires trailing /
    'CSVProcesser.errorfilepath' => '/../uploads/archive/error/', // Requires trailing /

    'mockKeys'                   => true, // true means we will not attempt to fetch keys from eZtorm, but will return a fake

    'account.dropshipDirectory'  => '@console/runtime/asn',
];
