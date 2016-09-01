<?php
namespace common\components;

use Yii;
use yii\console\Exception;
use common\models\SessionOrder;
use common\models\Orderdetails;

/**
 * Class CreditLevel
 *
 * @package common\components
 */
class CreditLevel
{
    const MAX_LIFE_IN_MINUTES = 15;          // 15 minutes before a session order times out

    private $user;

    /**
     * CONSTRUCTOR
     * ===========
     *
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->clearOldOrderedItems();
    }

    /**
     * READ CURRENT CREDIT
     * ===================
     * This will return the outstanding credit balance for the account,
     * reading the cleared value from the credit table and then subtracting
     * the value of
     *      1) all items recorded in the session_order
     *      2) all entries in the stock_item table which haven't been cleared
     *         this is flagged by the fact that the filename is missing from
     *         the associated orderedetails record.
     */
    public function readCurrentCredit()
    {
        $balance = ['limit' => 0, 'balance' => 0];

        if ($this->user && $this->user->account) {
            $balance = $this->getCurrentCredit();

            //if ($balance['balance'] > 0) {
            if (($balance['limit'] + $balance['balance']) > 0) { // RCH 20160212
                $pending = $this->getPendingOrderValue();
                $waiting = $this->getUnclearedOrders();

                //$balance['balance'] -= ($pending + $waiting);
                $balance['balance'] -= ($pending + $waiting);
            }
        }

        return $balance;
    }

    /**
     * GET CURRENT CREDIT
     * ==================
     * This returns an array of the credit limit and balance, adjusted to allow
     * for all known items not included in the main balance.
     *
     *
     * @return array
     */
    private function getCurrentCredit()
    {
        $account = $this->user->account;
        $credit  = $account->credit;

        $result = [
            'limit'   => $credit ? $credit->overall_credit_limit : 0,
            'balance' => $credit ? $credit->available_credit : 0,
        ];

        return $result;
    }

    /**
     * GET PENDING ORDERS
     * ==================
     * Sums all records for this account in the session order table
     *
     * @return int|mixed
     */
    public function getPendingOrderValue()
    {
        $accountId = $this->user->account->id;

        $sessionOrders = SessionOrder::find()
            ->select(['SUM(cost * quantity) as total'])
            ->where(['account_id' => $accountId]) ;

        if (Yii::$app->session->get('internal_user')) {
            $sessionOrders->andWhere(['sales_rep_id' => Yii::$app->session->get('internal_user')]) ;
        } else {
            $sessionOrders->andWhere(['sales_rep_id' => null]) ;
        }

        $sessionOrders = $sessionOrders
            ->orderBy('product_id')
            ->one();

        return $sessionOrders && $sessionOrders->total ? $sessionOrders->total : 0;
    }

    /**
     * GET UNCLEARED ORDERS
     * ====================
     * This reads details of any purchases which haven't been detected by oracle
     * and sums their total value. The price for these is recorded in an external
     * database and it looks like Yii2 can't generate the join to this in a single
     * statement, so we need to iterate over the results and request each as needed.
     *
     * @return int
     */
    private function getUnclearedOrders()
    {
        $accountId = $this->user->account->id;

        // -------------------------------------------------------------------
        // Read the product details and counts.
        // -------------------------------------------------------------------
        $items = Orderdetails::find()
            ->select(['productcode as prodcode', 'orderdetails.stock_item_id', 'COUNT(*) as quantity', 'stock_item.*'])
            ->where(['filename' => null])
            ->joinWith('stockitem.stockroom.account')
            ->andWhere(['stockroom.account_id' => $accountId])
            ->groupBy('stock_item.productcode')
            ->all();

        // -------------------------------------------------------------------
        // Now sum the costs across all properties
        // -------------------------------------------------------------------
        $total = 0;
        foreach ($items as $item) {
            $total += $item->stockitem->itemPrice * $item->quantity;
        }

        return $total;
    }

    /**
     * CLEAR OLD ORDERED ITEMS
     * =======================
     * Called everytime the process runs, this is used to remove any items from
     * other sessions which have 'expired'.
     *
     * Expired items are those which haven't been referenced in a pre-defined
     * timespan,which guards against the credit balance being locked for extended
     * periods if no action is taken (say for two weeks when they go on holiday)
     */
    private function clearOldOrderedItems()
    {
        $sessionOrders = SessionOrder::deleteAll(
            ['and', ['<>', 'session_id', session_id()],
                ['>', 'TIMESTAMPDIFF(MINUTE, updated_at, NOW())', self::MAX_LIFE_IN_MINUTES]
            ]
        );
    }


}
