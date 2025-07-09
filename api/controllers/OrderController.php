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
use yii\web\HttpException;
use common\models\Postback;
use api\components\PostbackHandler;// throw new \yii\web\HttpException(500, "Тестовое исключение");

class OrderController extends Controller
{
    public function actionAdd()
    {
        $request = \Yii::$app->request;
        $data = $request->isPost ? $request->getBodyParams() : $request->get();
        
        $form = new OrderForm();
        $form->fullName = $data['fullName'] ?? null;
        $form->phone = OrderForm::cleanPhone($data['phone'] ?? '');
        $form->country = $data['country'] ?? null;
        $form->price = $data['price'] ?? null;
        $form->partnerId = $data['partnerId'] ?? null;
        $form->accessToken = $data['access-token'] ?? null;
        $form->offerId = $data['offerId'] ?? null;
        $form->sub_id = $data['sub_id'] ?? null;
        $form->web_id = $data['web_id'] ?? null;
        $form->comment = $data['comment'] ?? null;

        if ($form->validate()) {
            $form->clearOptionalFields();

            // Получаем данные из связанных таблиц
            $country = Country::findOne(['id' => $form->country]);
            $offer = Offer::findOne(['id' => $form->offerId]);
            $partner = Partner::findOne([
                'id' => $form->partnerId,
                'access_token' => $form->accessToken,
            ]);

            if (!$country || !$offer || !$partner) {
                TelegramNotifier::sendValidationErrorMessage($data);
                return ['status' => 'ERROR', 'message' => 'wrong country or offer or partner'];
            }

            // Проверяем, является ли заказ тестовым
            if ($form->fullName === 'test') {
                $order = new Order();
                $order->name = $form->fullName;
                $order->phone = $form->phone;
                $order->offer_id = $form->offerId;
                $order->offer_name = $offer->name;
                $order->status = 4; // Тестовый заказ
                $order->country_name = $country->name;
                $order->partner_id = $form->partnerId;
                $order->price = $form->price;
                $order->comment = $form->comment;
                $order->sub_id = $form->sub_id;
                $order->web_id = $form->web_id;

                if ($order->save(false)) {
                    TelegramNotifier::sendSuccessMessage((int)$order->id);
                    return ['status' => 'OK', 'id' => $order->id, 'type' => 'test'];
                } else {
                    TelegramNotifier::sendSaveErrorMessage($data);
                    return ['status' => 'ERROR', 'message' => 'Не удалось сохранить тестовый заказ'];
                }
            }

            // Обычный заказ — проверка дубликата
            if (Order::isDuplicate($form->partnerId, $form->offerId, $form->phone, $form->sub_id)) {
                TelegramNotifier::sendDuplicateMessage($data);
                return ['status' => 'ERROR', 'message' => 'дубль заказа'];
            }

            // Сохраняем обычный заказ
            $order = new Order();
            $order->name = $form->fullName;
            $order->phone = $form->phone;
            $order->offer_id = $form->offerId;
            $order->offer_name = $offer->name;
            $order->status = 0;
            $order->country_name = $country->name;
            $order->partner_id = $form->partnerId;
            $order->price = $form->price;
            $order->comment = $form->comment;
            $order->sub_id = $form->sub_id;
            $order->web_id = $form->web_id;

            if ($order->save(false)) {
                TelegramNotifier::sendSuccessMessage((int)$order->id);
                PostbackHandler::add($order->id, $form->sub_id, 'lead');
                return ['status' => 'OK', 'id' => $order->id];
            } else {
                TelegramNotifier::sendSaveErrorMessage($data);
                return ['status' => 'ERROR', 'message' => 'Не удалось сохранить заказ'];
            }
        } else {
            TelegramNotifier::sendValidationErrorMessage($data);
            $errors = $form->getErrorsList();
            return ['status' => 'ERROR', 'message' => implode(', ', $errors)];
        }
    }
}