<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\AccountSopLookup;

class m150113_102800_add_PO_col_to_account_sop_lookup extends Migration
{
    public function up()
    {
        $this->addColumn(AccountSopLookup::tableName(), 'po',Schema::TYPE_STRING);
    }

    public function down()
    {
        $this->dropColumn(AccountSopLookup::tableName(), 'po');
       
    }
}
