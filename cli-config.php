<?php
$CommandLine = 1;
require_once 'includes/initialize.php';

$Config = new Doctrine\DBAL\Configuration();
$ConnectionParams = [
    'dbname' => $Los_DB[0]['DB'],
    'user'  => $Los_DB[0]['UN'],
    'password' => $Los_DB[0]['PW'],
    'host' => $Los_DB[0]['HN'],
    'pdo' => $GLOBALS['DB'][0]
];
$Connection = Doctrine\DBAL\DriverManager::getConnection($ConnectionParams, $Config);

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($Connection)
));

return $helperSet;
?>