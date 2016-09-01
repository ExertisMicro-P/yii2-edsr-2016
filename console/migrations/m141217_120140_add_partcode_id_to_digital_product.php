<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\DigitalProduct;

class m141217_120140_add_partcode_id_to_digital_product extends Migration
{
    public function up()
    {
        $this->addColumn(DigitalProduct::tableName(), 'eztorm_id', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        echo "m141217_120140_add_partcode_id_to_digital_product cannot be reverted.\n";

        return false;
    }
}
