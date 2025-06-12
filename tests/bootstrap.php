<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';
/**
 * @var int<0, 999999> $phpVersion PHPStan type depends on PHP version ¯\_(ツ)_/¯
 */
$phpVersion = PHP_VERSION_ID;
// Load environment variables based on PHP version
if ($phpVersion >= 70100) { // PHP 7.1 or higher
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
} else {
    // Fallback for older PHP versions
    (new Dotenv())->load(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}