<?php

use yii\db\Schema;
use yii\db\Migration;

class m160914_102642_add_store_alias_to_account extends Migration
{
    public function up()
    {
        $this->addColumn('{{%account}}', 'storealias', $this->string(20) . ' default "EDSR" COMMENT"Use, in conjunction with the story type (test/live) to lookup the  actual store id in ztormaccess" after id') ;
    }

    public function down()
    {
        $this->dropColumn('{{%account}}', 'storealias');
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
