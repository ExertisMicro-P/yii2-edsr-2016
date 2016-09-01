<?php

use yii\helpers\Url;
/**
 * @var string $subject
 * @var \amnah\yii2\user\models\User $user
 * @var \amnah\yii2\user\models\Profile $profile
 * @var \amnah\yii2\user\models\UserKey $userKey
 * @var gaDetails
 */
use yii\helpers\Html;
?>

<style>body {background-color:#f2f2f2;} body * {font-family: verdana;} .email-body {width: 100%;padding: 0;margin:0;text-align:center;background-color:#f2f2f2;font-family: verdana;}.email-body th {font-family: verdana;font-size: 12px;}.email-body td {font-family: verdana;font-size: 12px;}.email-body h2 {font-family: verdana;font-size: 12px;}.email-body h3 {font-family: verdana;font-size: 12px;}.email-body h4 {font-family: verdana;font-size: 12px;}.email-header {width:650px;height:132px;}.email-border {width:20px;}.email-content {font-family: verdana;padding: 20px;background-color:#ffffff;text-align: left;}.email-pre-footer {width:650px;height:18px;}.email-footer {width: 650px;height:24px;}.pkey {background-color: #eeeeee; padding: 2px;}
</style>



<TABLE border=0 cellSpacing=0 cellPadding=0 width=650>
    <TBODY>
    <TR>
        <TD class=email-header colSpan=3><IMG
                src="http://email.exertismicro-p.biz/img/simple-header-white.jpg"
                NOSEND="1"></TD></TR>
    <TR>
        <TD class=email-border width=20></TD>
        <TD class=email-content width=610><BR><BR>
            <TABLE border=0 cellSpacing=0 cellPadding=0>
                <TBODY>

                <?php
                if ($recipientDetails['message']) {     ?>

                    <TR>
                        <TD COLSPAN="2"><?= $recipientDetails['message'] ?></TD>
                    </TR>

                    <?php
                }
                ?>



                <TR>
                    <TD COLSPAN="4">
                        <h3><?= $subject ?></h3>


                        <p><?= Yii::t("user", "Please find your Product License Keys below") ?></p>

                    </TD>
                </TR>

                <TR>
                    <TH>Product&nbsp;Code&nbsp;&nbsp;</TH><TH>Description</TH>
                </TR>
                
    <?php foreach ($selectedDetails['codes'] as $productCode => $details) { ?>
                <TR>
                    <TD><?= $productCode ?></TD><TD><?= $details['description'] ?></TD>
                </TR>

        <?php foreach ($details['keyItems'] as $productKey) {
            \Yii::info('Emailing license key ending in "'.substr($productKey, -5).'" to '.$recipientDetails['email']);
            ?>
                <TR>
                    <TD>Product Key:&nbsp;</TD>
                    <TD class="pkey"><?= $productKey ?></TD>
                </TR>

        <?php } ?>
                <?php 
                if (isset($details['downloadUrl'])) {
                    foreach($details['downloadUrl'] as $download){ ?>
                    <TR>
                        <TD>Download URL:</TD>
                        <TD><?=$download?></TD>
                    </TR>
                <?php } 
                } // if
                ?>
                <TR>
                    <TD colspan="2"><?= $details['faqs'] ?></TD>
                </TR>

    <?php } ?>
                </TBODY>
            </TABLE>
        </TD></TR>
    </TBODY>
</TABLE>
