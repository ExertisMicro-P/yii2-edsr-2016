<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\DigitalProduct;

class m141209_161928_change_length_product_digital extends Migration
{
    public function up()
    {
        $this->alterColumn(DigitalProduct::tableName(), 'description', Schema::TYPE_STRING . '(255)');
    }

    public function down()
    {
        echo "m141209_161928_change_length_product_digital cannot be reverted.\n";

        return false;
    }
}
