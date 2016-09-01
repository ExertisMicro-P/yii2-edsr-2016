<?php

use yii\db\Schema;
use yii\db\Migration;
use amnah\yii2\user\models\Role;
use common\models\DigitalProduct;


class m150617_093941_add_role_can_buy_and_digital_product_enabled extends Migration
{
    public function up()
    {
        $this->addColumn(Role::tableName(), 'can_buy', Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT 0');
        $this->addColumn(DigitalProduct::tableName(), 'enabled', Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT 0 AFTER is_digital');
    }

    public function down()
    {
        $this->dropColumn(DigitalProduct::tableName(), 'enabled');
        $this->dropColumn(Role::tableName(), 'can_buy');
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
