<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Страны';
?>

<div class="country-index">
    <p>
        <?= Html::a('Добавить страну', ['create'], ['class' => 'btn btn-success']) ?>
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