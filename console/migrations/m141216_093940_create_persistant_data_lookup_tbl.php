<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\PersistantDataLookup;

class m141216_093940_create_persistant_data_lookup_tbl extends Migration
{
    public function up()
    {
        $this->createTable(PersistantDataLookup::tableName(), [
                'id'=>Schema::TYPE_PK,
        	'name'=> Schema::TYPE_STRING."(50) NULL DEFAULT 0",
	        'value'=> Schema::TYPE_STRING."(50) NULL DEFAULT 0",
        ]);
        PersistantDataLookup::initData();
    }

    public function down()
    {
        $this->dropTable(PersistantDataLookup::tableName());
    }
}
