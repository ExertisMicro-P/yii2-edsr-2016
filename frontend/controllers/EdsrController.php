<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Stockroom;
use common\models\StockItem;
use common\models\StockItemSearch;

use yii\filters\AccessControl;
use common\components\DigitalPurchaser ;

use exertis\savewithaudittrail\models\Audittrail;

use common\models\Accounts;

class EdsrController  extends SiteController
{
    public $layout = false;

    protected $user;
    protected $userType;                 // Set to a for admin, u for user and c for customer
    protected $stockroomId;              // The current stock room id.


}
