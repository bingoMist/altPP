<?php

namespace console\controllers;

use yii\console\Controller;
use common\models\Order;
use api\components\PostbackHandler;
use api\components\TelegramNotifier;

class CheckStatusController extends Controller
{
    const CRM_URL = 'http://api.m4crm.com/v1/order/status';
    const ACCESS_TOKEN = '4Eg5qa2QnBVca_MLqRw0seXZXz0F84x6';

    // Статусы, которые не меняют текущий статус (остаются 6)
    private static $keepStatuses = [
        'intermediate', 'postponed_delivery', 'New', 'Process', 'new', 'processed',
        'postponed', 'answering_machine', 'manual_call', 'pre_accepted', 'wait_prepayment'
    ];

    // Статусы, которые переводят в статус 1 (sale)
    private static $statusToSale = [
        'Confirmed', 'confirmed', 'delivery', 'delivered', 'return', 'equipment',
        'ready_to_give', 'called_canceled'
    ];

    // Статусы, которые переводят в статус 2 (rejected)
    private static $statusToRejected = [
        'Rejected', 'canceled'
    ];

    // Статусы, которые переводят в статус 4 (invalid/double/etc.)
    private static $statusToInvalid = [
        'Incorrect', 'invalid', 'double'
    ];

    public function actionProcess()
    {
        echo "Начинаю опрос статусов из CRM...\n";

        // Ищем заказы со статусом 6 и непустым crm_order_id
        $orders = Order::find()
            ->where(['status' => 6])
            ->andWhere(['not', ['crm_order_id' => null]])
            ->all();

        if (empty($orders)) {
            echo "Нет заказов для проверки статуса.\n";
            return;
        }

        // Разбиваем на группы по 30 заказов
        $chunks = array_chunk($orders, 30);

        foreach ($chunks as $chunk) {
            $ids = implode(',', array_map(fn($o) => $o->crm_order_id, $chunk));

            // Формируем URL запроса
            $url = self::CRM_URL . '?access-token=' . self::ACCESS_TOKEN . '&ids=' . urlencode($ids);

            // Выполняем запрос
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || empty($response)) {
                TelegramNotifier::sendStatusCheckErrorMessage([
                    'note' => 'Ошибка при запросе статусов',
                    'http_code' => $httpCode,
                    'response' => $response,
                ]);
                continue;
            }

            $responseData = json_decode($response, true);

            if (!is_array($responseData)) {
                TelegramNotifier::sendStatusCheckErrorMessage([
                    'note' => 'Неверный формат ответа от CRM',
                    'response' => $response,
                ]);
                continue;
            }

            foreach ($responseData as $item) {
                $crmId = $item['id'] ?? null;

                if (!$crmId) continue;

                // Находим соответствующий заказ
                $order = Order::findOne(['crm_order_id' => $crmId]);

                if (!$order) continue;

                $status = strtolower($item['status'] ?? '');

                // Обновляем комментарий
                if (!empty($item['comment'])) {
                    $order->comment = substr($item['comment'], 0, 50);
                }

                // Определяем новый статус
                if (in_array($status, self::$keepStatuses)) {
                    // Оставляем статус 6
                } elseif (in_array($status, self::$statusToSale)) {
                    $order->status = 1;
                    PostbackHandler::add($order->id, $order->sub_id, 'sale');
                } elseif (in_array($status, self::$statusToRejected)) {
                    $order->status = 2;
                    PostbackHandler::add($order->id, $order->sub_id, 'rejected');
                } elseif (in_array($status, self::$statusToInvalid)) {
                    $order->status = 4;
                    PostbackHandler::add($order->id, $order->sub_id, 'rejected');
                } else {
                    // Неизвестный статус — можно логировать
                    TelegramNotifier::sendUnknownStatusMessage([
                        'order_id' => $order->id,
                        'crm_status' => $status,
                    ]);
                }

                // Сохраняем изменения
                $order->save(false);
                echo "Заказ {$order->id} обновлён. Новый статус: {$order->status}\n";
            }
        }

        echo "Обработка завершена.\n";
    }
}