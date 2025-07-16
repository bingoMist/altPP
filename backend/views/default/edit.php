<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'id')->textInput(['readonly' => true]) ?>

<?php if (property_exists($model, 'name')): ?>
    <?= $form->field($model, 'name')->textInput() ?>
<?php endif; ?>

<?php if (property_exists($model, 'country_iso')): ?>
    <?= $form->field($model, 'country_iso')->textInput(['placeholder' => 'Пример: RU']) ?>
<?php endif; ?>

<?php if (property_exists($model, 'crm_id')): ?>
    <?= $form->field($model, 'crm_id')->textInput() ?>
<?php endif; ?>

<?php if (property_exists($model, 'access_token')): ?>
    <?= $form->field($model, 'access_token')->textInput() ?>
<?php endif; ?>

<div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>