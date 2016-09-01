<?php

use yii\db\Schema;
use yii\db\Migration;

class m141224_182947_add_account_to_emailed_user extends Migration
{
    public function up()
    {
        $this->addColumn(\common\models\EmailedUser::tableName(), 'account_id', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        $this->dropColumn(\common\models\EmailedUser::tableName(), 'account_id');
    }
}
