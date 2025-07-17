<?php

require(__DIR__ . '/../../vendor/autoload.php');

use Dotenv\Dotenv;

//.env
try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
} catch (\Exception $e) {
    $errorMessage = "❌ Ошибка загрузки .env: " . $e->getMessage();
    error_log($errorMessage);
     die("Error. not found .env.");
}

defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/main.php');

$application = new yii\web\Application($config);
$application->run();