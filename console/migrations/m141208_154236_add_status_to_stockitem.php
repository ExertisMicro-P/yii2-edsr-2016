<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\StockItem;

class m141208_154236_add_status_to_stockitem extends Migration
{
    public function up()
    {
        $this->addColumn(StockItem::tableName(), 'status', Schema::TYPE_STRING);


    }

    public function down()
    {
        $this->dropColumn(StockItem::tableName(), 'status');


    }
}
