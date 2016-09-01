<?php

use yii\db\Schema;
use yii\db\Migration;

class m160318_143545_add_boxshot_to_catalogue_cache extends Migration
{
    public function up()
    {
        $this->addColumn('ztorm_catalogue_cache', 'Boxshot', $this->string());
    }

    public function down()
    {
        $this->dropColumn('ztorm_catalogue_cache', 'Boxshot');
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
