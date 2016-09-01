<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\StockItem;

class m141209_132301_add_product_cols_to_stockitem extends Migration
{
    public function up()
    {
        $this->addColumn(StockItem::tableName(), 'productcode', Schema::TYPE_STRING);
        $this->addColumn(StockItem::tableName(), 'eztorm_product_id', Schema::TYPE_INTEGER);
        $this->addColumn(StockItem::tableName(), 'eztorm_order_id', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        echo "m141209_132301_add_product_cols_to_stockitem cannot be reverted.\n";
        $this->dropColumn(StockItem::tableName(), 'productcode');
        $this->dropColumn(StockItem::tableName(), 'eztorm_product_id');
        $this->dropColumn(StockItem::tableName(), 'eztorm_order_id');


    }
}
