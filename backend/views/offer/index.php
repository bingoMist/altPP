<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Офферы';
?>

<div class="offer-index">
    <p>
        <?= Html::a('Добавить оффер', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
            ],
        ],
    ]); ?>
</div>