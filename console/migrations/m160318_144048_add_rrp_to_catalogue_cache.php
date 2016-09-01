<?php

use yii\db\Schema;
use yii\db\Migration;

class m160318_144048_add_rrp_to_catalogue_cache extends Migration
{
    public function up()
    {
        $this->addColumn('ztorm_catalogue_cache', 'RRP', $this->string());
        $this->addColumn('ztorm_catalogue_cache', 'RRPCurrency', $this->string());
    }

    public function down()
    {
        $this->dropColumn('ztorm_catalogue_cache', 'RRP');
        $this->dropColumn('ztorm_catalogue_cache', 'RRPCurrency');
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
