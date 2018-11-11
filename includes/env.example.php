<?php
define ('SITE_ROOT', dirname(__DIR__).'/');
define ('SITE_URL', '//BareMinimum.test/');
define ('SITE_NAME', 'Bare Minimum Website');
define ('TIMEZONE', 'America/Los_Angeles');

// Encryption
define ('USE_COOKIE', TRUE);
define ('USE_CRACKER', TRUE);
define ('KEY1', '12345678901234567890123456789012');// Cookie - 32 Characters
define ('KEY2', '09876543210987654321098765432109');// Cracker - 32 Characters

// Database Connections
$Los_DB = [
    0 => [
        'HN' => 'DatabaseHostName',
        'UN' => 'DatabaseUserName',
        'PW' => 'DatabasePassword',
        'DB' => 'DatabaseName'
    ]
];
