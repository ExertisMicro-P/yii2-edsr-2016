<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\Customer;
class m141209_134340_change_name_type_customer extends Migration
{
    public function up()
    {
        $this->alterColumn(Customer::tableName(), 'name', Schema::TYPE_STRING);
    }

    public function down()
    {
        echo "m141209_134340_change_name_type_customer cannot be reverted.\n";

        return false;
    }
}
