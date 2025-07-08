<?php

use yii\db\Migration;

class m250708_141121_add_id_to_postback_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('postback', 'id', $this->primaryKey()->unsigned());
    }

    public function safeDown()
    {
        $this->dropColumn('postback', 'id');
    }
}