<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\Account;

class m150402_090507_add_key_flag_to_account extends Migration
{
    public function up()
    {
        $this->addColumn(Account::tableName(), 'include_key_in_email', Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn(Account::tableName(), 'include_key_in_email');
    }
}
