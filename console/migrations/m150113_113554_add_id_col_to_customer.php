<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\Customer;
use common\models\Account;

class m150113_113554_add_id_col_to_customer extends Migration
{
    public function up()
    {
        \Yii::$app->getDb()->createCommand('SET foreign_key_checks = 0;')->execute();

        //$this->dropIndex(Customer::primaryKey()[0], Customer::tableName());

        $this->dropForeignKey('fk_account_customer1', Account::tableName());

        $this->dropPrimaryKey('exertis_account_number', Customer::tableName());
        $this->addColumn(Customer::tableName(), 'id', Schema::TYPE_PK);

        $this->createIndex('exertis_account_number', Customer::tableName(), 'exertis_account_number', true); // UNIQUE
        $this->addForeignKey('fk_account_customer1', Account::tableName(), 'customer_exertis_account_number', Customer::tableName(), 'exertis_account_number');

        \Yii::$app->getDb()->createCommand('SET foreign_key_checks = 1;')->execute();

     }

    public function down()
    {
        $this->dropColumn(Customer::tableName(), 'id');
        $this->addPrimaryKey(Customer::tableName(),'exertis_account_number', Schema::TYPE_STRING);

    }
}
