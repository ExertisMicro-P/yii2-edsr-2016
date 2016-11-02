<?php

use yii\db\Schema;
use yii\db\Migration;

class m161102_114313_make_mapping_table extends Migration {
    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
//            $tableOptions = 'CHARACTER SET utf8 COLLATE cp1252 West European ENGINE=InnoDB';
            $tableOptions = 'CHARACTER SET latin1 COLLATE latin1_swedish_ci ENGINE=INNODB';
        }

        $this->createTable('{{%customer_product_mapping}}', [
            'id'                      => Schema::TYPE_PK,
            'customer_account_number' => Schema::TYPE_STRING . '(20) NOT NULL',
            'customer_partcode'       => Schema::TYPE_STRING . ' NOT NULL',
            'oracle_partcode'         => Schema::TYPE_STRING . ' NOT NULL',

            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

    }

    public function down() {
        $this->dropTable('{{%customer_product_mapping}}');
    }

}
