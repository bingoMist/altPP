<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Order;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class OrderController extends Controller
{
    public function actionIndex()
    {
        $request = Yii::$app->request;
    
        // Получаем данные для фильтров
        $partners = \common\models\Partner::find()->select(['id'])->asArray()->column();
        $offers = \common\models\Offer::find()->select(['id', 'name'])->asArray()->all();
        $countries = \common\models\Country::find()->select(['id', 'name'])->asArray()->all();
        $statuses = \common\models\Status::find()->select(['id', 'name'])->asArray()->all();
    
        // Преобразуем в удобный формат: [id => name]
        $offersList = ArrayHelper::map($offers, 'id', 'name');
        $countriesList = ArrayHelper::map($countries, 'id', 'name');
        $statusesList = ArrayHelper::map($statuses, 'id', 'name');
    
        // Фильтры из GET
        $searchModel = [
            'id' => $request->get('id'),
            'crm_order_id' => $request->get('crm_order_id'),
            'offer_id' => $request->get('offer_id'),
            'country_name' => $request->get('country_name'),
            'partner_id' => $request->get('partner_id'),
            'price' => $request->get('price'),
            'sub_id' => $request->get('sub_id'),
            'web_id' => $request->get('web_id'),
            'source' => $request->get('source'),
            'split' => $request->get('split'),
            'date_from' => $request->get('date_from', date('Y-m-d')),
            'date_to' => $request->get('date_to', date('Y-m-d')),
            'status' => $request->get('status'),
        ];
    
        // Создаём запрос с фильтрами
        $query = Order::find()->orderBy(['date' => SORT_DESC]);
    
        $query->andFilterWhere(['id' => $searchModel['id']])
            ->andFilterWhere(['like', 'crm_order_id', $searchModel['crm_order_id']])
            ->andFilterWhere(['offer_id' => $searchModel['offer_id']])
            ->andFilterWhere(['country_name' => $searchModel['country_name']])
            ->andFilterWhere(['partner_id' => $searchModel['partner_id']])
            ->andFilterWhere(['like', 'price', $searchModel['price']])
            ->andFilterWhere(['like', 'sub_id', $searchModel['sub_id']])
            ->andFilterWhere(['like', 'web_id', $searchModel['web_id']])
            ->andFilterWhere(['like', 'source', $searchModel['source']])
            ->andFilterWhere(['like', 'split', $searchModel['split']]);
    
        // Фильтр по дате (UTC+3 → UTC)
        $dateFrom = $searchModel['date_from'];
        $dateTo = $searchModel['date_to'];
    
        $dateFromUtc = (new \DateTime($dateFrom . ' 00:00:00', new \DateTimeZone('Europe/Moscow')))
            ->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    
        $dateToUtc = (new \DateTime($dateTo . ' 23:59:59', new \DateTimeZone('Europe/Moscow')))
            ->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    
        $query->andFilterWhere(['>=', 'date', $dateFromUtc])
            ->andFilterWhere(['<=', 'date', $dateToUtc]);
    
        // Фильтр по статусу
        if ($searchModel['status']) {
            $query->andWhere(['status' => $searchModel['status']]);
        }
    
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
        ]);
    
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'partners' => $partners,
            'offersList' => $offersList,
            'countriesList' => $countriesList,
            'statusesList' => $statusesList,
        ]);
    }
}