<?php

use yii\db\Migration;

class m241110_000003_create_order_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('order', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'phone' => $this->string()->notNull(),
            'offer_id' => $this->integer()->notNull(),
            'offer_name' => $this->string()->notNull(),
            'status' => $this->integer()->defaultValue(0),
            'country_name' => $this->string()->notNull(),
            'partner_id' => $this->integer()->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'date' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'comment' => $this->text(),
            'sub_id' => $this->string(),
            'web_id' => $this->string(),
        ]);

        // Внешние ключи (опционально)
        $this->addForeignKey(
            'fk-order-offer_id',
            'order',
            'offer_id',
            'offer',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-order-partner_id',
            'order',
            'partner_id',
            'partner',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-order-status',
            'order',
            'status',
            'status',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-order-offer_id', 'order');
        $this->dropForeignKey('fk-order-partner_id', 'order');
        $this->dropForeignKey('fk-order-status', 'order');

        $this->dropTable('order');
    }
}