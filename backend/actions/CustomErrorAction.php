<?php

namespace backend\actions;

use yii\web\ErrorAction as BaseErrorAction;
use yii\web\NotFoundHttpException;
use Yii;

class CustomErrorAction extends BaseErrorAction
{
    public function run()
    {
        $exception = \Yii::$app->getErrorHandler()->exception;

        if ($exception instanceof NotFoundHttpException) {
            // Редирект на авторизацию при 404
            $this->controller->layout = 'plain';
            return $this->controller->render('404', ['exception' => $exception]);
        }

        //layout:
        $this->controller->layout = 'plain';

        // далее
        return parent::run();
    }
}