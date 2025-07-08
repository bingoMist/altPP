<?php

namespace common\models;

use yii\db\ActiveRecord;

class Partner extends ActiveRecord
{
    public static function tableName()
    {
        return 'partner';
    }
}