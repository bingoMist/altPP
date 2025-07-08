<?php

use yii\db\Migration;

class m250708_124842_add_columns_to_offer_country_and_order_tables extends Migration
{
    public function safeUp()
    {
        // Добавляем crm_id в таблицу offer
        $this->addColumn('offer', 'crm_id', $this->integer()->null()->after('id'));
        
        // Добавляем country_iso в таблицу country
        $this->addColumn('country', 'country_iso', $this->string(2)->null()->after('name'));
        
        // Добавляем crm_order_id в таблицу order
        $this->addColumn('order', 'crm_order_id', $this->string()->null()->after('id'));
    }

    public function safeDown()
    {
        // Удаляем обратно
        $this->dropColumn('order', 'crm_order_id');
        $this->dropColumn('country', 'country_iso');
        $this->dropColumn('offer', 'crm_id');
    }
}