<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property integer $role_id
 * @property integer $status
 * @property string $email
 * @property string $new_email
 * @property string $username
 * @property string $password
 * @property string $auth_key
 * @property string $api_key
 * @property string $login_ip
 * @property string $login_time
 * @property string $create_ip
 * @property string $create_time
 * @property string $update_time
 * @property string $ban_time
 * @property string $ban_reason
 * @property string $uuid
 * @property integer $account_id
 *
 * @property EmailedUser[] $emailedUsers
 * @property SalesRepOrder[] $salesRepOrders
 * @property SessionOrder[] $sessionOrders
 */
class Useradmin extends \common\models\gauth\GAUser
{
    
}
