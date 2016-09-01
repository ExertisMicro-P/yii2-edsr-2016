<?php

use yii\db\Schema;
use yii\db\Migration;
use common\models\Stockroom;
use common\models\Account;

class m141208_131228_rename_columns_in_stockroom extends Migration
{
    public function up()
    {
        /*
         * CREATE TABLE `stockroom` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `account_id` INT(11) NOT NULL,
            `name` VARCHAR(128) NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            INDEX `fk_stockroom_user1` (`account_id`)
            )
            COLLATE='latin1_swedish_ci'
            ENGINE=InnoDB
            AUTO_INCREMENT=2;


         */
        \Yii::$app->getDb()->createCommand('SET foreign_key_checks = 0;')->execute();
       $this->dropTable(Stockroom::tableName());
       \Yii::$app->getDb()->createCommand('SET foreign_key_checks = 1;')->execute();
       $this->createTable(Stockroom::tableName(), [
           'id' => Schema::TYPE_PK,
           'account_id' => Schema::TYPE_INTEGER,
           'name' => Schema::TYPE_STRING.'(128)',
       ]);
       $this->createIndex('fk_stockroom_account1', Stockroom::tableName(), 'account_id');
    }

    public function down()
    {
        echo "m141208_131228_rename_columns_in_stockroom cannot be reverted.\n";

        return false;
    }
}
