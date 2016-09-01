<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\ZtormAccess;

class m151125_115946_create_ztormaccess_table extends Migration
{
    public function up()
    {
        $this->createTable('ztormaccess',[
           'id'=>Schema::TYPE_PK,
           'type'=>Schema::TYPE_STRING.'(100) NOT NULL DEFAULT "TEST" COMMENT "Should be either TEST or LIVE"',
           'storeid'=>Schema::TYPE_INTEGER.'(10) COMMENT "Identify ztorm store" ',
           'keycode'=>Schema::TYPE_STRING.'(20) COMMENT "store password" ',
           'url'=>Schema::TYPE_STRING.'(200) COMMENT "url to the store" ',
           'storealias'=>Schema::TYPE_STRING.'(20) NOT NULL DEFAULT "DEFAULT" COMMENT "VALUE MUST MATCH productcodelookup table"',
     //      'KEY `storealias` (`storealias`)',
        ]);
        $this->insert(ZtormAccess::tableName(),array(
         'storeid'=>'0',
         'keycode' =>'password',
         ));
    }

    public function down()
    {
        //echo "m151125_115946_create_ztormaccess_table cannot be reverted.\n";
        $this->dropTable('ztormaccess');
        return false;
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
