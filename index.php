<?php
require 'vendor/autoload.php'; // Include the Composer autoloader

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

set_include_path(__DIR__);
spl_autoload_extensions(".php");
spl_autoload_register();

$operation = new src\HotelDirectoryRemainder($_ENV['DRIVE_URL'], $_ENV['BATCH_SIZE']);
$operation->hotelDirectory();