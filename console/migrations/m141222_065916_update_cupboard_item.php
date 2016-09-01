<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m141222_065916_update_cupboard_item
 * =========================================
 * Changes the cupboard to link back to the stock_item rather than the
 * digital_product, and adds a status column
 *
 */
class m141222_065916_update_cupboard_item extends Migration
{
    public function up()
    {
        $this->dropForeignKey('fk_cupboard_item_digital_product1', \common\models\CupboardItem::tableName());
        $this->dropColumn(\common\models\CupboardItem::tableName(), 'digital_product_id');
        $this->dropColumn(\common\models\CupboardItem::tableName(), 'timestamp_added');

        $this->addColumn(\common\models\CupboardItem::tableName(), 'stock_item_id', Schema::TYPE_INTEGER .
                    " NOT NULL COMMENT'The original stock item record, with details of the EZStorm item' AFTER cupboard_id");
        $this->addColumn(\common\models\CupboardItem::tableName(), 'status', Schema::TYPE_INTEGER .
                    " NOT NULL DEFAULT 0 COMMENT 'To be defined, but the status of this ordered item, ' AFTER stock_item_id") ;

        $this->addColumn(\common\models\CupboardItem::tableName(), 'order_number', Schema::TYPE_STRING .
                    " NOT NULL COMMENT 'An unique ID for this particular order' AFTER status") ;

        $this->addColumn(\common\models\CupboardItem::tableName(), 'created_at', Schema::TYPE_DATETIME) ;
        $this->addColumn(\common\models\CupboardItem::tableName(), 'updated_at', Schema::TYPE_TIMESTAMP) ;

        $this->addForeignKey('fk_cupboard_item_stock_item1', '{{%cupboard_item}}', "stock_item_id", '{{%stock_item}}', "id");


    }

    public function down()
    {
        $this->dropColumn(\common\models\CupboardItem::tableName(), 'updated_at');
        $this->dropColumn(\common\models\CupboardItem::tableName(), 'created_at');
        $this->dropColumn(\common\models\CupboardItem::tableName(), 'status');
        $this->dropColumn(\common\models\CupboardItem::tableName(), 'order_number');

        $this->dropForeignKey('fk_cupboard_item_stock_item1', \common\models\CupboardItem::tableName());
        $this->dropColumn(\common\models\CupboardItem::tableName(), 'stock_item_id');

        $this->addColumn(\common\models\CupboardItem::tableName(), 'timestamp_added', Schema::TYPE_TIMESTAMP) ;

        $this->addColumn(\common\models\CupboardItem::tableName(), 'digital_product_id', Schema::TYPE_INTEGER . " NOT NULL AFTER cupboard_id") ;
        $this->addForeignKey('fk_cupboard_item_digital_product1', '{{%cupboard_item}}', "digital_product_id", '{{%digital_product}}', "id");

    }
}
