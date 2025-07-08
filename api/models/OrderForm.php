<?php

namespace api\models;

use Yii;
use yii\base\Model;
use common\models\Country;
use common\models\Partner;
use common\models\Offer;

class OrderForm extends Model
{
    public $fullName;
    public $phone;
    public $country;
    public $price;
    public $partnerId;
    public $accessToken;
    public $offerId;
    public $sub_id;
    public $web_id;
    public $comment;

    private $_errors = [];

    public function rules()
    {
        return [
            // Обязательные поля
            [['fullName', 'phone', 'country', 'price', 'partnerId', 'accessToken', 'offerId'], 'required'],

            // fullName
            ['fullName', 'filter', 'filter' => function ($value) {
                return preg_replace('/[^\p{L}\p{N}\s]/u', '', trim($value)); // Убираем эмодзи и лишние символы
            }],
            ['fullName', 'string', 'min' => 3, 'max' => 50],

            // phone
            ['phone', 'string', 'min' => 3, 'max' => 30],

            // country
            ['country', 'integer', 'max' => 999],
            ['country', 'exist', 'skipOnError' => true, 'targetClass' => Country::class, 'targetAttribute' => 'id'],

            // price
            ['price', 'number', 'numberPattern' => '/^[0-9]+(\.[0-9]+)?$/'],
            ['price', 'string', 'max' => 10],

            // partnerId + accessToken
            ['partnerId', 'validatePartner'],

            // offerId
            ['offerId', 'integer', 'max' => 99999],
            ['offerId', 'exist', 'skipOnError' => true, 'targetClass' => Offer::class, 'targetAttribute' => 'id'],

            // Дополнительные поля
            ['sub_id', 'string', 'max' => 50, 'skipOnEmpty' => true],
            ['web_id', 'string', 'max' => 50, 'skipOnEmpty' => true],
            ['comment', 'string', 'max' => 50, 'skipOnEmpty' => true],
        ];
    }

    public function validatePartner($attribute)
    {
        $partner = Partner::findOne([
            'id' => $this->partnerId,
            'access_token' => $this->accessToken,
        ]);

        if (!$partner) {
            $this->addError('partnerId', 'partnerId or access-token');
        }
    }

    public function getErrorsList()
    {
        $messages = [
            'fullName' => 'wrong name or phone',
            'phone' => 'wrong name or phone',
            'country' => 'wrong country',
            'price' => 'wrong price',
            'partnerId' => 'partnerId or access-token',
            'offerId' => 'wrong offerId',
        ];

        $result = [];
        foreach ($this->getErrors() as $field => $errors) {
            if (isset($messages[$field])) {
                $result[] = $messages[$field];
            }
        }

        return array_unique($result);
    }

    public function clearOptionalFields()
    {
        $this->sub_id = is_string($this->sub_id) ? substr(trim($this->sub_id), 0, 50) : null;
        $this->web_id = is_string($this->web_id) ? substr(trim($this->web_id), 0, 50) : null;
        $this->comment = is_string($this->comment) ? substr(trim($this->comment), 0, 50) : null;
    }

    public static function cleanPhone($value)
    {
        // Убираем всё, кроме цифр и '+'
        $cleaned = preg_replace('/[^\d\+]/', '', $value);

        if (!$cleaned) {
            return '';
        }

        // Если '+' не первый символ — удаляем его из строки
        if ($cleaned[0] !== '+') {
            $cleaned = preg_replace('/\+/', '', $cleaned); // удаляем все '+'
            return $cleaned;
        }

        // Оставляем только один '+' в начале
        $cleaned = preg_replace('/\+/u', '', $cleaned);
        $cleaned = '+' . $cleaned;

        return $cleaned;
    }
}