<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\Account;

class m161003_210549_add_bcc_and_subject_to_account extends Migration
{
    public function up()
    {
        $this->addColumn(Account::tableName(), 'drop_ship_bcc', Schema::TYPE_STRING . ' NULL COMMENT"Address for BCC when sending drop ship email"') ;
        $this->addColumn(Account::tableName(), 'drop_ship_subject', Schema::TYPE_STRING . ' NULL COMMENT"Template for the drop ship email subject. Can replace \{\{ORDER_NUMBER}} and \{\{PO}}"') ;
    }

    public function down()
    {
        $this->dropColumn(Account::tableName(), 'drop_ship_subject') ;
        $this->dropColumn(Account::tableName(), 'drop_ship_bcc') ;
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
