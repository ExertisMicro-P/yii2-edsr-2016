<?php

use yii\db\Schema;
use yii\db\Migration;

class m141203_132114_createAuthLog extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id'                   => Schema::TYPE_PK,
            'user_id'              => Schema::TYPE_INTEGER . ' NOT NULL',
            'auth_code'             => Schema::TYPE_STRING . '(6) NOT NULL',
            'ip_address'            => Schema::TYPE_STRING . '(45) NOT NULL',
            'status'               => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',

            'created_at'           => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at'           => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
    }

    public function down()
    {
        echo "m141203_132114_createAuthLog cannot be reverted.\n";

        return false;
    }
}
