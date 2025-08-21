<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Заказы';
?>

<div class="order-index">

    <!-- Форма фильтров -->
    <?php $form = \yii\widgets\ActiveForm::begin([
        'method' => 'get',
        'action' => ['/orders'],
    ]); ?>
    <div class="row">
        <div class="col-md-1">
            <?= \yii\helpers\Html::input('text', 'id', $searchModel['id'], ['class' => 'form-control', 'placeholder' => 'ID']) ?>
        </div>

        <div class="col-md-1">
            <?= \yii\helpers\Html::input('text', 'crm_order_id', $searchModel['crm_order_id'], ['class' => 'form-control', 'placeholder' => 'CRM ID']) ?>
        </div>

        <div class="col-md-1">
            <?= \yii\helpers\Html::dropDownList('partner_id', $searchModel['partner_id'], $partners, ['prompt' => 'Партнёр', 'class' => 'form-control']) ?>
        </div>

        <div class="col-md-1">
            <?= \yii\helpers\Html::dropDownList('offer_id', $searchModel['offer_id'], $offersList, ['prompt' => 'Оффер', 'class' => 'form-control']) ?>
        </div>

        <div class="col-md-1">
            <?= \yii\helpers\Html::dropDownList('country_name', $searchModel['country_name'], $countriesList, ['prompt' => 'Страна', 'class' => 'form-control']) ?>
        </div>

        <div class="col-md-1">
            <?= \yii\helpers\Html::dropDownList('status', $searchModel['status'], $statusesList, ['prompt' => 'Статус', 'class' => 'form-control']) ?>
        </div>

        <div class="col-md-2">
            <?= \yii\helpers\Html::input('date', 'date_from', $searchModel['date_from'], ['class' => 'form-control', 'placeholder' => 'С']) ?>
        </div>
        <div class="col-md-2">
            <?= \yii\helpers\Html::input('date', 'date_to', $searchModel['date_to'], ['class' => 'form-control', 'placeholder' => 'По']) ?>
        </div>
        <div class="col-md-1">
        <?= \yii\helpers\Html::input('text', 'sub_id', $searchModel['sub_id'], ['class' => 'form-control', 'placeholder' => 'sub_id']) ?>
        </div>

        <div class="col-md-1">
            <?= \yii\helpers\Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php \yii\widgets\ActiveForm::end(); ?>

    <br />

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => 'Показано {count} из {totalCount}',
        'columns' => [
            [
                'attribute' => 'id',
                'value' => function ($model) {
                    return $model->id . '<br><small style="color:#888;">' . $model->crm_order_id . '</small>';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'Клиент',
                'format' => 'raw',
                'value' => function ($model) {
                    $name = Html::encode($model->name);
                    $phone = Html::encode($model->phone);
                    return "$name<br><small style='color:#555;'>$phone</small>";
                },
            ],
            'country_name',
            'partner_id',
            'offer_name',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    // Получаем имя статуса из связанной модели
                    return $model->statusModel ? $model->statusModel->name : 'Неизвестный статус';
                },
            ],
            [
                'attribute' => 'date',
                'value' => function ($model) {
                    $dt = new \DateTime($model->date, new \DateTimeZone('UTC'));
                    $dt->setTimezone(new \DateTimeZone('Europe/Moscow')); // UTC +3
                    return $dt->format('d.m.Y H:i:s');
                }
            ],
            'price',
            'source',
            'sub_id',
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>