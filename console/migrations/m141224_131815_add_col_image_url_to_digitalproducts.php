<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\DigitalProduct;

class m141224_131815_add_col_image_url_to_digitalproducts extends Migration
{
    public function up()
    {
        $this->addColumn(DigitalProduct::tableName(), 'image_url', 'text');
    }

    public function down()
    {
        $this->dropColumn(DigitalProduct::tableName(), 'image_url');

    }
}
