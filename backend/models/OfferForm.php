<?php

namespace backend\models;

use yii\base\Model;
use common\models\Offer;

class OfferForm extends Model
{
    public $id;
    public $crm_id;
    public $name;

    public function rules()
    {
        return [
            [['crm_id', 'name'], 'required'],
            ['crm_id', 'string', 'max' => 255],
            ['name', 'string', 'max' => 255],
        ];
    }

    public function loadFromModel(Offer $offer)
    {
        $this->id = $offer->id;
        $this->crm_id = $offer->crm_id;
        $this->name = $offer->name;
    }

    public function save()
    {
        $offer = Offer::findOne($this->id) ?? new Offer();
        $offer->name = $this->name;
        $offer->crm_id = $this->crm_id;
        
        if (!$offer->save(false)) {
            \Yii::error("Ошибка сохранения оффера: " . json_encode($offer->getErrors()), 'offer');
            return false;
        }
    
        $this->id = $offer->id; // ← обновляем id после сохранения
        return true;
    }
}