<?php

namespace api\components;

use Yii;
use yii\helpers\ArrayHelper;

class TelegramNotifier
{
    private const BOT_TOKEN = '5237886982:AAG3AK8ZYLBG7BaBGGlRe3UNK4MFKeVee1c';
    private const CHAT_ID = '-4537041942';

    public static function sendSuccessMessage(int $orderId)
    {
        $text = 'успешный_прием_данных.заказ_' . $orderId;
        self::sendMessage($text);
    }

    public static function sendSaveErrorMessage(array $params = [])
    {
        if (!empty($params)) {
            // Формируем строку с полученными параметрами
            $filteredParams = [];
            foreach ($params as $key => $value) {
                if (is_scalar($value)) {
                    $filteredParams[] = "$key=" . $value;
                } else {
                    $filteredParams[] = "$key=" . json_encode($value);
                }
            }

            $text = 'не удалось сохранить заказ: ' . implode(', ', $filteredParams);
        } else {
            $text = 'не удалось сохранить заказ';
        }

        self::sendMessage($text);
    }

    public static function sendValidationErrorMessage(array $params)
    {
        $filteredParams = [];

    foreach ($params as $key => $value) {
        if (is_scalar($value)) {
            // Если значение простое (string, int, float, bool)
            $filteredParams[] = "$key=" . $value;
        } else {
            // Если это массив или объект — сериализуем как JSON
            $filteredParams[] = "$key=" . json_encode($value);
        }
    }

    $text = 'не удалось сохранить заказ: ' . implode(', ', $filteredParams);
    self::sendMessage($text);
    }

    public static function sendPostbackErrorMessage(array $params)
    {
        $text = "❌ Ошибка postback:\n";

        foreach ($params as $key => $value) {
            if (is_scalar($value)) {
                $text .= "$key: $value\n";
            } else {
                $text .= "$key: " . json_encode($value, JSON_UNESCAPED_UNICODE) . "\n";
            }
        }

        self::sendMessage($text);
    }

    public static function sendCrmErrorMessage(array $data)
    {
        $text = "❌ Ошибка отправки в CRM:\n\n";

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            $text .= "$key: " . substr((string)$value, 0, 200) . "\n";
        }

        self::sendMessage($text);
    }

    public static function sendStatusCheckErrorMessage(array $data)
        {
            $text = "❌ Ошибка опроса статусов:\n\n";
            foreach ($data as $key => $value) {
                $text .= "$key: $value\n";
            }
            self::sendMessage($text);
        }

    public static function sendUnknownStatusMessage(array $data)
        {
            $text = "⚠️ Неизвестный статус в CRM:\n";
            foreach ($data as $key => $value) {
                $text .= "$key: $value\n";
            }
            self::sendMessage($text);
        }

    public static function sendDuplicateMessage(array $params)
    {
        $text = 'дубль заказа. параметры: ';
        foreach ($params as $key => $value) {
            $text .= "$key=" . (is_scalar($value) ? $value : json_encode($value)) . ', ';
        }
        $text = rtrim($text, ', ');

        self::sendMessage($text);
    }

    public static function sendMessage(string $text)
    {
        $token = '5237886982:AAG3AK8ZYLBG7BaBGGlRe3UNK4MFKeVee1c';
        $chatId = '-4537041942';

        $url = "https://api.telegram.org/bot" . $token . "/sendMessage?chat_id=" . $chatId . "&text=" . urlencode($text);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        curl_close($ch);
    }
}