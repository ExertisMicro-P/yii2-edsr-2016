<?php

use yii\db\Schema;
use yii\db\Migration;

class m160909_093418_dropship_email_details extends Migration {
    public function up() {
        $this->createTable('{{%dropship_email_details}}', [
            'id'              => Schema::TYPE_PK,
            'account_id'      => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "The account id" ',
            'orderdetails_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "The order id" ',
            'account_no'      => Schema::TYPE_STRING . ' NOT NULL COMMENT "The account number number" ',
            'po'              => Schema::TYPE_STRING . ' NOT NULL COMMENT "The purchase order number" ',
            'email'           => Schema::TYPE_STRING . ' NULL COMMENT "The drop shop email"',
            'timestamp'       => Schema::TYPE_TEXT . ' NULL COMMENT "Time the email was sent"',

            'created_at' => Schema::TYPE_DATETIME,
            'updated_at' => Schema::TYPE_TIMESTAMP,
            'created_by' => Schema::TYPE_INTEGER,
            'updated_by' => Schema::TYPE_INTEGER
        ]);
        $this->addForeignKey('fk_sde_account', '{{%dropship_email_details}}', 'account_id', '{{%account}}', 'id');
        $this->addForeignKey('fk_sde_order', '{{%dropship_email_details}}', 'orderdetails_id', '{{%orderdetails}}', 'id');

    }

    public function down() {
        $this->dropTable('{{%dropship_email_details}}');
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
