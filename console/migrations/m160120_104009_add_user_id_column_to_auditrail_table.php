<?php

use yii\db\Schema;
use yii\db\Migration;

class m160120_104009_add_user_id_column_to_auditrail_table extends Migration
{
    public function up()
    {
        $this->addColumn('audittrail', 'user_id', $this->integer()->notNull());
    }

    public function down()
    {
        $this->dropColumn('audittrail', 'user_id');
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
