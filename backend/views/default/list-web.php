<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Вебы';
?>
<h1><?= Html::encode($this->title) ?></h1>

<p>
    <?= Html::a('Добавить веба', ['web/create'], ['class' => 'btn btn-success']) ?>
</p>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'access_token',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('Редактировать', ['web/edit', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']);
                },
            ],
        ],
    ],
]); ?>