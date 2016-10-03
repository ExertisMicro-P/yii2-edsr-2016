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
     * @param      $recipientDetails
     * @param      $selectedItems
     * @param      $account
     * @param null $brand
     * @param bool $processIfAlreadySent
     *
     * @return bool|mixed
     */
    public function completeEmailOrder($recipientDetails, $selectedItems, $account, $brand = null, $processIfAlreadySent = false) {

        \Yii::info(__METHOD__ . ': $recipientDetails=' . print_r($recipientDetails, true));
        \Yii::info(__METHOD__ . ': $selectedItems=' . print_r($selectedItems, true));
        \Yii::info(__METHOD__ . ': $account=' . print_r($account->attributes, true));

        $result = $this->markKeysAsProcessed($recipientDetails, $selectedItems);
        // -------------------------------------------------------------------
        // If no errors, of we're from the drop ship handler, which sends emails
        // to previously purchased/emailed items
        // -------------------------------------------------------------------
        if ($processIfAlreadySent || $result === true) {
            $this->logToEmailTable($recipientDetails, $selectedItems, null, $account) ;

            $selectedDetails = $this->readDescriptionAndKeys($recipientDetails);
            $result          = $this->sendOrderEmailToCustomer($recipientDetails, $selectedDetails, $account, $brand);
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
     * @param $selectedDetails
     * @param $account
     * @param $stockId
     *
     * @return mixed
     */
    public function reEmailKeys($recipientDetails, $selectedDetails, $account, $brand = null) {

        \Yii::info(__METHOD__ . ': $recipientDetails=' . print_r($recipientDetails, true));
        \Yii::info(__METHOD__ . ': selectedDetails=' . print_r($selectedDetails, true));
        \Yii::info(__METHOD__ . ': $account=' . print_r($account->attributes, true));

        $this->logToEmailTable($recipientDetails, null, $selectedDetails, $account) ;

        $result = $this->sendOrderEmailToCustomer($recipientDetails, $selectedDetails, $account, $brand);

        return $result;
    }

    /**
     * FIND STOCK IDS
     * ==============
     * Loops through the full stock details to build an array of hte stock ids.
     *
     * @param $selectedDetails
     *
     * @return array
     */
    private function findStockIds($selectedDetails) {
        $codes = [] ;
        $results = [];

        foreach ($selectedDetails['codes'] as $productcode => $details) {

            foreach ($details['keyItems'] as $stockItemId => $key) {
                $codes[] = $stockItemId ;
            }
        }
        return $codes ;
    }

    /**
     * LOG TO EMAIL TABLE
     * ==================
     * Creates entries in the emailed_used and emailed_items tables for the current
     * request. Some requests pass all the stock item ids in the selectedItems
     * array, whereas other provide full stock details. In the latter case, we
     * call fndStockId to get the ids to record.
     *
     * @param $recipientDetails
     * @param $selectedItems
     * @param $selectedDetails
     * @param $account
     */
    private function logToEmailTable(&$recipientDetails, $selectedItems, $selectedDetails, $account) {
        if (!$selectedItems && $selectedDetails) {
            $selectedItems = $this->findStockIds($selectedDetails);
        }

        $result = $this->saveEmailedRecipient($recipientDetails, $account);
        $this->copyStockItemsToEmailedItems($recipientDetails, $selectedItems);
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

        $result = $this->markKeysAsProcessed($recipientDetails, $selectedItems);
        if ($result) {
            $this->logToEmailTable($recipientDetails, $selectedItems, null, $account) ;
        }

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
     * @return bool
     * @throws \Exception
     */
    private function markKeysAsProcessed($recipientDetails, $selectedItems) {
        $errors        = [];
        $newStatusCode = StockItem::STATUS_DELIVERING . $recipientDetails['orderNumber'];
        $connection    = EmailedUser::getDb();
        $transaction   = $connection->beginTransaction();

        try {
            $result = $this->updateStockItems($newStatusCode, $selectedItems);

            if ($result !== true) {
                $errors['insufficient'] = $result;

            } else {
                $transaction->commit() ;
            }

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

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
//    private function oldmarkKeysAsProcessed($recipientDetails, $selectedItems, $account) {
//        $errors          = [];
//        $connection      = EmailedUser::getDb();
//        $selectedDetails = [];
//        $result          = false;
//
//        $transaction = $connection->beginTransaction();
//        try {
//            $result = $this->saveEmailedRecipient($recipientDetails, $account);
//
//            if ($result !== true) {
//                $errors['user'] = $result;
//
//                // ---------------------------------------------------------------
//                // Can now record the individual stock item movements
//                // ---------------------------------------------------------------
//            } else {
//                $newStatusCode = StockItem::STATUS_DELIVERING . $recipientDetails['orderNumber'];
//
//                $result = $this->updateStockItems($newStatusCode, $selectedItems);
//
//                if ($result !== true) {
//                    $errors['insufficient'] = $result;
//
//                } else {
//                    if ($this->copyStockItemsToEmailedItems($newStatusCode, $recipientDetails, $selectedItems)) {
//                        $selectedDetails = $this->readDescriptionAndKeys($recipientDetails);
//                    } else {
//                        $errors['unknown'] = 'Failed';
//                    }
//                }
//            }
//            if (count($errors)) {
//                $result = $errors;
//                $transaction->rollBack();
//
//            } else {
//                $transaction->commit();
//                $result = true;
//            }
//
//        } catch (\Exception $e) {
//            $transaction->rollBack();
//            throw $e;
//        }
//
//        return array($result, $selectedDetails);
//    }


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
            if (!$emailedRecipient->saveWithAuditTrail('Emailing ' . $emailedRecipient->email . ' with ' . $emailedRecipient->order_number)) {
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
     * @param $recipientDetails
     * @param $selectedItems
     *
     * @return bool|int
     * @throws \yii\db\Exception
     */
    private function copyStockItemsToEmailedItems($recipientDetails, $selectedItems) {

        \Yii::info(__METHOD__ . ': $recipientDetails=' . print_r($recipientDetails, true));
        \Yii::info(__METHOD__ . ': $selectedItems=' . print_r($selectedItems, true));

        $result = true;

        $connection = EmailedUser::getDb();

        $sqlCommand = $connection->createCommand(
            'INSERT INTO ' . EmailedItem::tableName() . '
                    (emailed_user_id, stock_item_id, created_at)
                        SELECT :emailedId, id, NOW()
                            FROM ' . StockItem::tableName() . '
                            WHERE id IN(' . implode(', ', $selectedItems) . ')'
        )
                                 ->bindValues([':emailedId' => $recipientDetails['emailedUser']['id'],
                                              ]);

        $result = $sqlCommand->execute();

        $msg       = 'Stock items Emailed / Printed to(email:' . $recipientDetails['email'] . ') (Ref: ' . $recipientDetails['orderNumber'] . ')';
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
     * @param                               $unSortedSelectedDetails
     * @param                               $account Account so we can include things like accountLogo
     * @param                               $brand   Brand name (e.g., littlewoods, very) to allow customisation
     *
     * @return mixed
     */
    private function sendOrderEmailToCustomer($recipientDetails, $unSortedSelectedDetails, $account, $brand) {

        $sortedSelectedDetails = $this->groupItemByPublisher($unSortedSelectedDetails);


        $mailer           = Yii::$app->mailer;
        $oldViewPath      = $mailer->viewPath;
        $mailer->viewPath = '@common/mail';

        foreach ($sortedSelectedDetails as $publisher => $selectedDetails) {
            $templateName = $this->findEmailTemplate($mailer, $account, $publisher, $brand);

            list($individualEmails, $includePdfs) = $this->checkEmailOptions($templateName);

            //Check if account has a logo
            if (!$account->logo) {

                // RCH 20160425
                // We can't fail it here!it will prevent the email being sent and as this is called via AJAX
                // it'll fail silently, leaving a mess.
                // Consider generating another email to the user to ask them to set their logo up.
                //
                //
                //Yii::$app->getSession()->setFlash('warning', 'Please set a logo for your account.');
                //$this->redirect('/settings');

                //return;
            }


            if ($individualEmails) {
                $result = $this->sendEmailPerItem($mailer, $includePdfs, $templateName, $recipientDetails, $selectedDetails, $account);

            } else {
                $result = $this->sendEmail($mailer, $includePdfs, $templateName, $recipientDetails, $selectedDetails, $account);
            }

        }
// restore view path and return result
        $mailer->viewPath = $oldViewPath;

        return $result;
    }

    /**
     * GROUP ITEM BY PUBLISHER
     * =======================
     * This is called to sort the selected items into groups based on their
     * publisher. The original input is already sorted by product code, so this
     * also maintains that sorting.
     *
     * This is almost certainly doing more database work than necessary, as I
     * think the details for each code will be identical, so the inner loop
     * is redundant.
     *
     * @param $selectedDetails
     *
     * @return array
     */
    private function groupItemByPublisher($selectedDetails) {

        $results = [];

        foreach ($selectedDetails['codes'] as $productcode => $details) {

            foreach ($details['keyItems'] as $stockItemId => $key) {
                $publisher = '';
                $stockItem = StockItem::find()
                                      ->where(['id' => $stockItemId])
                                      ->one();
                if ($stockItem) {
                    $publisher = $stockItem->publisher;
                }
                if (!array_key_exists($publisher, $results)) {
                    $results[$publisher] = ['codes' => []];
                }
                if (!array_key_exists($productcode, $results[$publisher]['codes'])) {
                    $results[$publisher]['codes'][$productcode] = [
                        'description' => $details['description'],
                        'faqs'        => $details['faqs'],
                        'downloadUrl' => $details['downloadUrl'],
                        'keyItems'    => []
                    ];
                }
                $results[$publisher]['codes'][$productcode]['keyItems'][$stockItemId] = $key;
            }
        }

        return $results;
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
     * @param $templateName
     * @param $recipientDetails
     * @param $selectedDetails
     * @param $account
     *
     * @return bool
     */
    private function sendEmailPerItem($mailer, $includePdfs, $templateName, $recipientDetails, $selectedDetails, $account) {
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

                if (!$this->sendEmail($mailer, $includePdfs, $templateName, $recipientDetails, $emailData, $account)) {
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
     * @param $templateName
     * @param $recipientDetails
     * @param $emailData
     * @param $account
     *
     * @return mixed
     */
    private function sendEmail($mailer, $includePdfs, $templateName, $recipientDetails, $emailData, $account) {

        // ---------------------------------------------------------------
        // RCH orderNumber is actually just an arbitrary ref entered by the user
        // ---------------------------------------------------------------
        $orderNumber = $recipientDetails['orderNumber'] ;
        $subject= '' ;
        $bccAddress = null ;

        if ($account){
            if ($account->drop_ship_subject) {
                $subject = $account->drop_ship_subject ;
            }
            if ($account->drop_ship_bcc) {
                $bccAddress = $account->drop_ship_bcc;
            }
        }

        // ---------------------------------------------------------------
        // RCH orderNumber is actually just an arbitrary ref entered by the user
        // ---------------------------------------------------------------
        if (!$subject) {
            $subject = 'Exertis Digital Stock Room: ' . Yii::t("user", "Order Details for {{ORDER_NUMBER}}");
        }

        $subject = str_replace("{{ORDER_NUMBER}}", $orderNumber, $subject) ;



        $message = $mailer->compose($templateName . '.php',
                                    compact("subject", "recipientDetails", "emailData", "account"))
                          ->setTo([$recipientDetails['email'] => $recipientDetails['recipient']])
                          ->setBcc(Yii::$app->params['account.copyAllEmailsTo'])// RCH 20150420
                          ->setSubject($subject);
        if ($includePdfs) {
            $filename = $this->createKeyPdfs($emailData, $account);
            $message->attach($filename);
        }

        if ($bccAddress) {
            $message->setBcc($bccAddress) ;
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
     * @param $publisher
     * @param $brand
     *
     * @return string
     */
    private function findEmailTemplate($mailer, $account, $publisher, $brand) {

        // -------------------------------------------------------------------
        // The following are the valid name combinations, and in the order
        // they are searched for.
        // -------------------------------------------------------------------
        // NOTE : All the names will be prefixed by email and suffixed .php
        // -------------------------------------------------------------------
        $possibleNames = [
            '_' . $publisher . '_' . $brand,
            '_' . $publisher,
            '_' . $brand,
            ''
        ];

        // -------------------------------------------------------------------
        // Find the basepath to all the templates.
        // -------------------------------------------------------------------
        $basePath = Yii::getAlias($mailer->viewPath);
        if (substr($basePath, -1, 1) <> '/') {
            $basePath .= '/';
        }

        // -------------------------------------------------------------------
        // All account specific templates must reside in an account directory
        // -------------------------------------------------------------------
        $accountPath   = 'stockroom/' . $account->customer_exertis_account_number . '/';
        $fullDirectory = $basePath . $accountPath;
        $template      = '';

        if (file_exists($fullDirectory) && is_dir($fullDirectory)) {
            $template = $this->scanDirectoryForTemplate($fullDirectory, $possibleNames);
        }

        if (!$template) {
            $accountPath   = 'stockroom/';
            $fullDirectory = $basePath . $accountPath . '/';
            $template      = $this->scanDirectoryForTemplate($fullDirectory, $possibleNames);
        }

        if (!$template) {
            $template = 'orderedetails.pdfs';
        }

        return $accountPath . $template;
    }

    /**
     * SCAN DIRECTORY FOR TEMPLATE
     * ===========================
     * Searches all files in the passed directory for any one of the possible
     * valid template names, as passed in.
     *
     * @param $fullDirectory
     * @param $possibleNames
     *
     * @return bool|string
     */
    private function scanDirectoryForTemplate($fullDirectory, $possibleNames) {

        $matches = [];
        $dir     = new \DirectoryIterator($fullDirectory);

        foreach ($possibleNames as $possibleName) {
            $fileNameMask = "/^email$possibleName(.*?).php/";
            $iterator     = new \RegexIterator($dir, $fileNameMask, \RegexIterator::MATCH);

            // -------------------------------------------------------------------
            // If we found a match, return just name, minus the extension
            // -------------------------------------------------------------------
            foreach ($iterator as $fileinfo) {
                if (!$fileinfo->isDot()) {
                    return $fileinfo->getBasename('.php');
                }
            }
        }

        return false;
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
