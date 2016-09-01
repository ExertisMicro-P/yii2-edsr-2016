<?php

use yii\db\Schema;
use yii\db\Migration;

class m141204_102407_addUUIDToUser extends Migration
{
    public function up()
    {
        $this->addColumn('{{%user}}', 'uuid', 'string(36) Comment "Universall Unique Id used for google authentication"') ;
        $this->execute('CREATE TRIGGER user_uuid BEFORE INSERT ON {{%user}} FOR EACH ROW SET NEW.uuid = UUID()') ;
    }

    public function down()
    {
        $this->execute('DROP TRIGGER user_uuid') ;
        $this->dropColumn('{{%user}}', 'uuid') ;

    }
}
