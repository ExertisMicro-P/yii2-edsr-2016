<?php

use yii\db\Schema;
use yii\db\Migration;

class m141205_151859_init_taggable_behavior_tables extends Migration
{
    public function up()
    {
        // Global tag table
        $this->createTable('tbl_tag', [
                    'id' => Schema::TYPE_PK,
                    'frequency' => Schema::TYPE_INTEGER,
                    'name' => Schema::TYPE_STRING,
                ]);

        // tagging mapping table to StockItems
        $this->createTable('tbl_stockitem_tag_assn', [
                    'stockitem_id' => Schema::TYPE_INTEGER,
                    'tag_id' => Schema::TYPE_INTEGER,
                ]);
    }

    public function down()
    {
        $this->dropTable('tbl_tag');
        $this->dropTable('tbl_stockitem_tag_assn');


    }
}
