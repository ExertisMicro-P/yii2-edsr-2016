<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\StockItem;

class m150113_102747_add_send_email_col_to_stockitem extends Migration
{
    public function up()
    {
        $this->addColumn(StockItem::tableName(), 'send_email',Schema::TYPE_INTEGER);
    }

    public function down()
    {
        echo "m150113_102747_add_send_email_col_to_stockitem cannot be reverted.\n";
        
    }
}
