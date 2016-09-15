<?php
namespace common\components;

use Yii;
use common\models\Account;
use common\models\EmailedUser;
use common\models\EmailedItem;
use common\models\StockItem;
use exertis\savewithaudittrail\models\Audittrail;


/**
 * Class CreditLevel
 *
 * @package common\components
 */
class EmailKeys {
    private $user;
    private $userEmail;

    /**
     * EmailKeys constructor.
     * ======================
     * Used to set the user and user email, for use in logging,
     */
    public function __construct() {

        if (!(\Yii::$app instanceof yii\console\Application) && \Yii::$app->user) {
            $this->user      = Yii::$app->user->getIdentity();
            $this->userEmail = $this->user->email;

        } else {
            $this->user      = null;
            $this->userEmail = '<system process>';
        }
    }


    /**
     * COMPLETE ORDER
     * ==============
     * This is the controlling process when keys are initially emailed out for
     * one or more items.
     *
     *
     *
     * @param array $recipientDetails email address, name, unique ref from popup form etc
     * @param array $selectedItems    Array of Stock Item IDs to send by email
     * @param       $account          Account so we can include things like accountLogo
     *
     * @return array|StockroomController|mixed|string|static
     * @throws \Exception
     */
    public function completeEmailOrder($recipientDetails, $selectedItems, $account) {

        \Yii::info(__METHOD__ . ': $recipientDetails=' . print_r($recipientDetails, true));
        \Yii::info(__METHOD__ . ': $selectedItems=' . print_r($selectedItems, true));
        \Yii::info(__METHOD__ . ': $account=' . print_r($account, true));

        list($result, $selectedDetails) = $this->markKeysAsProcessed($recipientDetails, $selectedItems, $account);

        if ($result === true) {
            $result = $this->sendOrderEmailToCustomer($recipientDetails, $selectedDetails, $account);
        }

        return $result;
    }

    /**
     * RE-EMAIL KEYS
     * =============
     * Sends a duplicate copy of a previously sent email - but with an optional
     * message
     *
     * @param $recipientDetails
     * @param $selectedItems
     * @param $account
     * @param $stockId
     *
     * @return mixed
     */
    public function reEmailKeys($recipientDetails, $selectedItems, $account) {

        \Yii::info(__METHOD__ . ': $recipientDetails=' . print_r($recipientDetails, true));
        \Yii::info(__METHOD__ . ': $selectedItems=' . print_r($selectedItems, true));
        \Yii::info(__METHOD__ . ': $account=' . print_r($account, true));

        $result = $this->sendOrderEmailToCustomer($recipientDetails, $selectedItems, $account);

        return $result;
    }


    /**
     * SAVE EMAILED ORDER DETAILS
     * ==========================
     * This is called when keys are processed other than emailing. This
     * can be as a result of printing them or by 'delivering' them manually
     *
     * @param $recipientDetails
     * @param $selectedItems
     *
     * @return array
     */
    public function saveEmailedOrderDetails($recipientDetails, &$selectedItems, $account) {
        \Yii::info(__METHOD__ . ': $recipientDetails=' . print_r($recipientDetails, true));
        \Yii::info(__METHOD__ . ': $selectedItems=' . print_r($selectedItems, true));

        return $this->sendOrderEmailToCustomer($recipientDetails, $selectedItems, $account);
    }

    /**
     * MARK KEYS DELIVERED
     * ===================
     *
     * @param $recipientDetails
     * @param $selectedItems
     * @param $account
     *
     * @return mixed
     */
    public function markKeysDelivered($recipientDetails, $selectedItems, $account) {

        \Yii::info(__METHOD__ . ': $recipientDetails=' . print_r($recipientDetails, true));
        \Yii::info(__METHOD__ . ': $selectedItems=' . print_r($selectedItems, true));
        \Yii::info(__METHOD__ . ': $account=' . print_r($account, true));

        list($result, $selectedDetails) = $this->markKeysAsProcessed($recipientDetails, $selectedItems, $account);

        return $result;
    }

