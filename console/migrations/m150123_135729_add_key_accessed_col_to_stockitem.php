<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\StockItem;

class m150123_135729_add_key_accessed_col_to_stockitem extends Migration
{
    public function up()
    {
        $this->addColumn(StockItem::tableName(), 'key_accessed', Schema::TYPE_TIMESTAMP);
    }

    public function down()
    {
        $this->dropColumn(StockItem::tableName(), 'key_accessed');

    }
}
