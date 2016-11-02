<?php

use yii\db\Schema;
use yii\db\Migration;
use amnah\yii2\user\models\Role;
use common\models\DigitalProduct;

class m161102_173127_add_no_sop_to_account extends Migration
{
    public function up()
    {
        $this->addColumn('{{%account}}', 'allow_drop_ship_sop',
                         Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 1
                          COMMENT"Allow or forbid SOP generation whena drop-ship-emal is requested"
                          after drop_ship_from') ;
    }

    public function down()
    {
        $this->dropColumn('{{%account}}', 'allow_drop_ship_sop');
    }
}
