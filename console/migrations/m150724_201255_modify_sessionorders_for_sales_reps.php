<?php

use yii\db\Schema;
use yii\db\Migration;
use amnah\yii2\user\models\Role;

class m150724_201255_modify_sessionorders_for_sales_reps extends Migration
{
    public function up()
    {
        $this->addColumn(\common\models\SessionOrder::tableName(), 'sales_rep_id', Schema::TYPE_INTEGER .
                    ' NULL COMMENT "User id of the sales rep building this order" AFTER account_id');
        $this->addForeignKey('so_user_fk', '{{%session_order}}', 'sales_rep_id', '{{%user}}', 'id');

        $this->createTable('{{%sales_rep_order}}', [
            'id'           => Schema::TYPE_PK,
            'sales_rep_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "User id of the rep who created this order"',
            'account_id'   => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "The account the order is for"',
            'po'           => Schema::TYPE_STRING . ' NOT NULL COMMENT "The associated purchase, used to link to the order details table"',

            'created_by'   => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at'   => Schema::TYPE_DATETIME . ' NOT NULL',

            'updated_by'   => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at'   => Schema::TYPE_TIMESTAMP . ' NOT NULL',
        ]);

        $this->addForeignKey('sro_acc_fk', '{{%sales_rep_order}}', 'account_id', '{{%account}}', 'id');
        $this->addForeignKey('sro_user_fk', '{{%sales_rep_order}}', 'sales_rep_id', '{{%user}}', 'id');

        $this->addColumn(Role::tableName(), 'can_buy_for_customer', Schema::TYPE_BOOLEAN.' NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropColumn(Role::tableName(), 'can_buy_for_customer');
        $this->dropTable('{{%sales_rep_order}}');
        $this->dropForeignKey('so_user_fk', \common\models\SessionOrder::tableName());
        $this->dropColumn(\common\models\SessionOrder::tableName(), 'sales_rep_id');
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
