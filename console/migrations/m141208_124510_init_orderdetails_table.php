<?php

use yii\db\Schema;
use yii\db\Migration;

class m141208_124510_init_orderdetails_table extends Migration
{
    public function up()
    {
     /*
        CREATE TABLE 'orderdetails' (
        'id' INT(10) NULL DEFAULT NULL,
        'stock_item_id' INT(10) NULL DEFAULT NULL,
        'name' VARCHAR(50) NULL DEFAULT NULL,
        'contact' VARCHAR(50) NULL DEFAULT NULL,
        'street' VARCHAR(200) NULL DEFAULT NULL,
        'town' VARCHAR(200) NULL DEFAULT NULL,
        'city' VARCHAR(200) NULL DEFAULT NULL,
        'postcode' VARCHAR(200) NULL DEFAULT NULL,
        'country' VARCHAR(200) NULL DEFAULT 'GB',
        'sop' VARCHAR(50) NULL DEFAULT NULL
        )
        COLLATE='latin1_swedish_ci'
        ENGINE=InnoDB;
        */

        $this->createTable('orderdetails' , [
            'id' => Schema::TYPE_INTEGER,
            'stock_item_id' => Schema::TYPE_INTEGER,
            'name' => Schema::TYPE_STRING.'(50)',
            'contact' => Schema::TYPE_STRING.'(50)',
            'street' => Schema::TYPE_STRING.'(200)',
            'town' => Schema::TYPE_STRING.'(200)',
            'city' => Schema::TYPE_STRING.'(200)',
            'postcode' => Schema::TYPE_STRING.'(200)',
            'country' => Schema::TYPE_STRING.'(200) DEFAULT "GB"',
            'sop' => Schema::TYPE_STRING.'(50)',
        ]);

    }

    public function down()
    {
        $this->dropTable('orderdetails');


    }
}
