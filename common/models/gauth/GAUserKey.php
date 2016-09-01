<?php
/**
 * Created by PhpStorm.
 * User: noelwalsh
 * Date: 04/12/2014
 * Time: 21:51
 */

namespace common\models\gauth;

use exertis\savewithaudittrail\SaveWithAuditTrailBehavior;

class GAUserKey extends \amnah\yii2\user\models\UserKey
{
    /**
     * GA PENDING
     * ==========
     * This is used to flag the case where we're waiting for the user to
     * enter their password prior to use creating them as a user on the
     * gauthify site
     *
     */
    const TYPE_GAPENDING    = 100 ;
/*
    public function behaviors() {
        return [
            [
                'class' => SaveWithAuditTrailBehavior::className(),
            ],
        ];
    }
 * 
 */

}
