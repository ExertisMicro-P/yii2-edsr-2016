<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\StockItem;

class m141212_103804_drop_digital_id_from_stockitem extends Migration
{
    public function up()
    {
        $this->dropColumn(StockItem::tableName(), 'digital_product_id');
    }

    public function down()
    {
        $this->addColumn('{{%stock_item}}', 'digital_product_id', 'int NOT NULL AFTER stockroom_id') ;
    }
}
