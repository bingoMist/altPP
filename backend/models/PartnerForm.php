<?php

namespace backend\models;

use yii\base\Model;
use common\models\Partner;

class PartnerForm extends Model
{
    public $id;
    public $access_token;

    public function rules()
    {
        return [
            // `id` — опциональный, но должен быть целым и не отрицательным
            ['id', 'integer', 'min' => 1, 'when' => function ($model) {
                return $this->id !== null;
            }, 'whenClient' => "function (attribute) { return $('#partnerform-id').val() !== ''; }"],

            // Проверка на уникальность `id`
            ['id', 'checkUniqueID', 'when' => function ($model) {
                return $this->id !== null;
            }],

            // access_token — обязательный
            ['access_token', 'required', 'message' => 'Токен обязателен'],
            ['access_token', 'string', 'max' => 255],
        ];
    }

    public function checkUniqueID()
    {
        if (Partner::findOne($this->id)) {
            $this->addError('id', 'Партнёр с таким ID уже существует');
        }
    }

    public function loadFromModel(Partner $partner)
    {
        $this->id = $partner->id;
        $this->access_token = $partner->access_token;
    }

    public function save()
    {
        $partner = Partner::findOne($this->id) ?? new Partner(['id' => $this->id]);
        $partner->access_token = $this->access_token;

        if (!$partner->save(false)) {
            \Yii::error("Ошибка сохранения веба: " . json_encode($partner->getErrors()), 'web');
            return false;
        }

        $this->id = $partner->id;
        return true;
    }
}