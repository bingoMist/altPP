<?php

use yii\db\Migration;

class m241110_000001_create_country_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('country', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('country');
    }
}
