<?php

namespace common\models;

use yii\db\ActiveQuery;

class AccountQuery extends ActiveQuery
{
    public function waitingForEmailSetup($id=NULL)
    {
        if ($id==NULL) {
            $this->joinWith('users');
            $this->andWhere('user.account_id IS NOT NULL AND user.email IS NULL');
        } else {
            $this->joinWith('users');
            $this->andWhere('user.account_id IS NOT NULL AND user.email IS NULL and account.id=:id', [':id'=>$id]);            
        }
        return $this;
    }
}