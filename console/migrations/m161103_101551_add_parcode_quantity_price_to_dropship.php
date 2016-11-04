<?php

use yii\db\Schema;
use yii\db\Migration;

class m161103_101551_add_parcode_quantity_price_to_dropship extends Migration {
    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET latin1 COLLATE latin1_swedish_ci ENGINE=INNODB';
        }

        $this->createTable('{{%dropship_orderline}}', [
            'id'                => Schema::TYPE_PK,
            'dropship_id'       => Schema::TYPE_INTEGER . ' NOT NULL COMMENT"This is the related dropship_email_details_record"',
            'customer_partcode' => Schema::TYPE_STRING . ' NOT NULL COMMENT"The customer\'s own partcode"',
            'oracle_partcode'   => Schema::TYPE_STRING . ' NOT NULL COMMENT"The oracle version of the customer partcode"',
            'quantity'          => Schema::TYPE_STRING . ' NOT NULL COMMENT"The number ordered"',
            'price'             => Schema::TYPE_DOUBLE . ' NOT NULL COMMENT"The order price, which can be zero"',

            'created_at' => Schema::TYPE_DATETIME . ' NULL',
            'created_by' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_TIMESTAMP,
            'updated_by' => Schema::TYPE_INTEGER,
            'deleted_at' => Schema::TYPE_DATETIME,
            'deleted_by' => Schema::TYPE_INTEGER
        ], $tableOptions);

        $this->addForeignKey('ds_email_fk',
                             '{{%dropship_orderline}}', 'dropship_id',
                             '{{%dropship_email_details}}', 'id');
    }

    public function down() {
        $this->dropTable('{{%dropship_orderline}}');
    }
}
