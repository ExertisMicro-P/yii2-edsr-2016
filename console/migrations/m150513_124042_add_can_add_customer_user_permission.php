<?php

use yii\db\Schema;
use yii\db\Migration;
use amnah\yii2\user\models\Role;

class m150513_124042_add_can_add_customer_user_permission extends Migration
{
    public function up()
    {
        $this->execute('UPDATE role SET NAME = "CustomerAdmin" where name = "User"') ;
        $this->addColumn(Role::tableName(), 'can_add_customer_user', Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT 0');
        $this->execute('UPDATE role SET can_add_customer_user = 1 where name = "CustomerAdmin"') ;
    }

    public function down()
    {
        $this->dropColumn(Role::tableName(), 'can_add_customer_user');
        $this->execute('UPDATE role SET NAME = "User" where name = "CustomerAdmin"') ;
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
