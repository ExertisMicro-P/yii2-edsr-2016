<?php

use yii\db\Schema;
use yii\db\Migration;

class m160316_145254_add_lastUpdated_to_ztorm_catalogue_cache extends Migration
{
    public function up()
    {
        $this->addColumn('ztorm_catalogue_cache', 'lastUpdated', $this->timestamp());
    }

    public function down()
    {
        $this->dropColumn('ztorm_catalogue_cache', 'lastUpdated');
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
