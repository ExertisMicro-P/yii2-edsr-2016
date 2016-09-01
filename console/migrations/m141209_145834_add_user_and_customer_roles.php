<?php

use yii\db\Schema;
use yii\db\Migration;
use amnah\yii2\user\models\Role;

class m141209_145834_add_user_and_customer_roles extends Migration
{
    public function up()
    {
        $this->addColumn(Role::tableName(), 'can_user', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT"1 if can carry out Exertis customer actions"') ;
        $this->addColumn(Role::tableName(), 'can_customer', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT"1 if can carry out Exertis customer\s customer actions"') ;

        $this->execute ('ALTER TABLE {{%role}} CHANGE `update_time` `update_time` TIMESTAMP  NULL  DEFAULT CURRENT_TIMESTAMP  ON UPDATE CURRENT_TIMESTAMP') ;

        $this->execute('UPDATE {{%role}} set can_user=1 WHERE name="User"') ;
        $this->execute('INSERT INTO {{%role}} (name, create_time, can_customer) VALUES("Customer", NOW(), 1)') ;

    }

    public function down()
    {
        $this->execute('DELETE FROM {{%role}} WHERE name="Customer"') ;
        $this->dropColumn(Role::tableName(), 'can_customer') ;
        $this->dropColumn(Role::tableName(), 'can_user') ;
    }
}
