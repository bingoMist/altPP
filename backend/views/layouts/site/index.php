<?php
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Order;

$this->title = 'Панель управления';
?>

<div class="site-index">
    <h1>Добро пожаловать в админку m4leads.org</h1>

    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?= number_format($ordersToday, 0, '', ' ') ?></h3>
                    <p>Заказов за сегодня</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?= number_format($totalOrders, 0, '', ' ') ?></h3>
                    <p>Всего заказов</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?= number_format($totalPartners, 0, '', ' ') ?></h3>
                    <p>Партнёров</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?= number_format($totalRevenue, 2, '.', ' ') ?> ₽</h3>
                    <p>Общая выручка</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
            </div>
        </div>
    </div>
</div>