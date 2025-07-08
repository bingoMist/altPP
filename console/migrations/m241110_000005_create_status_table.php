<?php

use yii\db\Migration;

class m241110_000005_create_status_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('status', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('status');
    }
}
