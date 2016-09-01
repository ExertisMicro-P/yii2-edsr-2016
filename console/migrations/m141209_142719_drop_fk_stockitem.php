<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\StockItem;

class m141209_142719_drop_fk_stockitem extends Migration
{
    public function up()
    {
        $this->dropForeignKey('fk_stock_item_digital_product1', StockItem::tableName());
    }

    public function down()
    {
        echo "m141209_142719_drop_fk_stockitem cannot be reverted.\n";

        return false;
    }
}
