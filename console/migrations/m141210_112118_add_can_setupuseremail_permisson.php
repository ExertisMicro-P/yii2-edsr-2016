<?php

use yii\db\Schema;
use yii\db\Migration;

use amnah\yii2\user\models\Role;

class m141210_112118_add_can_setupuseremail_permisson extends Migration
{
    public function up()
    {
        $this->addColumn(Role::tableName(), 'can_setupuseremail', Schema::TYPE_SMALLINT.'(6) NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn(Role::tableName(), 'can_setupuseremail');

    }
}
