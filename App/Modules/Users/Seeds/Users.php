<?php
include dirname(dirname(dirname(dirname(__DIR__)))).'/includes/initialize.php';

$seeder = new tebazil\dbseeder\Seeder($GLOBALS['DB'][0]);
$generator = $seeder->getGeneratorConfigurator();
$faker = $generator->getFakerConfigurator();

$UsersVals = [
    [
        'id' => 1,
        'username' => 'Admin',
        'password' => 'Admin',
        'email' => 'Someone@LosPrograms.com',
        'role' => '|ADMIN|',
        'active' => 1
    ],
    [
        'id' => 2,
        'username' => 'User',
        'password' => 'User',
        'email' => 'Someone@LosPrograms.com',
        'role' => '|USER|',
        'active' => 1
    ]
];
$seeder->table('users')->data($UsersVals);

$seeder->refill();
