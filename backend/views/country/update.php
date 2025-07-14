<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Редактировать страну #' . $model->id;

?>
<div class="country-update">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>