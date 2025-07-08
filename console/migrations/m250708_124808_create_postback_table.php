<?php

use yii\db\Migration;

class m250708_124808_create_postback_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('postback', [
            'url' => $this->text()->notNull(),
            'send' => $this->boolean()->defaultValue(false),
            'status' => $this->boolean()->defaultValue(true),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('postback');
    }
}