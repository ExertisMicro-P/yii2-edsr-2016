<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\Account;

class m150217_134920_add_logo_to_account extends Migration
{
    public function up()
    {
        $this->addColumn(Account::tableName(), 'logo', Schema::TYPE_STRING);
    }

    public function down()
    {
        $this->dropColumn(Account::tableName(), 'logo');
    }
}
