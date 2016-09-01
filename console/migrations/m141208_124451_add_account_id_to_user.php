<?php

use yii\db\Schema;
use yii\db\Migration;
use amnah\yii2\user\models\User;

class m141208_124451_add_account_id_to_user extends Migration
{
    public function up()
    {
        $this->addColumn(User::tableName(), 'account_id', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        echo "m141208_124451_add_account_id_to_user cannot be reverted.\n";

        return false;
    }
}
