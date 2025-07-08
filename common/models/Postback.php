<?php

namespace common\models;

use yii\db\ActiveRecord;

class Postback extends ActiveRecord
{
    public static function tableName()
    {
        return 'postback';
    }

    public function rules()
    {
        return [
            [['url'], 'required'],
            [['url'], 'string'],
            [['send', 'status'], 'safe'],
        ];
    }
}