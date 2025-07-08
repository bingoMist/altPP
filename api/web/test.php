<?php

echo "Composer autoload: ";
require(__DIR__ . '/../../vendor/autoload.php');
echo "OK<br>";

echo "Yii class exists: ";
if (class_exists(\yii\BaseYii::class)) {
    echo "YES";
} else {
    echo "NO";
}
