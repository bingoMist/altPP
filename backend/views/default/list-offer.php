<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Офферы';
?>
<h1><?= Html::encode($this->title) ?></h1>

<p>
    <?= Html::a('Добавить оффер', ['offer/create'], ['class' => 'btn btn-success']) ?>
</p>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'crm_id',
        'name',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('Редактировать', ['offer/edit', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']);
                },
            ],
        ],
    ],
]); ?>