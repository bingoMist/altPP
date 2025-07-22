<?php

namespace backend\controllers;

use yii\web\Controller;
use backend\models\PartnerStats;
use Yii;

class StatisticsController extends Controller
{
    public function actionPartner()
    {
        $params = Yii::$app->request->queryParams;
        $searchModel = new PartnerStats();
        $searchModel->attributes = [
            'dateFrom' => $params['dateFrom'] ?? null,
            'dateTo' => $params['dateTo'] ?? null,
            'country' => $params['country'] ?? null,
            'offerId' => $params['offerId'] ?? null,
            'partnerId' => $params['partnerId'] ?? null,
            'source' => $params['source'] ?? null,
            'web_id' => $params['web_id'] ?? null,
        ];

        $dataProvider = $searchModel->search($params);
        $sources = \common\models\Order::find()
        ->select(['source'])
        ->distinct()
        ->andWhere(['IS NOT', 'source', null])
        ->andWhere(['<>', 'source', '']) // исключаем пустые строки
        ->orderBy('source')
        ->asArray()
        ->column();
        $sources = array_filter(array_map('trim', $sources));
        sort($sources);

        return $this->render('partner', [
            'model' => $searchModel,
            'dataProvider' => $dataProvider,
            'sources' => $sources,
        ]);
    }
}