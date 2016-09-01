<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m141219_184430_createEmailedItems
 * =======================================
 * This creates the table to store details of individual items which
 * are delivered via email rather than using a cupboard.
 */
class m141219_184430_create_emailed_item extends Migration
{
    public function up()
    {
        $this->createTable('{{%emailed_item}}', [
            'id'            => Schema::TYPE_PK,
            'emailed_user_id'   => Schema::TYPE_INTEGER . " NOT NULL COMMENT 'The corresponding emailed_user table id'",
            'stock_item_id' => Schema::TYPE_INTEGER . " NULL DEFAULT 0 COMMENT 'The id of the corresponding stock_item entry'",
            'status'        => Schema::TYPE_INTEGER . " NOT NULL DEFAULT 0 COMMENT 'To be defined, but the status of this ordered item, '",
            'created_at'    => Schema::TYPE_DATETIME,
            'updated_at'    => Schema::TYPE_TIMESTAMP . ''
        ]);

        $this->addForeignKey('fk_account_emailed_user_id', '{{%emailed_item}}', "emailed_user_id",
            '{{%emailed_user}}', "id");
        $this->addForeignKey('fk_emailed_stock_item', '{{%emailed_item}}', "stock_item_id",
            '{{%stock_item}}', "id");

    }

    public function down()
    {
        $this->dropTable('{{%emailed_item}}');
    }
}
