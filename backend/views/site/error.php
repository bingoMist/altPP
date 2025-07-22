<?php
/* @var $this yii\web\View */
/* @var $exception \Exception */
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Ошибка ' . $exception->getCode();
?>
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; font-family: Arial, sans-serif; text-align: center;">
    <h1><?= $exception->getCode() ?></h1>
    <h2><?= Html::encode($exception->getMessage()) ?></h2>
    <p style="color: #555;">Произошла ошибка при обработке запроса</p>
    <p style="color: #888; font-size: 0.9em;">Файл: <?= Html::encode($exception->getFile()) ?></p>
    <p style="color: #888; font-size: 0.9em;">Строка: <?= $exception->getLine() ?></p>

    <pre style="color: #333; font-size: 0.85em; background: #f9f9f9; padding: 10px 20px; margin-top: 20px; max-width: 800px; white-space: pre-wrap;">
        <?= nl2br(Html::encode($exception->getTraceAsString())) ?>
    </pre>

    <a href="<?= \yii\helpers\Url::home() ?>" style="margin-top: 20px; color: #007bff; text-decoration: none;">← На главную</a>
</div>