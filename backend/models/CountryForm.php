<?php

namespace backend\models;

use yii\base\Model;
use common\models\Country;

class CountryForm extends Model
{
    public $id;
    public $name;
    public $country_iso;

    public function rules()
    {
        return [
            [['name', 'country_iso'], 'required'],
            ['name', 'string', 'max' => 255],
            ['country_iso', 'string', 'max' => 2],
        ];
    }

    public function loadFromModel(Country $country)
    {
        $this->id = $country->id;
        $this->name = $country->name;
        $this->country_iso = $country->country_iso;
    }

    public function save()
    {
        $country = Country::findOne($this->id) ?? new Country();
        $country->name = $this->name;
        $country->country_iso = $this->country_iso;
    
        if (!$country->save(false)) {
            \Yii::error("Ошибка сохранения страны: " . json_encode($country->getErrors()), 'country');
            return false;
        }
    
        $this->id = $country->id;
        return true;
    }
}