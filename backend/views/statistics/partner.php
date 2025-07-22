<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use Yii;

$this->title = 'Статистика по партнерам';

// Получаем данные для фильтров
$partners = \common\models\Partner::find()
    ->select(['id'])
    ->asArray()
    ->column();

$offers = \yii\helpers\ArrayHelper::map(
    \common\models\Offer::find()
        ->select(['id', 'name'])
        ->asArray()
        ->all(),
    'id', 'name'
);

$countries = \yii\helpers\ArrayHelper::map(
    \common\models\Country::find()
        ->select(['id', 'name'])
        ->asArray()
        ->all(),
    'id', 'name'
);

$sources = \common\models\Order::find()
    ->select(['source'])
    ->distinct()
    ->andWhere(['IS NOT', 'source', null])
    ->andWhere(['<>', 'source', ''])
    ->orderBy('source')
    ->asArray()
    ->column();
$sources = array_filter(array_map('trim', $sources));

$webIds = \common\models\Order::find()
    ->select(['web_id'])
    ->distinct()
    ->andWhere(['IS NOT', 'web_id', null])
    ->andWhere(['<>', 'web_id', ''])
    ->orderBy('web_id')
    ->asArray()
    ->column();
$webIds = array_filter(array_map('trim', $webIds));
?>

<div class="card">
    <div class="card-header">
        <!-- <h3 class="card-title">Статистика по партнерам</h3> -->
    </div>

    <!-- Фильтры -->
    <?php $form = \yii\widgets\ActiveForm::begin([
        'method' => 'get',
        'action' => ['statistics/partner'],
        'options' => ['class' => 'card-body'],
    ]); ?>

    <div class="row">
        <div class="col-md-2">
        <?= \yii\helpers\Html::input('date', 'dateFrom', $model->dateFrom, ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-2">
        <?= \yii\helpers\Html::input('date', 'dateTo', $model->dateTo, ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-1">
            <?= \yii\helpers\Html::dropDownList('country', Yii::$app->request->get('country'), $countries, ['prompt' => 'Страна', 'class' => 'form-control']) ?>
        </div>
        <div class="col-md-1">
            <?= \yii\helpers\Html::dropDownList('offerId', Yii::$app->request->get('offerId'), $offers, ['prompt' => 'Оффер', 'class' => 'form-control']) ?>
        </div>
        <div class="col-md-1">
            <?= \yii\helpers\Html::dropDownList('partnerId', Yii::$app->request->get('partnerId'), array_combine($partners, $partners), ['prompt' => 'Партнер', 'class' => 'form-control']) ?>
        </div>
        <div class="col-md-1">
        <?= \yii\helpers\Html::dropDownList('source', Yii::$app->request->get('source'), array_combine($sources, $sources), ['prompt' => 'Источники', 'class' => 'form-control']) ?>
        </div>
        <div class="col-md-1">
            <?= \yii\helpers\Html::textInput('web_id', Yii::$app->request->get('web_id'), ['class' => 'form-control', 'placeholder' => 'Web ID']) ?>
        </div>
        <div class="col-md-1">
            <?= \yii\helpers\Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php \yii\widgets\ActiveForm::end(); ?>

    <!-- Основная таблица -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-bordered'],
        'columns' => [
            [
                'header' => '',
                'value' => function ($model) {
                    $id = $model['partner_id'];
                    return Html::a('▶', '#', [
                        'onclick' => "toggleSources('$id'); return false;",
                        'style' => 'cursor: pointer; text-decoration: none;',
                        'id' => "toggle-partner-$id"
                    ]);
                },
                'format' => 'raw',
            ],
            [
                'label' => 'ID',
                'value' => 'partner_id',
            ],
            [
                'label' => 'Всего',
                'value' => 'total',
            ],
            [
                'label' => 'Подтверждено',
                'value' => 'approved',
            ],
            [
                'label' => 'Отклонён',
                'value' => 'rejected',
            ],
            [
                'label' => 'В ожидании',
                'value' => 'pending',
            ],
            [
                'label' => 'Некорректный',
                'value' => 'invalid',
            ],
            [
                'label' => 'Дубль',
                'value' => 'duplicate',
            ],
            [
                'label' => '% Подтвержденных',
                'value' => function ($model) {
                    return $model['total'] > 0 ? round($model['approved'] / $model['total'] * 100, 2) . '%' : '0%';
                },
            ],
            [
                'label' => '% Некорректных',
                'value' => function ($model) {
                    return $model['total'] > 0 ? round($model['invalid'] / $model['total'] * 100, 2) . '%' : '0%';
                },
            ],
        ],
    ]) ?>
</div>

