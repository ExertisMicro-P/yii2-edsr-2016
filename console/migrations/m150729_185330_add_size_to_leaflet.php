<?php

use yii\db\Schema;
use yii\db\Migration;

class m150729_185330_add_size_to_leaflet extends Migration
{
    public function up()
    {
        $this->addColumn(\common\models\ProductLeafletInfo::tableName(), 'key_box_width', 'INTEGER NULL AFTER key_ycoord');
        $this->addColumn(\common\models\ProductLeafletInfo::tableName(), 'key_box_height', 'INTEGER NULL AFTER key_box_width');
    }

    public function down()
    {
        $this->dropColumn(\common\models\ProductLeafletInfo::tableName(), 'key_box_height');
        $this->dropColumn(\common\models\ProductLeafletInfo::tableName(), 'key_box_width');
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
