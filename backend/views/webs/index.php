<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Вебы';
?>

<div class="webs-index">
    <p>
        <?= Html::a('Добавить веба', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'access_token',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
            ],
        ],
    ]); ?>
</div>