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
    private $workDir;

    /**
     * EmailKeys constructor.
     * ======================
     * Used to set the user and user email, for use in logging,
     */
    public function __construct() {

        if (!(\Yii::$app instanceof yii\console\Application) && \Yii::$app->user) {
            $this->user      = Yii::$app->user->getIdentity();
            $this->userEmail = $this->user->email;
            $this->workDir   = Yii::getAlias('@frontend') . '/runtime/tmp';

        } else {
            $this->user      = null;
            $this->userEmail = '<system process>';
            $this->workDir   = Yii::getAlias('@console') . '/runtime/tmp';
        }
        if (!is_dir($this->workDir)) {
            mkdir($this->workDir, 0600, true);
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
    public function completeEmailOrder($recipientDetails, $selectedItems, $account, $brand = null) {

        \Yii::info(__METHOD__ . ': $recipientDetails=' . print_r($recipientDetails, true));
        \Yii::info(__METHOD__ . ': $selectedItems=' . print_r($selectedItems, true));
        \Yii::info(__METHOD__ . ': $account=' . print_r($account->attributes, true));

        list($result, $selectedDetails) = $this->markKeysAsProcessed($recipientDetails, $selectedItems, $account);

        if ($result === true) {
            $result = $this->sendOrderEmailToCustomer($recipientDetails, $selectedDetails, $account, $brand);
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
    public function reEmailKeys($recipientDetails, $selectedItems, $account, $brand = null) {

        \Yii::info(__METHOD__ . ': $recipientDetails=' . print_r($recipientDetails, true));
        \Yii::info(__METHOD__ . ': $selectedItems=' . print_r($selectedItems, true));
        \Yii::info(__METHOD__ . ': $account=' . print_r($account->attributes, true));

        $result = $this->sendOrderEmailToCustomer($recipientDetails, $selectedItems, $account, $brand);

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
    public function saveEmailedOrderDetails($recipientDetails, &$selectedItems, $account, $brand) {
        \Yii::info(__METHOD__ . ': $recipientDetails=' . print_r($recipientDetails, true));
        \Yii::info(__METHOD__ . ': $selectedItems=' . print_r($selectedItems, true));

        return $this->sendOrderEmailToCustomer($recipientDetails, $selectedItems, $account, $brand);
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
        \Yii::info(__METHOD__ . ': $account=' . print_r($account->attributes, true));

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
            $selectedDetails['codes'][$stockItem['productcode']]['downloadUrl']              = $stockItem->getDownloadURL();
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
     * @param                               $brand   Brand name (e.g., littlewoods, very) to allow customisation
     *
     * @return mixed
     */
    private function sendOrderEmailToCustomer($recipientDetails, $selectedDetails, $account, $brand) {

        $mailer           = Yii::$app->mailer;
        $oldViewPath      = $mailer->viewPath;
        $mailer->viewPath = '@common/mail';
        $templateName     = $this->findEmailTemplate($mailer, $account, $brand);

        list($individualEmails, $includePdfs) = $this->checkEmailOptions($templateName);

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


        if ($individualEmails) {
            $result = $this->sendEmailPerItem($mailer, $includePdfs, $subject, $templateName, $recipientDetails, $selectedDetails, $account);

        } else {
            $result = $this->sendEmail($mailer, $includePdfs, $subject, $templateName, $recipientDetails, $selectedDetails, $account);
        }


        // restore view path and return result
        $mailer->viewPath = $oldViewPath;

        return $result;
    }

    /**
     * CHECK EMAIL OPTIONS
     * ===================
     * Examines the template name for a suffix to indicate if it only accepts
     * a single item from the order, an whether or not the pdf version should be
     * appended.
     *
     * @param $templateName
     *
     * @return array
     */
    private function checkEmailOptions($templateName) {
        $individualItems = false;
        $includePdfs     = false;

        $options = strrchr($templateName, '.');
        if ($options) {
            $options = substr($options, 1);
            echo "\nOPTIONS $options\n";

            if (strpos($options, ':')) {
                $options = explode(':', $options);
            } else {
                $options = [$options];
            }
            $individualItems = in_array('one', $options);
            $includePdfs     = in_array('pdfs', $options);
        }

        return [$individualItems, $includePdfs];

    }

    /**
     * SEND EMAIL PER ITEM
     * ===================
     * This is used where an individual email is to be sent for each item on a
     * purchase order. It iterates over the actual contents, creating the data
     * structure required by the standard email send mthod
     *
     * @param $mailer
     * @param $includePdfs
     * @param $subject
     * @param $templateName
     * @param $recipientDetails
     * @param $selectedDetails
     * @param $account
     *
     * @return bool
     */
    private function sendEmailPerItem($mailer, $includePdfs, $subject, $templateName, $recipientDetails, $selectedDetails, $account) {
        $result = true;

        foreach ($selectedDetails['codes'] as $productCode => $details) {
            // ---------------------------------------------------------------
            // First, copy the basic details related to this product
            // ---------------------------------------------------------------
            $emailData = [
                'codes' => [
                    $productCode => [
                        'description' => $details['description'],
                        'downloadUrl' => isset($details['downloadUrl']) ? $details['downloadUrl'][0] : null,
                        'keyItems'    => []
                    ]
                ]
            ];
            // ---------------------------------------------------------------
            // Mow append each key in turn, then send an email
            // ---------------------------------------------------------------
            foreach ($details['keyItems'] as $keyId => $productKey) {
                $emailData['codes'][$productCode]['keyItems'][$keyId] = $productKey;

                if (!$this->sendEmail($mailer, $includePdfs, $subject, $templateName, $recipientDetails, $emailData, $account)) {
                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * SEND EMAIL
     * ==========
     * This will build and send teh key details email using the provided data.
     * This must be in the format
     *  $emailData [
     *      'codes'     [
     *          'description'   => ''
     *          'downloadUrl    => [url url, url]               Optional
     *          'keyItems'      => [key, key, key]
     *          'faqs'          => ''
     *      ]
     * ]
     *
     * If includePdfs is true, a pdf version of the key will be created and
     * attached to the email
     *
     * @param $mailer
     * @param $subject
     * @param $templateName
     * @param $recipientDetails
     * @param $emailData
     * @param $account
     *
     * @return mixed
     */
    private function sendEmail($mailer, $includePdfs, $subject, $templateName, $recipientDetails, $emailData, $account) {

        echo "\nEMAILDATA  $includePdfs \n";
        print_r($emailData);

        $message = $mailer->compose($templateName . '.php',
                                    compact("subject", "recipientDetails", "emailData", "account"))
                          ->setTo([$recipientDetails['email'] => $recipientDetails['recipient']])
                          ->setBcc(Yii::$app->params['account.copyAllEmailsTo'])// RCH 20150420
                          ->setSubject($subject);
        if ($includePdfs) {
            $filename = $this->createKeyPdfs($emailData, $account);
            $message->attach($filename);
        }


        // check for messageConfig before sending (for backwards-compatible purposes)
        if (empty($mailer->messageConfig["from"])) {
            $message->setFrom(Yii::$app->params["adminEmail"]);
        }
        $result = $message->send();

        if ($includePdfs) {
            unlink($filename);
        }

        return $result;
    }

    /**
     * FIND EMAIL TEMPLATE
     * ===================
     * This checks for account and brand specific email templates in the
     * mailer viewpath, returning the name of the most specific found.
     *
     * @param $mailer
     * @param $account
     * @param $brand
     *
     * @return string
     */
    private function findEmailTemplate($mailer, $account, $brand) {
//        return 'stockroom/orderedetails.pdfs';

        $basePath = Yii::getAlias($mailer->viewPath);
        if (substr($basePath, -1, 1) <> '/') {
            $basePath .= '/';
        }

        $possibleName = 'stockroom/email_' . $account->customer_exertis_account_number;
        $matches      = [];

        // -------------------------------------------------------------------
        // Check first for any variation of a file with the brand
        // -------------------------------------------------------------------
        if ($brand) {
            $filenameMask = $basePath . $possibleName . '_' . $brand . '\.*.php';
            $matches      = glob($filenameMask);
        }

        // -------------------------------------------------------------------
        // If no match, check for the account number followed by options
        // -------------------------------------------------------------------
        if (!$matches || count($matches) == 0) {
            $filenameMask = $basePath . $possibleName . '\.*.php';
            $matches      = glob($filenameMask);
        }

        // -------------------------------------------------------------------
        // If still no match, check for just the account number
        // -------------------------------------------------------------------
        if (!$matches || count($matches) == 0) {
            $filenameMask = $basePath . $possibleName . '.php';
            $matches      = glob($filenameMask);
        }

        // -------------------------------------------------------------------
        // If we found a match, return it after stripping the full path and
        //teh php extension (and then pre-pending 'stockroom/')
        // -------------------------------------------------------------------
        if ($matches && count($matches)) {
            return 'stockroom/' . basename($matches[0], '.php');
        }

        // -------------------------------------------------------------------
        // Neither found, so return the default
        // -------------------------------------------------------------------
        return 'stockroom/orderedetails.pdfs';
    }


    /**
     * CREATE KEY PDF
     * ==============
     * Allocates the file to hold the pdf, then creates the contents,
     * finally returning the name
     *
     * @param $selectedDetails
     * @param $account
     *
     * @return string
     */
    private function createKeyPdfs($selectedDetails, $account) {

        $filename = tempnam($this->workDir, 'key');

        rename($filename, $filename . '.pdf');
        $filename .= '.pdf';

        $this->produceKeyPdf($selectedDetails, $account, $filename);

        return $filename;
    }

    /**
     * PRODUCE KEY PDF
     * ===============
     * Actually builds the pdf for all selected keys
     *
     * @param $selectedDetails
     * @param $account
     * @param $filename
     */
    private function produceKeyPdf($selectedDetails, $account, $filename) {

        $stockCodes = [];
        foreach ($selectedDetails['codes'] as $code => $details) {
            foreach ($details['keyItems'] as $stockCode => $key) {
                $stockCodes[] = $stockCode;
            }
        }

        $keyPrinter = new printKeys($account);
        $keyPrinter->printKeys($stockCodes, $filename);
    }

}
