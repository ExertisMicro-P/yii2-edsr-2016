<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m141219_183533_createEmailedUser
 * ======================================
 * Creates the master table of a set used to record all transactions which
 * aren't placed in a cupboard
 *
 */
class m141219_183533_create_emailed_user extends Migration
{
    public function up()
    {
        $this->createTable('{{%emailed_user}}', [
            'id'            => Schema::TYPE_PK,
            'user_id'       => Schema::TYPE_INTEGER . " NULL DEFAULT NULL COMMENT 'Link to the user tbale if the user record exists'",
            'email'         => Schema::TYPE_STRING . "(255) NULL DEFAULT NULL COMMENT 'The email address if not in the user record'",
            'name'          => Schema::TYPE_STRING . "(255) NULL DEFAULT NULL COMMENT 'The recipient name provided when creating the order'",
            'order_number'  => Schema::TYPE_STRING . "(255) NOT NULL COMMENT 'An unique ID for this particular order'",
            'created_at'    => Schema::TYPE_DATETIME,
            'updated_at'    => Schema::TYPE_TIMESTAMP
        ]);

        $this->addForeignKey('fk_account_emailed_user_record', '{{%emailed_user}}', "user_id",
            '{{%user}}', "id");


    }

    public function down()
    {
        $this->dropTable('{{%emailed_user}}');
    }
}
