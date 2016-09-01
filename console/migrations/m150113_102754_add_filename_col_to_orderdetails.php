<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\Orderdetails;

class m150113_102754_add_filename_col_to_orderdetails extends Migration
{
    public function up()
    {
        $this->addColumn(Orderdetails::tableName(), 'filename',Schema::TYPE_STRING);
    }

    public function down()
    {
        $this->dropColumn(Orderdetails::tableName(), 'filename');
       
    }
}
