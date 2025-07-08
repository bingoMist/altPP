<?php

namespace common\models;

use yii\db\ActiveRecord;

class Country extends ActiveRecord
{
    public static function tableName()
    {
        return 'country';
    }
}