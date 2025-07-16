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

    public static function isDuplicate($partnerId, $offerId, $phone, $sub_id = null)
    {
        $query = self::find()
            ->where(['partner_id' => $partnerId])
            ->andWhere(['offer_id' => $offerId])
            ->andWhere(['phone' => $phone]);

        if (!empty($sub_id)) {
            $query->andWhere(['sub_id' => $sub_id]);
        }

        // Проверяем, был ли заказ создан за последние 30 минут
        $query->andWhere(['>', 'date', new Expression('NOW() - INTERVAL 30 MINUTE')]);

        $duplicate = $query->exists();

        if ($duplicate) {
            \Yii::error("Дублированный заказ: partnerId={$partnerId}, offerId={$offerId}, phone={$phone}, sub_id=" . ($sub_id ?? 'empty'), 'api_duplicate');
        }

    return $duplicate;
    }

    public function getStatusModel()
    {
        return $this->hasOne(Status::class, ['id' => 'status']);
    }
}