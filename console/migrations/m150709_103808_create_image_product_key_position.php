<?php

use yii\db\Schema;
use yii\db\Migration;

class m150709_103808_create_image_product_key_position extends Migration
{
    public function up()
    {
        $tableOptions = null;
//        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
//            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
//        }

        $this->createTable('{{%product_leaflet_info}}', [
            'id'           => Schema::TYPE_PK,
            'partcode'      => Schema::TYPE_STRING . ' NOT NULL COMMENT "The digital product partcode" ',
            'image'         => Schema::TYPE_STRING . ' NULL COMMENT "The path, inside the main container, to the image on which the key is overlaid"',
            'key_xcoord'    => Schema::TYPE_DOUBLE . ' NULL COMMENT "The x-coordinate for the start of the key"',
            'key_ycoord'    => Schema::TYPE_DOUBLE . ' NULL COMMENT "The y-coordinate for the start of the key"',

            'created_at'    => Schema::TYPE_DATETIME,
            'updated_at'    => Schema::TYPE_TIMESTAMP,
            'created_by'    => Schema::TYPE_INTEGER,
            'updated_by'    => Schema::TYPE_INTEGER
        ], $tableOptions);

        // -------------------------------------------------------------------
        // Fails to create the key, so ignore
        // -------------------------------------------------------------------
//        $this->addForeignKey('pli_pcode', '{{%product_leaflet_info}}', 'partcode', '{{%digital_product}}', 'partcode') ;
    }

    public function down()
    {
        $this->dropTable('{{%product_leaflet_info}}');
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
