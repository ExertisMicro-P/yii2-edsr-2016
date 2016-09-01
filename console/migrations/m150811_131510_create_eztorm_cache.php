<?php

use yii\db\Schema;
use yii\db\Migration;

class m150811_131510_create_eztorm_cache extends Migration
{
    public function up()
    {
        $this->createTable('{{%eztorm_cache}}', [
            'id'              => Schema::TYPE_PK,
            'partcode'        => Schema::TYPE_STRING . ' NOT NULL COMMENT "The digital product partcode" ',
            'eztorm_id'       => Schema::TYPE_STRING . ' NOT NULL COMMENT "The matching eztorm id (32000+) as in productcode_lookup.product_id" ',
            'valid_until'     => Schema::TYPE_DATETIME . ' NOT NULL COMMENT "The date and time after which this should be re-read from eztorm"',

            'xml'             => Schema::TYPE_TEXT . ' NOT NULL COMMENT "The full xml from the original request"',

            'Name'            => Schema::TYPE_STRING . ' NULL COMMENT "The product name"',
            'Category'        => Schema::TYPE_STRING . ' NULL COMMENT "A single category name"',
            'Format'          => Schema::TYPE_STRING . ' NULL COMMENT "Intended system - eg PC, Mac, Mobile..."',

            'Publisher'       => Schema::TYPE_STRING . ' NULL COMMENT ""',
            'InformationFull' => Schema::TYPE_TEXT . ' NULL COMMENT "Full product description"',
            'Requirements'    => Schema::TYPE_STRING . ' NULL COMMENT "Game system requirements"',

            'PEGI_Age_Others' => Schema::TYPE_INTEGER . ' NULL COMMENT "Indicator of any age restriction on games, see http://en.wikipedia.org/wiki/PEGI"',

            'Boxshot'         => Schema::TYPE_STRING . ' NULL COMMENT "Url for the box image"',
            'Screenshots'     => Schema::TYPE_STRING . ' NULL COMMENT "Additional images"',
            'Genres'          => Schema::TYPE_STRING . ' NULL COMMENT ""',

            'created_at'      => Schema::TYPE_DATETIME,
            'updated_at'      => Schema::TYPE_TIMESTAMP,
            'created_by'      => Schema::TYPE_INTEGER,
            'updated_by'      => Schema::TYPE_INTEGER
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%eztorm_cache}}');
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
