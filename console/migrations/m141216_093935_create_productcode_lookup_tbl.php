<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\ProductcodeLookup;

class m141216_093935_create_productcode_lookup_tbl extends Migration
{
    public function up()
    {
        $this->createTable(ProductcodeLookup::tableName(), [
                'id'=>Schema::TYPE_PK,
        	'product_id'=> Schema::TYPE_INTEGER ."(50) NULL DEFAULT 0",
	        'name'=> Schema::TYPE_STRING."(255) NULL DEFAULT NULL",
        ]);

    }

    public function down()
    {
        $this->dropTable(ProductcodeLookup::tableName());
    }
}
