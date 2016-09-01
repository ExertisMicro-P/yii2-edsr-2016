<?php

use yii\db\Schema;
use yii\db\Migration;

class m141224_124306_create_stock_activity extends Migration
{
    public function up()
    {
        $this->createTable('{{%stock_activity}}', [
            'id'            => Schema::TYPE_PK,
//            'user_id'       => Schema::TYPE_INTEGER . " NOT NULL COMMENT 'Link to the user table if the user record exists'",
            'stockroom_id'  => Schema::TYPE_INTEGER . " NULL COMMENT 'Link to the stockroom table if the user record exists'",
            'stock_item'    => Schema::TYPE_INTEGER . " NULL COMMENT 'Link to the stock_item entry'",
            'status'        => Schema::TYPE_STRING  . " NULL COMMENT 'The stock item status at this point'",
            'notes'         => Schema::TYPE_STRING  . " NOT NULL COMMENT 'The action applied at this point'",



            'destination_table' => Schema::TYPE_STRING  . " NULL COMMENT 'The table this items is now associated with'",
            'destination_id'=> Schema::TYPE_INTEGER  . " NULL COMMENT 'Record id in the destination table'",

            'sent_to'       => Schema::TYPE_STRING  . " NULL COMMENT 'name of the recipient (emailed, cupboard owner)'",
            'sent_to_email' => Schema::TYPE_STRING  . " NULL COMMENT 'Email address of recipient'",

            'created_at'    => Schema::TYPE_DATETIME,
            'updated_at'    => Schema::TYPE_TIMESTAMP,
            'created_by'    => Schema::TYPE_INTEGER,
            'updated_by'    => Schema::TYPE_INTEGER
        ]);

//        $this->addForeignKey('fk_stock_activity_user_record', '{{%stock_activity}}', "user_id",
//            '{{%user}}', "id");

        $this->addForeignKey('fk_stock_activity_stock_room_record', '{{%stock_activity}}', "stockroom_id",
            '{{%stockroom}}', "id");
    }

    public function down()
    {
        $this->dropTable('{{%stock_activity}}');
    }
}
