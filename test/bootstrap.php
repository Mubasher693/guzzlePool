<?php

use Dotenv\Dotenv;

require 'vendor/autoload.php'; // Include the Composer autoloader

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

set_include_path(__DIR__);
spl_autoload_extensions(".php");
spl_autoload_register();