<?php

use yii\db\Migration;

class m241110_000002_create_offer_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('offer', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('offer');
    }
}
