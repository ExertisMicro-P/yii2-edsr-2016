<?php

use yii\db\Schema;
use yii\db\Migration;
use amnah\yii2\user\models\Role;

class m150123_132729_add_can_monitor_sop_permission extends Migration
{
    public function up()
    {
        $this->addColumn(Role::tableName(), 'can_monitor_sales', Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0 COMMENT"1 if can use sales monitoring views"') ;

    }

    public function down()
    {
        $this->dropColumn(Role::tableName(), 'can_monitor_sales') ;
    }
}
