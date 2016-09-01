<?php

use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Account;

/**
 * @var string $subject
 * @var \amnah\yii2\user\models\User $user
 * @var \amnah\yii2\user\models\Profile $profile
 * @var \amnah\yii2\user\models\UserKey $userKey
 *
 * data =  pos => [po =>[ orderdetails[ ]product[] item[]]]
 *
 */
?>

<style>body {background-color:#f2f2f2;} body * {font-family: verdana;font-size: 8pt;} .email-body {width: 100%;padding: 0;margin:0;text-align:center;background-color:#f2f2f2;font-family: verdana;}.email-body th {font-family: verdana;font-size: 8pt;}.email-body td {font-family: verdana;font-size: 8pt;}.email-body h2 {font-family: verdana;font-size: 8pt;}.email-body h3 {font-family: verdana;font-size: 8pt;}.email-body h4 {font-family: verdana;font-size: 8pt;}.email-header {width:650px;height:132px;}.email-border {width:20px;}.email-content {font-family: verdana;padding: 20px;background-color:#ffffff;text-align: left;}.email-pre-footer {width:650px;height:18pt;}.email-footer {width: 650px;height:24px;}
.stockitem {border:1px dotted black; padding: 10px; margin-top:10px;} .boxshot {width:100px;}
.stockitem tbody tr td {border-bottom: 1px solid lightgray; padding-bottom: 10px; padding-top: 10px;}
.accountlogo {width:100px; padding:20px;}
.vendor-logo {height:60px;}
</style>



<TABLE border=0 cellSpacing=0 cellPadding=0 width=650>
    <TBODY>
        <TR>
            <TD class="email-header" colSpan=3><IMG
                    src="http://email.exertismicro-p.biz/img/simple-header-white.jpg"
                    NOSEND="1"></TD>
        </TR>
        <tr>
            <?php
                if (!empty($account->accountLogo)) {
                    echo '<td>'.Html::img($account->accountLogo, ['style'=>'width:100px; padding:20px;']).'</td>';
                }
            ?>
            <td colSpan=2>

                <h3><?= $account->customer_exertis_account_number ?></h3>
                <p><?= $account->findMainUser()->email ?></p>

                <p><?=count($dataitem)?> New keys are now available in your <?= Html::a('Exertis Digital Stock Room', 'https://stockroom.exertis.co.uk') ?>.</p>
                <p>Please visit <?= Html::a('our Help Page', 'https://stockroom.exertis.co.uk/site/help') ?> for FAQs, Support Contacts and Download Links</p>
            </td>
        </tr>
        <TR>
            <!--<TD class="email-border" width=20></TD>-->
            <TD class="email-content" width=810 colspan="3">

                  <?php
                                    foreach($data['pos'] as $key=>$po){
                                        foreach($po as $dataitem){
//print_r($dataitem,true);
                                    ?>

                <TABLE border=0 cellSpacing=0 cellPadding=0 class="stockitem">
                    <thead>
                        <TR>
                            <th></th>
                            <th>Your PO</th>
                            <th>Our Order</th>
                            <th>Our Partcode</th>
                        </TR>
                    </thead>
                    <TBODY>


                        <TR>
                            <td rowspan="4">
                                <?php
                                /** @var StockItem $stockItem */
                                $stockItem = $dataitem['item'];
                                if ($stockItem && $stockItem->getImageUrl() ) {
                                    echo Html::img($stockItem->getImageUrl()->image_url, ['class'=>'boxshot']);
                                    }
                                ?>
                            </td>
                            <td><?= $key ?></td>
                            <td><?= $dataitem['orderdetails']->sop.'-'.$stockItem->id.'-'.$stockItem->eztorm_order_id; ?></td>
                            <td><?= $dataitem['product']->partcode; ?></td>
                        </TR>

                        <TR>
                            <td colspan="3"><?= $dataitem['product']->description; ?></td>
                        </TR>
                        <TR>
                            <td colspan="3"><?= $dataitem['orderdetails']->name; ?></td>
                        </TR>

                         <?php if (isset($data['showkeys']) && $data['showkeys']) {
                         echo '<TR>';
                            echo '<td colspan="3">';
                            $stockItem = $dataitem['item'];
                            echo common\components\DigitalPurchaser::getProductInstallKey($stockItem);
                            echo '</td>';
                         echo '</TR>';
                         } ?>

                </TABLE>
                 <?php  } // foreach pos
                                          } // foreach po
                                ?>
            </TD></TR>
                <tr>
                    <?= Html::img(Yii::$app->params['frontendBaseUrl'].'img/microsoft-partner.png', ['class'=>'vendor-logo']); ?>
                <td>
            </td></tr>
    </TBODY>
</TABLE>







