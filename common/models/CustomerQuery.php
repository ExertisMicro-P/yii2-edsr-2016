<?php

namespace common\models;

use yii\db\ActiveQuery;

class CustomerQuery extends ActiveQuery
{
    public function waitingForEmailSetupOrNoAccount($id=NULL)
    {
        if ($id==NULL) {
            $this->joinWith(['account','account.users']);
            //$this->andWhere('account.id IS NULL OR (user.account_id IS NOT NULL AND user.email IS NULL)');
            $this->andWhere('account.id IS NULL OR (user.email IS NULL)');
        } else {
            $this->with(['account','users']);
            $this->andWhere('account.id IS NULL OR (user.account_id IS NOT NULL AND user.email IS NULL and account.id=:id)', [':id'=>$id]);
        }
        return $this;
    }
}