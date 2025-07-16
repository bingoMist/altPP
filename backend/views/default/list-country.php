<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Страны';
?>
<h1><?= Html::encode($this->title) ?></h1>

<p>
    <?= Html::a('Добавить страну', ['country/create'], ['class' => 'btn btn-success']) ?>
</p>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'name',
        'country_iso',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('Редактировать', ['country/edit', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']);
                },
            ],
        ],
    ],
]); ?>