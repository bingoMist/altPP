<?php

use yii\db\Migration;

class m250708_141726_reorder_columns_in_postback_table extends Migration
{
    public function safeUp()
    {
        // Создаём временную таблицу с нужным порядком
        $this->createTable('postback_temp', [
            'id' => $this->primaryKey()->unsigned(),
            'url' => $this->text()->notNull(),
            'send' => $this->boolean()->defaultValue(false),
            'status' => $this->string(),
        ]);
    
        // Копируем данные
        $this->execute("INSERT INTO postback_temp (id, url, send, status) SELECT id, url, send, status FROM postback");
    
        // Удаляем старую таблицу
        $this->dropTable('postback');
    
        // Переименовываем новую
        $this->renameTable('postback_temp', 'postback');
    }
    
    public function safeDown()
    {
        // Аналогично, если нужно откатиться
    }
}