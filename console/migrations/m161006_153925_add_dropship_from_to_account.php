<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\Account;

class m161006_153925_add_dropship_from_to_account extends Migration
{
    public function up()
    {
        $this->addColumn(Account::tableName(), 'drop_ship_from', Schema::TYPE_STRING . ' COMMENT "email from: address"');
    }

    public function down()
    {
        $this->dropColumn(Account::tableName(), 'drop_ship_from');
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
