<?php

use yii\db\Schema;
use yii\db\Migration;

class m160318_131846_add_product_added_column_to_ztorm_catalogue_cache extends Migration
{
    public function up()
    {
        $this->addColumn('ztorm_catalogue_cache', 'product_added', $this->date());
    }

    public function down()
    {
        $this->dropColumn('ztorm_catalogue_cache', 'product_added');
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
