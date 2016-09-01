<?php

use yii\db\Schema;
use yii\db\Migration;

class m141216_104804_addUUIDToAccount extends Migration
{
    public function up()
    {
        $this->addColumn('{{%account}}', 'uuid', 'string(36) Comment "Universall Unique Id used for google authentication"') ;
        $this->execute('CREATE TRIGGER account_uuid BEFORE INSERT ON {{%account}} FOR EACH ROW SET NEW.uuid = UUID()') ;
    }

    public function down()
    {
        $this->execute('DROP TRIGGER account_uuid') ;
        $this->dropColumn('{{%account}}', 'uuid') ;

    }
}
