<?php

use yii\db\Migration;

class m160928_090812_add_deleted_at_column_to_dropship extends Migration
{
    public function up()
    {
        $this->addColumn('dropship_email_details', 'deleted_at', $this->dateTime());
    }

    public function down()
    {
        $this->dropColumn('dropship_email_details', 'deleted_at');
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
