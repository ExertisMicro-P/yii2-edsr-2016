<?php

use yii\db\Schema;
use yii\db\Migration;

class m150526_161406_make_session_order_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%session_order}}', [
            'id'          => Schema::TYPE_PK,
            'account_id'  => Schema::TYPE_INTEGER . ' NOT NULL',
            'session_id'  => Schema::TYPE_STRING . '(128) NOT NULL COMMENT "Record by session and delete afer some time of no-use"',

            'product_id'  => Schema::TYPE_INTEGER . ' NOT NULL',
            'photo'       => Schema::TYPE_STRING . '(255) NOT NULL',
            'partcode'    => Schema::TYPE_STRING . '(20) NOT NULL',
            'description' => Schema::TYPE_STRING . '(500) NOT NULL',
            'quantity'    => Schema::TYPE_INTEGER . ' NOT NULL',
            'cost'        => Schema::TYPE_DECIMAL . '(10,2) NOT NULL',

            'created_by'  => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at'  => Schema::TYPE_DATETIME . ' NOT NULL',

            'updated_by'  => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at'  => Schema::TYPE_TIMESTAMP . ' NOT NULL',
        ]);

        $this->addForeignKey('acc_fk', '{{%session_order}}', 'account_id', '{{%account}}', 'id');
        $this->addForeignKey('prod_fk', '{{%session_order}}', 'product_id', '{{%digital_product}}', 'id');

    }

    public function down()
    {
        $this->dropTable('{{%session_order}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }_id
    */
}
