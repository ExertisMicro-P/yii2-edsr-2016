<?php

use yii\db\Schema;
use yii\db\Migration;

class m150629_201739_make_session_delivering_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%session_delivering}}', [
            'id'           => Schema::TYPE_PK,
            'account_id'   => Schema::TYPE_INTEGER . ' NOT NULL',
            'session_id'   => Schema::TYPE_STRING . '(128) NOT NULL COMMENT "Record by session and delete afer some time of no-use"',

            'stockitem_id' => Schema::TYPE_INTEGER . ' NOT NULL',

            'photo'        => Schema::TYPE_STRING . '(255) NOT NULL',
            'partcode'     => Schema::TYPE_STRING . '(20) NOT NULL',
            'description'  => Schema::TYPE_STRING . '(500) NOT NULL',
            'quantity'     => Schema::TYPE_INTEGER . ' NOT NULL',
            'po'           => Schema::TYPE_STRING,

            'created_by'   => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at'   => Schema::TYPE_DATETIME . ' NOT NULL',

            'updated_by'   => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at'   => Schema::TYPE_TIMESTAMP . ' NOT NULL',
        ]);

        $this->createIndex('sitmuniq', '{{%session_delivering}}', 'stockitem_id', true) ;

        $this->addForeignKey('siacc_fk', '{{%session_delivering}}', 'account_id', '{{%account}}', 'id');
        $this->addForeignKey('sitem_fk', '{{%session_delivering}}', 'stockitem_id', '{{%stock_item}}', 'id');

    }

    public function down()
    {
        $this->dropTable('{{%session_delivering}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
