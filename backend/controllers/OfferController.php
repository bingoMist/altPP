<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\models\OfferForm;
use common\models\Offer;
use yii\data\ArrayDataProvider;

class OfferController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Только авторизованные
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $offers = Offer::find()->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $offers,
            'pagination' => false,
        ]);

        return $this->render('@backend/views/default/list-offer', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new OfferForm();
    
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Оффер создан');
            return $this->redirect(['edit', 'id' => $model->id]);
        }
    
        return $this->render('@backend/views/default/create', [
            'model' => $model,
            'title' => 'Создать оффер',
        ]);
    }
    
    public function actionEdit($id)
    {
        $offer = Offer::findOne($id);
        if (!$offer) throw new NotFoundHttpException('Оффер не найден');
    
        $model = new OfferForm();
        $model->loadFromModel($offer);
    
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Изменения сохранены');
            return $this->redirect(['edit', 'id' => $model->id]);
        }
    
        return $this->render('@backend/views/default/edit', [
            'model' => $model,
            'title' => 'Редактировать оффер',
        ]);
    }
}