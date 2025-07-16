<?php

namespace backend\controllers;

use yii\web\Controller;
use yii\data\ArrayDataProvider;
use yii\helpers\FileHelper;
use yii\helpers\Html;

class LogController extends Controller
{
    public function actionIndex()
    {
        // --- Пути к логам ---
        $apiLogPath = '/var/www/m4leads/api/runtime/logs/api_errors.log';
        $nginxLogPath = '/var/log/nginx/error.log';
        $crmLogPath = '/var/www/m4leads/api/runtime/logs/send_order.log';
        $statusLogPath = '/var/www/m4leads/api/runtime/logs/check_status.log';
        $postbackLogPath = '/var/www/m4leads/api/runtime/logs/postback.log';

            // --- API ошибки ---
            $apiLogs = [];

            if (file_exists($apiLogPath)) {
                $lines = file($apiLogPath); // Все строки
                foreach ($lines as $line) {
                    $line = trim($line);

                    // Парсим временную метку и категорию
                    if (preg_match('/^\[(.*?)\] (.*?): ({.*})/', $line, $matches)) {
                        $timestamp = $matches[1]; // Например: 2025-07-16 00:14:52
                        $category = $matches[2];  // Например: Дубль заказа
                        $jsonStr = $matches[3];   // JSON с данными запроса

                        // Декодируем JSON
                        $data = json_decode($jsonStr, true);
                        $jsonOneLine = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                        $apiLogs[] = [
                            'time' => $timestamp,
                            'category' => $category,
                            'params' => $jsonOneLine,
                        ];
                    } else {
                        // Если формат не подходит — просто текст
                        if (preg_match('/^\[(.*?)\]/', $line, $timeMatch)) {
                            $timestamp = $timeMatch[1];
                            $category = 'raw';
                            $jsonOneLine = substr($line, strpos($line, ':') + 1);
                        } else {
                            $timestamp = date('Y-m-d H:i:s');
                            $category = 'raw';
                            $jsonOneLine = $line;
                        }

                        $apiLogs[] = [
                            'time' => $timestamp,
                            'category' => $category,
                            'params' => $jsonOneLine,
                        ];
                    }
                }

                // Сортировка: новые сверху
                $apiLogs = array_reverse($apiLogs);
            }

        // --- 2. Nginx Ошибки (полные записи) ---
        $nginxLogs = [];

        if (file_exists($nginxLogPath)) {
            $rawEntries = $this->parseNginxLog($nginxLogPath);

            foreach ($rawEntries as $entry) {
                // Теперь $entry — строка
                preg_match('/^(.*?)\s+\[error\]/', $entry, $timeMatch);
                $timestamp = $timeMatch[1] ?? '—';

                $nginxLogs[] = [
                    'time' => $timestamp,
                    'message' => nl2br(Html::encode($entry)),
                    'file' => 'nginx.log'
                ];
            }
        }

        // --- 3. CRM Ошибки ---
        $crmKeywords = ['missing country or offer', 'является дублем', 'Ошибка при отправке'];
        $crmLogs = $this->filterLogByKeywords($crmLogPath, $crmKeywords);

        // --- 4. Status Ошибки ---
        $statusKeywords = ['Ошибка при запросе статусов', 'Неверный формат ответа', 'Неизвестный статус'];
        $statusLogs = $this->filterLogByKeywords($statusLogPath, $statusKeywords);

        // --- 5. Постбек Ошибки ---
        $postbackKeywords = ['Ошибка при отправке постбэка', 'Ошибка при сохранении постбэка'];
        $postbackLogs = $this->filterLogByKeywords($postbackLogPath, $postbackKeywords);

        return $this->render('@backend/views/logs/index', [
            'apiProvider' => new ArrayDataProvider([
                'allModels' => $apiLogs,
                'pagination' => false,
            ]),
            'nginxProvider' => new ArrayDataProvider([
                'allModels' => $nginxLogs,
                'pagination' => false,
            ]),
            'crmProvider' => new ArrayDataProvider([
                'allModels' => $crmLogs,
                'pagination' => false,
            ]),
            'statusProvider' => new ArrayDataProvider([
                'allModels' => $statusLogs,
                'pagination' => false,
            ]),
            'postbackProvider' => new ArrayDataProvider([
                'allModels' => $postbackLogs,
                'pagination' => false,
            ]),
        ]);
    }

    private function filterLogByKeywords($filePath, $keywords)
    {
        $result = [];

        if (!file_exists($filePath)) {
            return $result;
        }

        //$lines = array_slice(file($filePath), -200); // последние 200 строк
        $lines = file($filePath); // Читаем все строки
        foreach ($lines as $line) {
            $line = trim($line);

            // Если это JSON — пытаемся декодировать
            $decoded = json_decode($line, true);
            if (is_array($decoded) && isset($decoded['message'])) {
                $line = $decoded['message'];
            }

            // Убедимся, что $line — это строка
            if (!is_string($line)) {
                continue; // пропускаем, если не строка
            }
            foreach ($keywords as $keyword) {
                if (strpos($line, $keyword) !== false) {
                    $result[] = [
                        'time' => '—',
                        'message' => $line,
                        'file' => basename($filePath),
                    ];
                    break;
                }
            }
        }

        return array_reverse($result); // новые сверху
    }

    private function parseNginxLog($filePath)
    {
        $result = [];
    
        if (!file_exists($filePath)) {
            return $result;
        }
    
        $content = file_get_contents($filePath);
        // Разделяем по времени начала ошибки
        $entries = preg_split('/(\d{4}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2}.*?\])/s', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    
        for ($i = 0; $i < count($entries); $i += 2) {
            $time = trim($entries[$i]);
            if (isset($entries[$i + 1])) {
                $body = trim($entries[$i + 1]);
                $result[] = "$time $body"; // одна строка
            }
        }
    
        return array_reverse($result);
    }
}