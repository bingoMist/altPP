<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Добавить оффер';

/** @var yii\web\View $this */
/** @var common\models\Country $model */

?>
<div class="webs-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>