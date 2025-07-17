<?php

require(__DIR__ . '/../../vendor/autoload.php');

use Dotenv\Dotenv;
use api\components\TelegramNotifier;

//.env
try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
} catch (\Exception $e) {
    $errorMessage = "❌ Ошибка загрузки .env: " . $e->getMessage();
    TelegramNotifier::sendMessage($errorMessage);
    error_log($errorMessage);
     die("Произошла критическая ошибка. Проверьте файл .env.");
}

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/main.php');

$application = new yii\web\Application($config);
$application->run();