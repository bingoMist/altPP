<?php

namespace console\controllers;

use yii\console\Controller;
use common\models\Order;
use common\models\Country;
use common\models\Offer;
use api\components\TelegramNotifier;

class SendOrderController extends Controller
{
    const CRM_URL = 'http://api.m4crm.com/v1/order/create?access-token=4Eg5qa2QnBVca_MLqRw0seXZXz0F84x6';
    const ACCESS_TOKEN = '4Eg5qa2QnBVca_MLqRw0seXZXz0F84x6';

    public function actionProcess()
    {
        echo "Начинаю отправку заказов в CRM...\n";

        // Ищем заказы со статусом 0
        $orders = Order::find()->where(['status' => 0])->all();

        foreach ($orders as $order) {
            // Получаем связанные данные
            $country = Country::findOne(['name' => $order->country_name]);
            $offer = Offer::findOne($order->offer_id);

            if (!$country || !$offer) {
                TelegramNotifier::sendCrmErrorMessage([
                    'orderId' => $order->id,
                    'errors' => ['missing country or offer']
                ]);
                continue;
            }

            // Формируем данные для отправки
            $postData = [
                // 'access-token' => self::ACCESS_TOKEN,
                'country_code' => $country->country_iso,
                'fio' => $order->name,
                'phone' => $order->phone,
                'source' => 'API',
                'product_id' => $offer->crm_id,
                'web_id' => $order->partner_id,
                'price' => $order->price,
                'lead_price' => 1,
                'comment' => $order->comment,
                'external_id' => $order->id,
                'lead_price_currency' => 1,
                'is_payed' => 0,
            ];

            // Отправляем запрос
            $ch = curl_init(self::CRM_URL);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Проверяем ответ
            if ($httpCode === 200 && !empty($response)) {
                $responseData = json_decode($response, true);

                if (isset($responseData['id'])) {
                    // Сохраняем crm_order_id и меняем статус
                    $order->crm_order_id = $responseData['id'];
                    $order->status = 6;
                    $order->save(false);

                    echo "Заказ {$order->id} успешно отправлен.\n";
                    continue;
                }
            }

            // Если ошибка
            TelegramNotifier::sendCrmErrorMessage([
                'orderId' => $order->id,
                'sentData' => $postData,
                'response' => $response,
                'httpCode' => $httpCode,
            ]);

            echo "Ошибка при отправке заказа {$order->id}\n";
        }

        echo "Обработка завершена.\n";
    }
}