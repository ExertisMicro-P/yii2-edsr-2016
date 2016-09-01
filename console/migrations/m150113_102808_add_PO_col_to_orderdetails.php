<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\Orderdetails;

class m150113_102808_add_PO_col_to_orderdetails extends Migration
{
    public function up()
    {
        $this->addColumn(Orderdetails::tableName(), 'po',Schema::TYPE_STRING);
    }

    public function down()
    {
        $this->dropColumn(Orderdetails::tableName(), 'po');
        
    }
}
