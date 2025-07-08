<?php

namespace api\components;

use yii\web\ErrorHandler;
use yii\helpers\Url;
use Yii;

class TelegramErrorHandler extends ErrorHandler
{
    public function handleException($exception)
    {
        // Сначала стандартная обработка ошибок
        parent::handleException($exception);

        // Теперь отправляем в Telegram
        $message = "Ошибка:\n";
        $message .= "Код: " . $exception->getCode() . "\n";
        $message .= "Сообщение: " . $exception->getMessage() . "\n";
        $message .= "Файл: " . $exception->getFile() . "\n";
        $message .= "Строка: " . $exception->getLine() . "\n";
        $message .= "Trace:\n" . $exception->getTraceAsString();

        // Ограничиваем длину
        if (strlen($message) > 4096) {
            $message = substr($message, 0, 4096 - 30) . "... [обрезано]";
        }

        $botToken = '5237886982:AAG3AK8ZYLBG7BaBGGlRe3UNK4MFKeVee1c';
        $chatId = '-4537041942';
        $text = urlencode($message);
        $url = "https://api.telegram.org/bot5237886982:AAG3AK8ZYLBG7BaBGGlRe3UNK4MFKeVee1c/sendMessage?chat_id=-4537041942&text=" . $text;

        // Отправляем через curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // только для теста
        curl_exec($ch);
        curl_close($ch);
    }
}