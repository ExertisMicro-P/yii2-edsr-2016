<?php

use yii\db\Schema;
use yii\db\Migration;

class m160913_145448_add_brand_to_dropship extends Migration
{
    public function up()
    {
        $this->addColumn('{{%dropship_email_details}}', 'brand', Schema::TYPE_STRING . ' after po') ;
    }

    public function down()
    {
        $this->dropColumn('{{%dropship_email_details}}', 'brand');
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
