<?php

use yii\db\Schema;
use yii\db\Migration;

class m150722_111328_create_product_faqs extends Migration
{
    public function up()
    {
//        $this->createTable('{{%digital_product_faqs}}', [
//            'id'         => Schema::TYPE_PK,
//            'digital_product_id'         => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "The digital product id" ',
////            'partcode'   => Schema::TYPE_STRING . ' NOT NULL COMMENT "The digital product partcode" ',
//            'faqs'       => Schema::TYPE_TEXT . ' NULL COMMENT "The text"',
//
//            'created_at' => Schema::TYPE_DATETIME,
//            'updated_at' => Schema::TYPE_TIMESTAMP,
//            'created_by' => Schema::TYPE_INTEGER,
//            'updated_by' => Schema::TYPE_INTEGER
//        ]);
//
//        $this->addForeignKey('dpf_pcode', '{{%digital_product_faqs}}', 'digital_product_id', '{{%digital_product}}', 'id') ;

        $this->addColumn(\common\models\DigitalProduct::tableName(), 'faqs', Schema::TYPE_TEXT . ' NULL COMMENT "The text" AFTER is_digital');


    }

    public function down()
    {
        $this->dropColumn(\common\models\DigitalProduct::tableName(), 'faqs');
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
