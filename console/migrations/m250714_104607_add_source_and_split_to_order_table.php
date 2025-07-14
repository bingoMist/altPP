<?php

use yii\db\Migration;

class m250714_104607_add_source_and_split_to_order_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('order', 'source', $this->string(25)->null()->after('web_id'));
        $this->addColumn('order', 'split', $this->integer(10)->unsigned()->null()->after('source'));
    }

    public function safeDown()
    {
        $this->dropColumn('order', 'split');
        $this->dropColumn('order', 'source');
    }
}