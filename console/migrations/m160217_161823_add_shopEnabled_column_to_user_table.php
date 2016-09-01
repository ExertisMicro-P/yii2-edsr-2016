<?php

use yii\db\Schema;
use yii\db\Migration;

class m160217_161823_add_shopEnabled_column_to_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'shopEnabled', $this->boolean()->defaultValue(false));
        $this->createIndex('shopEnabledIndex', 'user', 'shopEnabled');
    }

    public function down()
    {
        $this->dropColumn('user', 'shopEnabled');
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
