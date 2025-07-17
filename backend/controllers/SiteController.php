<?php

namespace backend\controllers;

use common\models\LoginForm;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use common\models\Order;
use common\models\Partner;
use yii\db\Expression;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \backend\actions\CustomErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */

     public function actionIndex()
     {
        // Получаем текущее время в UTC+3
        $tzLocal = new \DateTimeZone('Europe/Moscow');
        $now = new \DateTime('now', $tzLocal);

        // Сегодняшняя дата в UTC+3
        $todayStartUTC3 = clone $now;
        $todayStartUTC3->setTime(0, 0, 0);

        $todayEndUTC3 = clone $todayStartUTC3;
        $todayEndUTC3->setTime(23, 59, 59);

        // Конвертируем начало и конец дня в UTC для SQL-запроса
        $startUTC = $todayStartUTC3->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
        $endUTC = $todayEndUTC3->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
        
         // Одобрено (status=1)
         $approvedCount = Order::find()
             ->where(['>=', 'date', $startUTC])
             ->andWhere(['<', 'date', $endUTC])
             ->andWhere(['status' => 1])
             ->count();
     
         // Некорректные (status=4)
         $invalidCount = Order::find()
             ->where(['>=', 'date', $startUTC])
             ->andWhere(['<', 'date', $endUTC])
             ->andWhere(['status' => 4])
             ->count();
     
         // Отменённые (status=2)
         $cancelledCount = Order::find()
             ->where(['>=', 'date', $startUTC])
             ->andWhere(['<', 'date', $endUTC])
             ->andWhere(['status' => 2])
             ->count();
     
         // Дубли (status=8)
         $duplicatesCount = Order::find()
             ->where(['>=', 'date', $startUTC])
             ->andWhere(['<', 'date', $endUTC])
             ->andWhere(['status' => 8])
             ->count();
     
         // В ожидании (status=6)
         $pendingCount = Order::find()
             ->where(['>=', 'date', $startUTC])
             ->andWhere(['<', 'date', $endUTC])
             ->andWhere(['status' => 6])
             ->count();
     
         // Все заказы за сегодня
         $ordersCount = Order::find()
             ->where(['>=', 'date', $startUTC])
             ->andWhere(['<', 'date', $endUTC])
             ->count();

        // --- График активности по часам ---
        $activityData = [];

        // Сегодняшняя дата в UTC+3
        $todayUTC3 = $now->format('Y-m-d');

            for ($hour = 0; $hour < 24; $hour++) {
                // Формируем метки для графика (в UTC+3)
                $fromHour = sprintf('%02d:00:00', $hour);
                $toHour = sprintf('%02d:59:59', $hour);

                // Формируем дату в формате Y-m-d H:i:s и конвертируем в UTC
                $fromUTC3 = new \DateTime("$todayUTC3 $fromHour", $tzLocal);
                $toUTC3 = new \DateTime("$todayUTC3 $toHour", $tzLocal);
                $fromUTC = $fromUTC3->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
                $toUTC = $toUTC3->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');

                // Запрос к БД
                $count = Order::find()
                    ->andWhere(['>=', 'date', $fromUTC])
                    ->andWhere(['<', 'date', $toUTC])
                    ->count();

                $activityData[] = [
                    'label' => sprintf('%02d:00 — %02d:59', $hour, $hour),
                    'count' => $count,
                ];
            }

        return $this->render('index', [
            'ordersCount' => $ordersCount,
            'approvedCount' => $approvedCount,
            'invalidCount' => $invalidCount,
            'cancelledCount' => $cancelledCount,
            'duplicatesCount' => $duplicatesCount,
            'pendingCount' => $pendingCount,
            'activityData' => json_encode(array_column($activityData, 'count')),
            'activityLabels' => json_encode(array_column($activityData, 'label')),
        ]);
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionError()
    {
        $this->layout = 'plain';
        return $this->render('error');
    }
}
