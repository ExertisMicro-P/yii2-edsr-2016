<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\DigitalProduct;
class m141209_162825_digital_product_length_change extends Migration
{
    public function up()
    {
        $this->alterColumn(DigitalProduct::tableName(), 'partcode', Schema::TYPE_STRING . '(255)');
    }

    public function down()
    {
        echo "m141209_162825_digital_product_length_change cannot be reverted.\n";

        return false;
    }
}
