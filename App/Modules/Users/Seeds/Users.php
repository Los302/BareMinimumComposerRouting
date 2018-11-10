<?php
include '../../../../includes/initialize.php';

$seeder = new tebazil\dbseeder\Seeder($GLOBALS['DB'][0]);
$generator = $seeder->getGeneratorConfigurator();
$faker = $generator->getFakerConfigurator();

$UsersVals = [
    [
        'id' => 1,
        'username' => 'Admin',
        'password' => 'Admin',
        'email' => 'Carlos@LosPrograms.com',
        'role' => '|ADMIN|USER|',
        'active' => 1
    ]
];
$seeder->table('users')->data($UsersVals);

$seeder->refill();
