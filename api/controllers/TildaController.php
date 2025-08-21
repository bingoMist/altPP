<?php

namespace api\controllers;

use yii\rest\Controller;
use yii\web\Request;
use api\models\OrderForm;
use common\models\Country;
use common\models\Offer;
use common\models\Partner;
use common\models\Order;
use api\components\TelegramNotifier;
use Yii;
use yii\web\HttpException;
use api\components\PostbackHandler;

class TildaController extends Controller
{
    public function actionWebhook()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $request = \Yii::$app->request;
            $data = $request->isPost ? $request->getBodyParams() : $request->get();

            if (empty($data)) {
                TelegramNotifier::sendMessage('Tilda: пустой запрос');
                return ['status' => 'ERROR', 'message' => 'Empty data'];
            }

            // Декодируем ключи массива (например, %D0%98%D0%BC%D1%8F → Имя)
            $decodedData = [];
            foreach ($data as $key => $value) {
                $decodedKey = urldecode($key);
                $decodedData[$decodedKey] = is_string($value) ? urldecode($value) : $value;
            }

            $form = new OrderForm();
            $form->fullName = $decodedData['Имя'] ?? $decodedData['name'] ?? $decodedData['Name'] ?? null;
            $form->phone = $decodedData['Телефон'] ?? $decodedData['phone'] ?? null;
            $form->country = $decodedData['country'] ?? null;
            $form->price = $decodedData['price'] ?? null;
            $form->partnerId = $decodedData['partnerId'] ?? $decodedData['partner_id'] ?? null;
            $form->accessToken = $decodedData['access-token'] ?? $decodedData['access_token'] ?? null;
            $form->offerId = $decodedData['offerId'] ?? $decodedData['offer_id'] ?? null;
            $form->sub_id = $decodedData['sub_id'] ?? $decodedData['source'] ?? $decodedData['tranid'] ?? null;
            $form->web_id = $decodedData['web_id'] ?? null;
            $form->comment = $decodedData['comment'] ?? null;

            // 1. Валидация
            if (!$form->validate()) {
                $errors = $form->getErrorsList();
                $errorText = implode(', ', $errors);
                $text = 'ошибка валидации: ' . $errorText . ' | данные: ';
                $logParams = [];
                foreach ($decodedData as $key => $value) {
                    $logParams[] = "$key=$value";
                }
                $text .= implode(', ', $logParams);
                TelegramNotifier::sendMessage($text);
                return ['status' => 'ERROR', 'message' => $errorText];
            }

            $form->clearOptionalFields();

            // 2. Проверка связанных сущностей
            $partner = Partner::findOne([
                'id' => $form->partnerId,
                'access_token' => $form->accessToken,
            ]);
            $country = Country::findOne(['id' => $form->country]);
            $offer = Offer::findOne(['id' => $form->offerId]);

            if (!$partner) {
                $text = 'ошибка: партнер не найден | partnerId=' . $form->partnerId;
                TelegramNotifier::sendMessage($text);
                return ['status' => 'ERROR', 'message' => 'Invalid partner'];
            }
            if (!$country) {
                $text = 'ошибка: страна не найдена | country=' . $form->country;
                TelegramNotifier::sendMessage($text);
                return ['status' => 'ERROR', 'message' => 'Invalid country'];
            }
            if (!$offer) {
                $text = 'ошибка: оффер не найден | offerId=' . $form->offerId;
                TelegramNotifier::sendMessage($text);
                return ['status' => 'ERROR', 'message' => 'Invalid offer'];
            }

            // 3. Проверка дубликата
            if (Order::isDuplicate($form->partnerId, $form->offerId, $form->phone, $form->sub_id)) {
                $text = 'дубль заказа. параметры: ';
                foreach ($decodedData as $key => $value) {
                    $text .= "$key=$value, ";
                }
                $text = rtrim($text, ', ');
                TelegramNotifier::sendMessage($text);
                return ['status' => 'ERROR', 'message' => 'Duplicate order'];
            }

            // 4. Сохранение заказа
            $order = new Order();
            $order->name = $form->fullName;
            $order->phone = OrderForm::cleanPhone($form->phone);
            $order->offer_id = $form->offerId;
            $order->offer_name = $offer->name;
            $order->country_id = $country->id;
            $order->country_name = $country->name;
            $order->partner_id = $form->partnerId;
            $order->price = $form->price;
            $order->comment = $form->comment ?? NULL;
            $order->sub_id = $form->sub_id ?? NULL;
            $order->web_id = $form->web_id ?? NULL;
            $order->source = 'tilda';
            if (($form->fullName == 'test') || ($form->fullName == 'тест')) {
                $order->status = 4; // Некорректный
            } else {
                $order->status = 0; // Новый
            }

            if ($order->save(false)) {
                $text = 'ТИЛЬДА_успешный_прием_данных.заказ_' . $order->id;
                TelegramNotifier::sendMessage($text);
                PostbackHandler::add($order->id, $form->sub_id, 'lead');
                return ['status' => 'OK', 'id' => $order->id];
            } else {
                $errors = $order->getErrors();
                $text = 'ошибка сохранения в БД: ' . json_encode($errors, JSON_UNESCAPED_UNICODE) . ' | данные: ';
                $logParams = [];
                foreach ($decodedData as $key => $value) {
                    $logParams[] = "$key=$value";
                }
                $text .= implode(', ', $logParams);
                TelegramNotifier::sendMessage($text);
                return ['status' => 'ERROR', 'message' => 'Save failed'];
            }

        } catch (\Throwable $e) {
            // 🔥 Критическая ошибка — поймали всё
            $text = '❌ ФАТАЛЬНАЯ ОШИБКА в TildaController: ' . $e->getMessage() . "\n";
            $text .= 'Файл: ' . $e->getFile() . ' : ' . $e->getLine() . "\n";
            $text .= 'Trace: ' . $e->getTraceAsString();
            TelegramNotifier::sendMessage($text);
            return ['status' => 'ERROR', 'message' => 'Server error'];
        }
    }
}