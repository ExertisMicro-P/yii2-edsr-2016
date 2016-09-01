<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\StockItem;

class m150122_091439_add_spare_col_to_stockitem extends Migration
{
    public function up()
    {
        $this->addColumn(StockItem::tableName(), 'spare',Schema::TYPE_INTEGER . "(2)NULL DEFAULT '0'");
    }

    public function down()
    {
        $this->dropColumn(StockItem::tableName(), 'spare');

    }
}
