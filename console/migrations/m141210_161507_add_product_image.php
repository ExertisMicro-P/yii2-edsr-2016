<?php

use yii\db\Schema;
use yii\db\Migration;

class m141210_161507_add_product_image extends Migration
{
    public function up()
    {

        $this->createTable('product_image', [
            'id'                 => Schema::TYPE_PK,
            'digital_product_id' => Schema::TYPE_INTEGER . " NOT NULL",
            'image_url'          => Schema::TYPE_STRING . " NOT NULL",
            'image_tn'           => Schema::TYPE_STRING . " NOT NULL",

            'width'              => Schema::TYPE_INTEGER,
            'height'             => Schema::TYPE_INTEGER,

            'create_at'          => Schema::TYPE_DATETIME,
            'updated_at'         => Schema::TYPE_TIMESTAMP . ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',

        ]);

        $this->addForeignKey('pimage_fk', 'product_image', 'digital_product_id', 'digital_product', 'id') ;
    }

    public function down()
    {
        $this->dropTable('product_images');

    }
}
