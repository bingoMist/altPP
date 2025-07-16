<?php

namespace api\components;

class SimpleLogger
{
    private const LOG_PATH = '/var/www/m4leads/api/runtime/logs/';

    public static function log($category, $message)
    {
        // Убедимся, что папка существует
        if (!is_dir(self::LOG_PATH)) {
            mkdir(self::LOG_PATH, 0775, true);
        }

        // Формируем имя файла
        $logFile = self::LOG_PATH . "$category.log";

        // Формируем строку с временем
        $time = date('Y-m-d H:i:s');
        $text = "[$time] $message\n";

        // Записываем в файл
        file_put_contents($logFile, $text, FILE_APPEND | LOCK_EX);
    }
}