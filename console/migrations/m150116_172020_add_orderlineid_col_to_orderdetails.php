<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\Orderdetails;

class m150116_172020_add_orderlineid_col_to_orderdetails extends Migration
{
    public function up()
    {
        $this->addColumn(Orderdetails::tableName(), 'orderlineid',Schema::TYPE_STRING . '(50)');
    }

    public function down()
    {
        $this->dropColumn(Orderdetails::tableName(), 'orderlineid');

    }
}
