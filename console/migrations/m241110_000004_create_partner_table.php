<?php

use yii\db\Migration;

class m241110_000004_create_partner_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('partner', [
            'id' => $this->primaryKey(),
            'access_token' => $this->string()->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('partner');
    }
}
