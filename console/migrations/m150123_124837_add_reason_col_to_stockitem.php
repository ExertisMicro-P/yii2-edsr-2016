<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\StockItem;

class m150123_124837_add_reason_col_to_stockitem extends Migration
{
    public function up()
    {
        $this->addColumn(StockItem::tableName(), 'reason',Schema::TYPE_STRING . "(100)");
    }

    public function down()
    {
        $this->dropColumn(StockItem::tableName(), 'reason');

    }
}
