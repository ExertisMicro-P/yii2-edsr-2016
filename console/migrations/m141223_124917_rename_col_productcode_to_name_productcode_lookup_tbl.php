<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\ProductcodeLookup;

class m141223_124917_rename_col_productcode_to_name_productcode_lookup_tbl extends Migration
{
    public function up()
    {
        $table = Yii::$app->db->schema->getTableSchema(ProductcodeLookup::tableName());
        //die(yii\helpers\VarDumper::dump($table->columns,99,true));

        if(isset($table->columns->productcode)) {
            $this->renameColumn(ProductcodeLookup::tableName(), 'productcode', 'name');
        }
    }

    public function down()
    {
        $this->renameColumn(ProductcodeLookup::tableName(), 'name', 'productcode');
        
    }
}