<!-- JavaScript для раскрытия -->
<script>
function toggleSources(partnerId) {
    const row = document.getElementById('detail-sources-' + partnerId);
    if (row) {
        row.remove();
        document.getElementById('toggle-partner-' + partnerId).textContent = '▶';
        return;
    }

    const parentRow = document.querySelector(`[onclick*="toggleSources('${partnerId}')"]`).closest('tr');
    const newRow = document.createElement('tr');
    newRow.id = 'detail-sources-' + partnerId;

    // Генерация данных
    const data = <?= json_encode($dataProvider->getModels()) ?>;
    const model = data.find(p => p.partner_id == partnerId);

    let html = '<td colspan="10" style="padding: 0; border: none;">';
    html += '<div style="background: #f9f9f9; border-left: 1px solid #dee2e6; padding: 8px 0;">';

    // Вложенная таблица без рамок, но с нужной структурой
    html += '<table style="width: 100%; border-collapse: collapse; margin: 0;">';
    html += '<thead>';
    html += '<tr>';

    // Первая ячейка: "Источник" — объединяет стрелку и ID
    html += '<th style="width: 130px; padding: 8px; font-size: 0.85rem; text-align: left;">Источник</th>';

    // Остальные заголовки — как в основной таблице, начиная с "Всего"
    html += '<th style="width: 80px;">Всего</th>';
    html += '<th style="width: 80px;">Подтв.</th>';
    html += '<th style="width: 80px;">Отклонён</th>';
    html += '<th style="width: 80px;">Ожидание</th>';
    html += '<th style="width: 80px;">Некорр.</th>';
    html += '<th style="width: 80px;">Дубль</th>';
    html += '<th style="width: 100px;">% Подтв.</th>';
    html += '<th style="width: 100px;">% Некорр.</th>';
    html += '</tr></thead><tbody>';

    for (const [sourceKey, source] of Object.entries(model._sources || {})) {
        const sourceId = encodeURIComponent(source.source);
        html += '<tr>';
        html += `<td style="width: 130px; padding: 8px;">
                    <a href="#" onclick="toggleOffers('${partnerId}', '${sourceId}'); return false;" style="text-decoration: none; color: inherit;">
                        ▶
                    </a>
                    <span style="margin-left: 8px;">${source.source}</span>
                 </td>`;
        html += `<td>${source.total}</td>`;
        html += `<td>${source.approved}</td>`;
        html += `<td>${source.rejected}</td>`;
        html += `<td>${source.pending}</td>`;
        html += `<td>${source.invalid}</td>`;
        html += `<td>${source.duplicate}</td>`;
        html += `<td>${(source.total > 0 ? (source.approved / source.total * 100).toFixed(2) : 0)}%</td>`;
        html += `<td>${(source.total > 0 ? (source.invalid / source.total * 100).toFixed(2) : 0)}%</td>`;
        html += '</tr>';

        // Таблица офферов (в стиле скрытой строки)
        html += `
        <tr id="detail-offers-${partnerId}-${sourceId}" style="display: none;">
            <td colspan="9" style="padding: 0;">
                <div style="margin-left: 20px; background: #fff; border: 1px solid #eee; border-radius: 4px; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f5f5f5;">
                                <th style="padding: 6px; font-size: 0.8rem; text-align: left;">Оффер</th>
                                <th style="padding: 6px; font-size: 0.8rem;">Всего</th>
                                <th style="padding: 6px; font-size: 0.8rem;">Подтв.</th>
                                <th style="padding: 6px; font-size: 0.8rem;">Откл.</th>
                                <th style="padding: 6px; font-size: 0.8rem;">Ожид.</th>
                                <th style="padding: 6px; font-size: 0.8rem;">Некорр.</th>
                                <th style="padding: 6px; font-size: 0.8rem;">Дубль</th>
                                <th style="padding: 6px; font-size: 0.8rem;">% Подтв.</th>
                                <th style="padding: 6px; font-size: 0.8rem;">% Некорр.</th>
                            </tr>
                        </thead>
                        <tbody>`;

        for (const [name, offer] of Object.entries(source._offers || {})) {
            html += `<tr>
                        <td style="padding: 6px; font-size: 0.85rem;">${name}</td>
                        <td style="padding: 6px; text-align: center;">${offer.total}</td>
                        <td style="padding: 6px; text-align: center;">${offer.approved}</td>
                        <td style="padding: 6px; text-align: center;">${offer.rejected}</td>
                        <td style="padding: 6px; text-align: center;">${offer.pending}</td>
                        <td style="padding: 6px; text-align: center;">${offer.invalid}</td>
                        <td style="padding: 6px; text-align: center;">${offer.duplicate}</td>
                        <td style="padding: 6px; text-align: center;">${(offer.total > 0 ? (offer.approved / offer.total * 100).toFixed(2) : 0)}%</td>
                        <td style="padding: 6px; text-align: center;">${(offer.total > 0 ? (offer.invalid / offer.total * 100).toFixed(2) : 0)}%</td>
                     </tr>`;
        }

        html += `      </tbody>
                    </table>
                </div>
            </td>
        </tr>`;
    }

    html += '</tbody></table>';
    html += '</div></td>';

    newRow.innerHTML = html;
    parentRow.after(newRow);
    document.getElementById('toggle-partner-' + partnerId).textContent = '▼';
}

function toggleOffers(partnerId, sourceId) {
    const row = document.getElementById('detail-offers-' + partnerId + '-' + sourceId);
    const icon = row.previousElementSibling.querySelector('a');
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
    icon.textContent = row.style.display === 'none' ? '▶' : '▼';
}
</script>

<!-- CSS для выравнивания колонок -->
<style>
.table th, .table td {
    white-space: nowrap;
    padding: 8px !important;
}
.detail-table {
    width: 100%;
    margin: 0;
    border-collapse: collapse;
}
</style>