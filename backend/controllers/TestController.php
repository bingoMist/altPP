<?php

namespace backend\controllers;

use yii\web\Controller;

class TestController extends Controller
{
    public function actionIndex()
    {
        return "Hello from TestController!";
    }

    public function actionView($id)
    {
        return "You requested item: $id";
    }
}
