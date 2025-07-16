<?php

$params = require __DIR__ . '/params.php';

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'db' => require __DIR__ . '/../../common/config/db.php',
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'httpClient' => [
            'class' => 'yii\httpclient\Client',
            'transport' => 'yii\httpclient\CurlTransport',
        ],
        'response' => [
            'format' => \yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'user' => [
            'identityClass' => 'yii\web\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'errorHandler' => [
            'class' => 'api\components\TelegramErrorHandler',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'order/add' => 'order/add',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
// Основной логгер (app.log)
[
    'class' => 'yii\log\FileTarget',
    'levels' => ['error', 'warning', 'info'],
    'logFile' => '@runtime/logs/app.log',
],
// Тестовый логгер
[
    'class' => 'yii\log\FileTarget',
    'categories' => ['test'],
    'logFile' => '@runtime/logs/test.log',
    'levels' => ['error', 'warning', 'info', 'trace'],
],
// Ошибки дубликатов
[
    'class' => 'yii\log\FileTarget',
    'categories' => ['api_duplicate'],
    'logFile' => '@runtime/logs/send_order.log',
    'levels' => ['error', 'info'],
],
// Ошибки формы
[
    'class' => 'yii\log\FileTarget',
    'categories' => ['api_form_validation'],
    'logFile' => '@runtime/logs/form_validation.log',
    'levels' => ['error', 'info'],
],
            ],
        ],
    ],
    'params' => $params,
];
