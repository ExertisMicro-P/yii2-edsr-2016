<?php

use yii\db\Schema;
use yii\db\Migration;

class m160913_142143_remove_order_key_from_dropship extends Migration
{
    public function up()
    {
        $this->dropForeignKey('fk_sde_order', '{{%dropship_email_details}}');
        $this->dropColumn(\common\models\DropshipEmailDetails::tableName(), 'orderdetails_id');

    }

    public function down()
    {
        $this->addColumn('{{%dropship_email_details}}', 'orderdetails_id', Schema::TYPE_INTEGER);
        $this->addForeignKey('fk_sde_order', '{{%dropship_email_details}}', 'orderdetails_id', '{{%orderdetails}}', 'id');
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