    /**
     * MARK KEYS AS PROCESSED
     * ======================
     * This handles the actual process of flagging oen or more keys as processed.
     *
     * @param $recipientDetails
     * @param $selectedItems
     * @param $account
     *
     * @return array
     * @throws \Exception
     */
    private function markKeysAsProcessed($recipientDetails, $selectedItems, $account) {
        $errors          = [];
        $connection      = EmailedUser::getDb();
        $transaction     = $connection->beginTransaction();
        $selectedDetails = [];

        try {
            $result = $this->saveEmailedRecipient($recipientDetails, $account);

            if ($result !== true) {
                $errors['user'] = $result;

                // ---------------------------------------------------------------
                // Can now record the individual stock item movements
                // ---------------------------------------------------------------
            } else {
                $newStatusCode = StockItem::STATUS_DELIVERING . $recipientDetails['orderNumber'];

                $result = $this->updateStockItems($newStatusCode, $selectedItems);

                if ($result !== true) {
                    $errors['insufficient'] = $result;

                } else {
                    if ($this->copyStockItemsToEmailedItems($newStatusCode, $recipientDetails, $selectedItems)) {
                        $selectedDetails = $this->readDescriptionAndKeys($recipientDetails);
                    } else {
                        $errors['unknown'] = 'Failed';
                    }
                }
            }
            if (count($errors)) {
                $result = $errors;
                $transaction->rollBack();

            } else {
                $transaction->commit();
                $result = true;
            }

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return array($result, $selectedDetails);
    }


    /**
     * SAVE EMAILED RECIPIENT
     * ======================
     *
     * @param $recipientDetails
     * @param $account
     *
     * @return bool
     */
    private function saveEmailedRecipient(&$recipientDetails, $account) {

        \Yii::info(__METHOD__ . ': $recipientDetails=' . print_r($recipientDetails, true));

        $result = true;

        // ---------------------------------------------------------------
        // First, try to record the main emailed_user details
        // ---------------------------------------------------------------
        $emailedRecipient               = new EmailedUser();
        $emailedRecipient->email        = $recipientDetails['email'];
        $emailedRecipient->name         = $recipientDetails['recipient'];
        $emailedRecipient->order_number = StockItem::STATUS_DELIVERING . $recipientDetails['orderNumber'];

        $emailedRecipient->account_id = $account->id;

        if ($this->user) {
            if (!$emailedRecipient->saveWithAuditTrail('Emailling ' . $emailedRecipient->email . ' with ' . $emailedRecipient->order_number)) {
                $result = $emailedRecipient->errors;
                \Yii::error(__METHOD__ . ': ' . print_r($emailedRecipient->getErrors(), true));

            } else {
                $recipientDetails['emailedUser'] = $emailedRecipient;
            }

        } elseif (!$emailedRecipient->save()) {
            $result = $emailedRecipient->errors;
            \Yii::error(__METHOD__ . ': ' . print_r($emailedRecipient->getErrors(), true));

        } else {
            $recipientDetails['emailedUser'] = $emailedRecipient;
        }

        return $result;
    }


    /**
     * UPDATE STOCK ITEMS
     * ==================
     * This attempts to flag all the requested stock items as allocated to
     * the current order
     *
     * @param $newStatusCode
     * @param $selectedItems
     *
     * @return bool
     * @throws \yii\db\Exception
     */
    private function updateStockItems($newStatusCode, $selectedItems) {
        $errors = [];
        if (!empty($selectedItems)) {
            $recordedCount = 0;

            foreach ($selectedItems as $selectedItem) {
                $stockItem = StockItem::find()
                                      ->where([
                                                  'status' => StockItem::STATUS_PURCHASED,
                                                  //                                                  'stockroom_id' => $this->stockroomId,
                                                  'id'     => $selectedItem
                                              ])->one();
                if ($stockItem) {
                    $stockItem->status = $newStatusCode;

                    if ($stockItem->saveWithAuditTrail('Recording StockItem delivery reference ' . $newStatusCode . ', Email address: ' . $this->userEmail)) {
                        $recordedCount++;
                    }
                }
            }


            // -------------------------------------------------------
            // If failed to move enough, record the available count
            // -------------------------------------------------------
            if ($recordedCount <> count($selectedItems)) {
                $errors[] = $recordedCount;
            }
        }

        return count($errors) ? $errors : true;
    }


    /**
     * COPY STOCK ITEMS TO EMAILED ITEMS
     * =================================
     * This creates an EmailedItem record for each of the requested stock items
     *
     * @param $newStatusCode
     * @param $recipientDetails
     *
     * @return bool|int
     * @throws \yii\db\Exception
     */
    private function copyStockItemsToEmailedItems($newStatusCode, $recipientDetails, $selectedItems) {
        \Yii::info(__METHOD__ . ': $newStatusCode=' . print_r($newStatusCode, true));
        \Yii::info(__METHOD__ . ': $recipientDetails=' . print_r($recipientDetails, true));
        \Yii::info(__METHOD__ . ': $selectedItems=' . print_r($selectedItems, true));

        $result = true;

        $connection = EmailedUser::getDb();

        $sqlCommand = $connection->createCommand(
            'INSERT INTO ' . EmailedItem::tableName() . '
                    (emailed_user_id, stock_item_id, created_at)
                    SELECT :emailedId, id, NOW()
                    FROM ' . StockItem::tableName() .
            ' WHERE ' .
            //' status=:status '.
            //    ' AND '.
            'id IN (' . implode(',', $selectedItems) . ')'
        )
                                 ->bindValues([':emailedId' => $recipientDetails['emailedUser']['id'],
                                               //':status'    => $newStatusCode
                                               //':selectedItems' => implode(', ', $selectedItems)
                                               //':selectedItems' => $selectedItems
                                              ]);

        $result = $sqlCommand->execute();

        $msg       = 'Stock items Emailed/Printed to (email:' . $recipientDetails['email'] . ') (Ref: ' . $recipientDetails['orderNumber'] . ')';
        $tableName = EmailedItem::tableName();
        $recordId  = $recipientDetails['emailedUser']['id'];

        $auditentry = new Audittrail();
        $auditentry->log($msg, $tableName, $recordId, $this->user);
        $auditentry->save();

        return $result > 0;;
    }

    /**
     * READ DESCRIPTION AND KEYS
     * =========================
     *
     * @param $recipientDetails
     *
     * @return array
     */
    private function readDescriptionAndKeys($recipientDetails) {
        $emailedUser     = $recipientDetails['emailedUser'];
        $selectedDetails = [];

        foreach ($emailedUser->emailedItems as $emailedItem) {
            $stockItem = $emailedItem->stockItem;

            $productKey = DigitalPurchaser::getProductInstallKey($stockItem);

            $selectedDetails['codes'][$stockItem['productcode']]['description']              = $stockItem->description;
            $selectedDetails['codes'][$stockItem['productcode']]['faqs']                     = $stockItem->digitalProduct->faqs;
            $selectedDetails['codes'][$stockItem['productcode']]['keyItems'][$stockItem->id] = $productKey;
        }

        return $selectedDetails;
    }


    /**
     * SEND ORDER EMAIL TO CUSTOMER
     * ============================
     *
     * @var Mailer                          $mailer
     * @var Message                         $message
     * @var \amnah\yii2\user\models\UserKey $userKey
     *
     * @param                               $recipientDetails
     * @param                               $selectedCounts
     * @param                               $account Account so we can include things like accountLogo
     *
     * @return mixed
     */
    private function sendOrderEmailToCustomer($recipientDetails, $selectedDetails, $account) {

        \Yii::info(__METHOD__ . ': $recipientDetails=' . print_r($recipientDetails, true));
        \Yii::info(__METHOD__ . ': $selectedDetails=' . print_r($selectedDetails, true));
        \Yii::info(__METHOD__ . ': $account=' . print_r($account, true));

        $mailer           = Yii::$app->mailer;
        $oldViewPath      = $mailer->viewPath;
        $mailer->viewPath = '@common/mail';

        $subject = 'Exertis Digital Stock Room: ' . Yii::t("user", "Order Details for " . $recipientDetails['orderNumber']); // RCH orderNumber is actually just an arbitrary ref entered by the user

        //Check if account has a logo
        if (!$account->logo) {

            // RCH 20160425
            // We can't fail it here! it will prevent the email being sent and as this is called via AJAX
            // it'll fail silently, leaving a mess.
            // Consider generating another email to the user to ask them to set their logo up.
            //
            //
            //Yii::$app->getSession()->setFlash('warning', 'Please set a logo for your account.');
            //$this->redirect('/settings');

            //return;
        }

        \Yii::info('ABOUT TO EMAIL');
        \Yii::info($recipientDetails['recipient']);
        \Yii::info(print_r(Yii::$app->params['account.copyAllEmailsTo'], true));

        $message = $mailer->compose('stockroom/orderedetails',
                                    compact("subject", "recipientDetails", "selectedDetails", "account"))
                          ->setTo([$recipientDetails['email'] => $recipientDetails['recipient']])
                          ->setBcc(Yii::$app->params['account.copyAllEmailsTo'])// RCH 20150420
                          ->setSubject($subject) ;


        $this->attachKeyPdfs($message, $selectedDetails) ;

        // check for messageConfig before sending (for backwards-compatible purposes)
        if (empty($mailer->messageConfig["from"])) {
            $message->setFrom(Yii::$app->params["adminEmail"]);
        }
        $result = $message->send();


        // restore view path and return result
        $mailer->viewPath = $oldViewPath;

        return $result;
    }

    private function attachKeyPdfs($message, $selectedDetails) {
        $filename = $this->produceKeyPdf($selectedDetails) ;

        $message->attach($filename);

//        unlink($filename) ;

    }

    private function produceKeyPdf($selectedDetails) {

        $filename = tempnam(Yii::getAlias('@frontend') . '/runtime/tmp', 'key') ;

        $stockCodes = [] ;
        foreach ($selectedDetails as $selectedItem) {
            foreach ($selectedItem as $code => $details) {
                foreach ($details['keyItems'] as $stockCode => $key) {
                    $stockCodes[] = $stockCode ;
                }
            }
        }

        $keyPrinter = new printKeys() ;
        $keyPrinter->printKeys($stockCodes, $filename) ;

        return $filename ;
    }

}
