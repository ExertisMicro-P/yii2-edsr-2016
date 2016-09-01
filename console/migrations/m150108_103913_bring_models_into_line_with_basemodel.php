<?php

use yii\db\Schema;
use yii\db\Migration;

use common\models\EmailedUser;
use common\models\EmailedItem;

class m150108_103913_bring_models_into_line_with_basemodel extends Migration
{
    public function up()
    {
        $this->addColumn(EmailedUser::tableName(), 'created_by', Schema::TYPE_INTEGER);
        $this->addColumn(EmailedUser::tableName(), 'updated_by', Schema::TYPE_INTEGER);

        $this->addColumn(EmailedItem::tableName(), 'created_by', Schema::TYPE_INTEGER);
        $this->addColumn(EmailedItem::tableName(), 'updated_by', Schema::TYPE_INTEGER);
    }

    public function down()
    {
        $this->dropColumn(EmailedUser::tableName(), 'created_by');
        $this->dropColumn(EmailedUser::tableName(), 'updated_by');

        $this->dropColumn(EmailedItem::tableName(), 'created_by');
        $this->dropColumn(EmailedItem::tableName(), 'updated_by');
    }
}
