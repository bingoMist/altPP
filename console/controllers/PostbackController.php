<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Postback;

class PostbackController extends Controller
{
    public function actionProcess()
    {
        echo "Начинаю обработку постбэков...\n";

        // Ищем все записи с send = false
        $postbacks = Postback::find()->where(['send' => false])->all();

        foreach ($postbacks as $item) {
            echo "Обрабатываю URL: {$item->url}\n";

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $item->url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
            ]);

            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $item->send = true;
                $item->status = '200 OK';
            } else {
                $item->status = "$httpCode";
                echo "Ошибка при отправке постбэка {$item->id}. Статус: " . $item->status . "\n";
            }

            if (!$item->save()) {
                TelegramNotifier::sendPostbackErrorMessage([
                    'url' => $item->url,
                    'errors' => $item->getErrors()
                ]);
                echo "Ошибка при сохранении постбэка {$item->id}. Ошибка: " . $item->getErrors() . "\n";
            }
        }

        echo "Обработка завершена.\n";
    }
}