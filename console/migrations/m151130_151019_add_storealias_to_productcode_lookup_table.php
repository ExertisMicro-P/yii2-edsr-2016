<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\ProductcodeLookup;
use common\models\ZtormAccess;

class m151130_151019_add_storealias_to_productcode_lookup_table extends Migration
{
   
    public function up()
    {
        $this->addColumn(ProductcodeLookup::tableName(), 'storealias', Schema::TYPE_STRING . '(20) NOT NULL DEFAULT "DEFAULT" COMMENT "Linked to Productcode_lookup table"');
   //     $this->addForeignKey('fkstorealias', ProductcodeLookup::tableName(), 'storealias', ZtormAccess::tableName(), 'storealias','RESTRICT','RESTRICT');
    }

    public function down()
    {
        echo "m151130_151019_add_storealias_to_productcode_lookup_table cannot be reverted.\n";

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
