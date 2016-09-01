<?php

use yii\db\Schema;
use yii\db\Migration;

class m150811_110545_drop_credit_balance_table extends Migration
{
    public function up()
    {
        $this->dropTable('credit_balance') ;
    }

    /**
     * DOWN
     * ====
     * This is a duplicate of the original migration used to create the table,
     * minus the tableOptions, as that prevents the foreign key creation
     */
    public function down()
    {
        $tableOptions = null;
//        if ($this->db->driverName === 'mysql') {
//            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
//            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
//        }

        $this->createTable('{{%credit_balance}}', [
            'id'           => Schema::TYPE_PK,
            'account'      => Schema::TYPE_STRING . '(20) NOT NULL',
            'balance'      => Schema::TYPE_DOUBLE . ' NOT NULL DEFAULT 0',
            'credit_limit' => Schema::TYPE_DOUBLE . ' NOT NULL DEFAULT 0',

            'created_at'   => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at'   => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        $this->addForeignKey('pimage_fk', '{{%credit_balance}}', 'account', '{{%account}}', 'customer_exertis_account_number') ;

    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
