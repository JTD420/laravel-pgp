<?php

/**
 * Adds the necessary classmap to applications composer.json for PGP to work with Laravel.
 *
 * @return void
 */

//$basePath = realpath(__DIR__ . '/../../../');
//ToDo: Remove hardcoded path prior to releasing composer package!
$basePath = 'C:/xampp/htdocs/laravel-pgp-app/';

$composerJsonFile = $basePath.'composer.json';

$composerJson = json_decode(file_get_contents($composerJsonFile), true);

$composerJson['autoload']['classmap'] = ['vendor/singpolyma/openpgp-php/lib/'];

file_put_contents($composerJsonFile, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
