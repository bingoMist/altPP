<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\models\PartnerForm;
use common\models\Partner;
use yii\data\ArrayDataProvider;

class WebController extends Controller
{
    public function actionIndex()
    {
        $partners = Partner::find()->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $partners,
            'pagination' => false,
        ]);

        return $this->render('@backend/views/default/list-web', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new PartnerForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            Yii::$app->session->setFlash('success', 'Веб создан');
            return $this->redirect(['edit', 'id' => $model->id]);
        }

        return $this->render('@backend/views/default/create', [
            'model' => $model,
            'title' => 'Создать веба',
        ]);
        }

    public function actionEdit($id)
    {
        $partner = Partner::findOne($id);
        if (!$partner) throw new NotFoundHttpException('Веб не найден');

        $model = new PartnerForm();
        $model->loadFromModel($partner);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Веб обновлён');
            return $this->redirect(['edit', 'id' => $id]);
        }

        return $this->render('@backend/views/default/edit', [
            'model' => $model,
            'title' => 'Редактировать веба',
        ]);
    }
}