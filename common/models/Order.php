<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\db\Expression;

class Order extends ActiveRecord
{
    public static function tableName()
    {
        return 'order';
    }

    public function rules()
    {
        return [
            [['name', 'phone', 'offer_id', 'offer_name', 'status', 'country_name', 'partner_id', 'price'], 'required'],
            [['comment', 'sub_id', 'web_id', 'crm_order_id'], 'safe'],
        ];
    }
    public static function isDuplicate($partnerId, $offerId, $phone, $subId = null)
    {
        $query = self::find()
            ->where(['partner_id' => $partnerId])
            ->andWhere(['offer_id' => $offerId])
            ->andWhere(['phone' => $phone]);

        if (!empty($subId)) {
            $query->andWhere(['sub_id' => $subId]);
        }

        // Проверяем, был ли заказ создан за последние 30 минут
        $query->andWhere(['>', 'date', new Expression('NOW() - INTERVAL 30 MINUTE')]);

        return (bool)$query->one();
    }
}