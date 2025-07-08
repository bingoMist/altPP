<?php

use yii\db\Migration;

class m250708_141425_alter_status_column_in_postback_table extends Migration
{
    public function safeUp()
    {
        // Меняем тип с BOOLEAN на VARCHAR(255), чтобы хранить текстовые статусы
        $this->alterColumn('postback', 'status', $this->string(255)->null());
    }

    public function safeDown()
    {
        // Возвращаем обратно (если нужно)
        $this->alterColumn('postback', 'status', $this->boolean()->defaultValue(true));
    }
}
