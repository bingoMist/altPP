<?php

namespace api\components;

use yii\web\ErrorHandler;
use yii\helpers\Url;
use Yii;

class TelegramErrorHandler extends ErrorHandler
{
    public function handleException($exception)
    {
        $text = "🚨 Ошибка: " . get_class($exception) . "\n";
        $text .= "Сообщение: " . $exception->getMessage() . "\n";
        $text .= "Файл: " . $exception->getFile() . "\n";
        $text .= "Строка: " . $exception->getLine() . "\n";
        $text .= "Трассировка:\n" . $exception->getTraceAsString();

        // Отправляем в Telegram
        TelegramNotifier::sendMessage($text);

        // Продолжаем стандартную обработку ошибок
        parent::handleException($exception);
    }
}