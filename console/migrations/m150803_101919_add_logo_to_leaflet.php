<?php

use yii\db\Schema;
use yii\db\Migration;

class m150803_101919_add_logo_to_leaflet extends Migration
{
    public function up()
    {

        $this->addColumn(\common\models\ProductLeafletInfo::tableName(), 'logo_xcoord', 'DOUBLE NULL COMMENT "x-coord for the start of the logo" AFTER key_box_height');
        $this->addColumn(\common\models\ProductLeafletInfo::tableName(), 'logo_ycoord', 'DOUBLE NULL COMMENT "y-coord for the start of the logo" AFTER logo_xcoord');

        $this->addColumn(\common\models\ProductLeafletInfo::tableName(), 'logo_box_width', 'INTEGER NULL AFTER logo_ycoord');
        $this->addColumn(\common\models\ProductLeafletInfo::tableName(), 'logo_box_height', 'INTEGER NULL AFTER logo_box_width');

    }

    public function down()
    {
        $this->dropColumn(\common\models\ProductLeafletInfo::tableName(), 'logo_box_height');
        $this->dropColumn(\common\models\ProductLeafletInfo::tableName(), 'logo_box_width');
        $this->dropColumn(\common\models\ProductLeafletInfo::tableName(), 'logo_ycoord');
        $this->dropColumn(\common\models\ProductLeafletInfo::tableName(), 'logo_xcoord');
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
