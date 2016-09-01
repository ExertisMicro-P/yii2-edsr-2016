<?php

use yii\db\Schema;
use yii\db\Migration;

class m141204_190441_init_account_table extends Migration
{
    public function up()
    {
    	$this->createTable('account', [
            'id' => Schema::TYPE_PK,
            'eztorm_user_id' => Schema::TYPE_STRING."(45)",
            'customer_exertis_account_number' => Schema::TYPE_STRING."(20)",
            'timestamp' => Schema::TYPE_TIMESTAMP,
        ]);

        $this->createIndex('fk_account_customer1_idx_key', 'account', "customer_exertis_account_number", true);
        $this->addForeignKey('fk_account_customer1', 'account', "customer_exertis_account_number",
        							'customer', "exertis_account_number");

    }

    public function down()
    {
 		$this->dropTable('account');


    }
}
