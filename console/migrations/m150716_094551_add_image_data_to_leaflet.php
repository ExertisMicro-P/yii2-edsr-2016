<?php

use yii\db\Schema;
use yii\db\Migration;

class m150716_094551_add_image_data_to_leaflet extends Migration
{
    public function up()
    {
        $this->addColumn(\common\models\ProductLeafletInfo::tableName(), 'image_type', 'VARCHAR(20) NULL AFTER key_ycoord');
        $this->addColumn(\common\models\ProductLeafletInfo::tableName(), 'image_data', 'MEDIUMBLOB NULL AFTER image_type');
    }

    public function down()
    {
        $this->dropColumn(\common\models\ProductLeafletInfo::tableName(), 'image_type');
        $this->dropColumn(\common\models\ProductLeafletInfo::tableName(), 'image_data');
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
