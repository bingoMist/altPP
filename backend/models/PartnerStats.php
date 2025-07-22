<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ArrayDataProvider;
use common\models\Order;
use yii\helpers\ArrayHelper;

class PartnerStats extends Model
{
    public $dateFrom;
    public $dateTo;
    public $country;
    public $offerId;
    public $priceGroup;
    public $partnerId;
    public $source;
    public $web_id;


    public function rules()
    {
        return [
            [['dateFrom', 'dateTo'], 'safe'],
            [['country', 'offerId', 'partnerId'], 'integer'],
            [['priceGroup', 'source', 'web_id'], 'string'],
        ];
    }

    public function search($params)
    {
        $this->load($params);

        if ($this->dateFrom === null) {
            $this->dateFrom = date('Y-m-d');
        }
        if ($this->dateTo === null) {
            $this->dateTo = date('Y-m-d');
        }

        if (!$this->validate()) {
            return new ArrayDataProvider(['allModels' => []]);
        }

        // Основной запрос с учётом UTC+3
        $query = Order::find();

        // Переводим даты из UTC+3 -> UTC для корректного запроса к БД
        if ($this->dateFrom) {
            $fromUtc = new \DateTime($this->dateFrom . ' 00:00:00', new \DateTimeZone('Europe/Moscow')); // UTC+3
            $fromUtc->setTimezone(new \DateTimeZone('UTC'));
            $query->andWhere(['>=', 'date', $fromUtc->format('Y-m-d H:i:s')]);
        }

        if ($this->dateTo) {
            $toUtc = new \DateTime($this->dateTo . ' 23:59:59', new \DateTimeZone('Europe/Moscow')); // UTC+3
            $toUtc->setTimezone(new \DateTimeZone('UTC'));
            $query->andWhere(['<=', 'date', $toUtc->format('Y-m-d H:i:s')]);
        }

        $query->andFilterWhere([
            'country_id' => $this->country,
            'offer_id' => $this->offerId,
            'partner_id' => $this->partnerId,
            'source' => $this->source,
            'web_id' => $this->web_id,
        ]);

        $orders = $query->asArray()->all();

        // Группировка:
        // 1. partner_id
        // 2. source
        // 3. offer_name
        $result = [];

        foreach ($orders as $order) {
            $pId = $order['partner_id'];
            if (!isset($result[$pId])) {
                $result[$pId] = [
                    'partner_id' => $pId,
                    'total' => 0, 'approved' => 0, 'rejected' => 0,
                    'pending' => 0, 'invalid' => 0, 'duplicate' => 0,
                    '_sources' => [],
                ];
            }

            $result[$pId]['total']++;
            if ($order['status'] == 1) $result[$pId]['approved']++;
            if ($order['status'] == 2) $result[$pId]['rejected']++;
            if ($order['status'] == 6) $result[$pId]['pending']++;
            if ($order['status'] == 4) $result[$pId]['invalid']++;
            if ($order['status'] == 8) $result[$pId]['duplicate']++;

            // 2. По source
            $source = trim($order['source'] ?? '') === '' ? 'без источника' : $order['source'];
            if (!isset($result[$pId]['_sources'][$source])) {
                $result[$pId]['_sources'][$source] = [
                    'source' => $source,
                    'total' => 0, 'approved' => 0, 'rejected' => 0,
                    'pending' => 0, 'invalid' => 0, 'duplicate' => 0,
                    '_offers' => [],
                ];
            }

            $result[$pId]['_sources'][$source]['total']++;
            if ($order['status'] == 1) $result[$pId]['_sources'][$source]['approved']++;
            if ($order['status'] == 2) $result[$pId]['_sources'][$source]['rejected']++;
            if ($order['status'] == 6) $result[$pId]['_sources'][$source]['pending']++;
            if ($order['status'] == 4) $result[$pId]['_sources'][$source]['invalid']++;
            if ($order['status'] == 8) $result[$pId]['_sources'][$source]['duplicate']++;

            // 3. По offer_name
            $offerName = $order['offer_name'] ?? 'без названия';
            if (!isset($result[$pId]['_sources'][$source]['_offers'][$offerName])) {
                $result[$pId]['_sources'][$source]['_offers'][$offerName] = [
                    'offer_name' => $offerName,
                    'total' => 0, 'approved' => 0, 'rejected' => 0,
                    'pending' => 0, 'invalid' => 0, 'duplicate' => 0,
                ];
            }

            $result[$pId]['_sources'][$source]['_offers'][$offerName]['total']++;
            if ($order['status'] == 1) $result[$pId]['_sources'][$source]['_offers'][$offerName]['approved']++;
            if ($order['status'] == 2) $result[$pId]['_sources'][$source]['_offers'][$offerName]['rejected']++;
            if ($order['status'] == 6) $result[$pId]['_sources'][$source]['_offers'][$offerName]['pending']++;
            if ($order['status'] == 4) $result[$pId]['_sources'][$source]['_offers'][$offerName]['invalid']++;
            if ($order['status'] == 8) $result[$pId]['_sources'][$source]['_offers'][$offerName]['duplicate']++;
        }

        $data = array_values($result);
        return new ArrayDataProvider([
            'allModels' => $data,
            'key' => 'partner_id',
            'pagination' => false,
        ]);
    }
}