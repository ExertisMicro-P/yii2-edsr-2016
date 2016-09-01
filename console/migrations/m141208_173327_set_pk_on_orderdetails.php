<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\Orderdetails;

class m141208_173327_set_pk_on_orderdetails extends Migration
{
    public function up()
    {
        $this->alterColumn(Orderdetails::tableName(), 'id', Schema::TYPE_PK);
    }

    public function down()
    {
        $this->alterColumn(Orderdetails::tableName(), 'id', Schema::TYPE_INTEGER);


    }
}
