<?php

use yii\db\Migration;

class m250721_220101_add_country_id_to_order_table extends Migration
{
    public function up()
    {
        // 1. Добавляем колонку country_id, NULL на время миграции
        $this->addColumn('{{%order}}', 'country_id', $this->integer()->null()->after('country_name'));

        // 2. Добавляем индекс для производительности
        $this->createIndex(
            'idx-order-country_id',
            '{{%order}}',
            'country_id'
        );

        // 3. Обновляем все записи: country_name → country_id
        $this->execute("
            UPDATE {{%order}} o
            JOIN {{%country}} c ON o.country_name = c.name
            SET o.country_id = c.id
        ");

        // 4. Делаем колонку NOT NULL (если нужно)
        $this->alterColumn('{{%order}}', 'country_id', $this->integer()->notNull());

        echo "✅ Колонка `country_id` успешно добавлена и заполнена.\n";
    }

    public function down()
    {
        // 1. Удаляем индекс
        $this->dropIndex('idx-order-country_id', '{{%order}}');

        // 2. Удаляем колонку
        $this->dropColumn('{{%order}}', 'country_id');

        echo "❌ Колонка `country_id` удалена.\n";
    }
}