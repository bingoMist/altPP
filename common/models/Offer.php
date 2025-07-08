<?php

namespace common\models;

use yii\db\ActiveRecord;

class Offer extends ActiveRecord
{
    public static function tableName()
    {
        return 'offer';
    }
}