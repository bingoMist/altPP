<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\models\CountryForm;
use common\models\Country;
use yii\data\ArrayDataProvider;

class CountryController extends Controller
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
        $countries = Country::find()->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $countries,
            'pagination' => false,
        ]);

        return $this->render('@backend/views/default/list-country', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new CountryForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Страна создана');
            return $this->redirect(['edit', 'id' => $model->id]);
        }

        return $this->render('@backend/views/default/create', [
            'model' => $model,
            'title' => 'Создать страну',
        ]);
    }

    public function actionEdit($id)
    {
        $country = Country::findOne($id);
        if (!$country) throw new NotFoundHttpException('Страна не найдена');

        $model = new CountryForm();
        $model->loadFromModel($country);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Изменения сохранены');
            return $this->redirect(['edit', 'id' => $model->id]);
        }

        return $this->render('@backend/views/default/edit', [
            'model' => $model,
            'title' => 'Редактировать страну',
        ]);
    }
}