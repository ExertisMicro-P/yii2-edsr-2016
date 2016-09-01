<?php

use yii\db\Schema;
use yii\db\Migration;

class m160119_161935_add_spending_limit_column_to_users_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'spending_limit', $this->string(50)->defaultValue('0.00'));
    }

    public function down()
    {
        $this->dropColumn('user', 'spending_limit');
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
