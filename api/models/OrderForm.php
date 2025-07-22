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
    public $source;
    public $split;

    private $_errors = [];

    public function rules()
    {
        return [
            // Обязательные поля
            [['fullName', 'phone', 'country', 'price', 'partnerId', 'accessToken', 'offerId'], 'required'],

            // fullName
            ['fullName', 'filter', 'filter' => '\api\models\OrderForm::cleanName'],

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
            ['comment', 'string', 'max' => 100, 'skipOnEmpty' => true],
            ['source', 'string', 'max' => 25, 'skipOnEmpty' => true],
            ['split', 'integer', 'min' => 1, 'max' => 999999, 'skipOnEmpty' => true],
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
            Yii::error("Ошибка валидации: Не найден партнер. partnerId={$this->partnerId}, accessToken={$this->accessToken}", 'api_form_validation');
        }
    }

    public function getErrorsList()
    {
        $messages = [
            'fullName' => 'wrong name or phone',
            'phone' => 'wrong name or phone',
            'country' => 'wrong country',
            'price' => 'wrong price',
            'partnerId' => 'wrong partnerId or access-token',
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
        $this->source = is_string($this->source) ? substr(trim($this->source), 0, 25) : null;
        $this->split = is_numeric($this->split) ? (int)$this->split : null;
    }

    public static function cleanPhone($value)
    {
        // Убираем всё, кроме цифр и '+'
        $cleaned = preg_replace('/[^\d\+]/', '', $value);
    
        if (!$cleaned) {
            return '';
        }
    
        // Если '+' есть, но он не первый — удаляем все '+' и возвращаем чистые цифры
        if (strpos($cleaned, '+') !== false && $cleaned[0] !== '+') {
            $cleaned = preg_replace('/\+/', '', $cleaned); // удаляем все '+'
            return $cleaned;
        }
    
        // Если '+' в начале — убираем дубли
        if ($cleaned[0] === '+') {
            // Удаляем все '+' кроме первого
            $cleaned = '+' . preg_replace('/\+/', '', substr($cleaned, 1));
        } else {
            // Если '+' нет — добавляем его в начало
            $cleaned = '+' . $cleaned;
        }
    
        return $cleaned;
    }

    public static function cleanName($value)
    {
        // Убираем все спецсимволы, оставляем только буквы и цифры + пробелы
        $cleaned = preg_replace('/[^\p{L}\p{N}\s]/u', '', trim($value));

        // Если длина меньше 3 символов — возвращаем стандартное значение
        if (mb_strlen($cleaned, 'UTF-8') < 3) {
            return 'Заказ обратного звонка';
        }

        return $cleaned;
    }
}