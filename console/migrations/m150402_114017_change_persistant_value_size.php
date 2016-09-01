<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\PersistantDataLookup;

class m150402_114017_change_persistant_value_size extends Migration
{
    public function up()
    {
        $this->alterColumn(PersistantDataLookup::tableName(), 'value', Schema::TYPE_TEXT);
    }

    public function down()
    {
        $this->alterColumn(PersistantDataLookup::tableName(), 'value', Schema::TYPE_STRING);
    }
}
