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
    ],
    'params' => $params,
];
