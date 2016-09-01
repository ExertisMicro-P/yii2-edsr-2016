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

<style>body {background-color:#f2f2f2;} body * {font-family: verdana;} .email-body {width: 100%;padding: 0;margin:0;text-align:center;background-color:#f2f2f2;font-family: verdana;}.email-body th {font-family: verdana;font-size: 12px;}.email-body td {font-family: verdana;font-size: 12px;}.email-body h2 {font-family: verdana;font-size: 12px;}.email-body h3 {font-family: verdana;font-size: 12px;}.email-body h4 {font-family: verdana;font-size: 12px;}.email-header {width:650px;height:132px;}.email-border {width:20px;}.email-content {font-family: verdana;padding: 20px;background-color:#ffffff;text-align: left;}.email-pre-footer {width:650px;height:18px;}.email-footer {width: 650px;height:24px;}</style>



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
                        <TR>
                            <TD>



                                <h3><?= $subject ?></h3>


                                <p><?= Yii::t("user", "Thanks for setting up your password for the your Digital Stock Room. You're nearly there!") ?></p>

                                <p><?= Yii::t("user", 'Now you need to install <a href="https://support.google.com/accounts/answer/1066447?hl=en"><strong>Google Authenticator</strong></a> on your mobile phone.') ?></p>
                                <p><?= Yii::t("user", '<a href="https://support.google.com/accounts/answer/1066447?hl=en"><strong>Google Authenticator</strong></a>
    uses your mobile phone to generate an extra password which you will need every
    time you log into your digital stockroom. <a href="https://support.google.com/accounts/answer/1066447?hl=en">
    <strong>Google Authenticator</strong></a> works with iOS, Android, and Blackberry devices<p>') ?></p>

                                <p><a href='https://support.google.com/accounts/answer/1066447?hl=en'>View Google Authenticator Installation Instructions></a></p>

                                <p><?=
                                Yii::t("user", 'Once <a href="https://support.google.com/accounts/answer/1066447?hl=en"><strong>Google Authenticator</strong></a> '
                                        . 'is installed, you need to tell it about your Digital Stock Room. Setup a new account in '
                                        . '<a href="https://support.google.com/accounts/answer/1066447?hl=en"><strong>Google '
                                        . 'Authenticator</strong></a> by using you phone\'s camera and the QR Code below')
                                ?></p>
                                <p><img src="<?= $gaDetails['qr_url'] ?>"/></p>

                                <p><?= Yii::t("user", "If you can't use the QR Code, you can also enter the following details manually:</p>") ?>
                                <p>GA Account Name : <?= $gaDetails['display_text'] ?></p>
                                <p>GA Key: <?= $gaDetails['unique_id'] ?></p>

                                <p><a href="<?= $gaDetails['ez_url'] ?>" title="Your EZ url">EZ Url </a></p>

                                <p><?= Yii::t("user", "You should see a Verification Code being generated in GA every 30 seconds. Now visit <a href='<?= Yii::$app->params['frontendBaseUrl'] ?>'><?= Yii::$app->params['frontendBaseUrl'] ?></a> to log in.") ?></p>

                                <p><?= Yii::t("user", "To find out more about the Exertis Digital Stock Room, please visit ") ?></p>
                                <a href='<?= Yii::$app->params['frontendBaseUrl'] ?>'><?= Yii::$app->params['frontendBaseUrl'] ?></a>


                            </TD>
                        </TR>


                        <TR>
                            <TD>
                                <P style="MARGIN-TOP: 25px"><B>Best regards</B><BR>Exertis<BR><BR><SPAN
                                        id=small>www.exertis.co.uk<BR><BR><BR></SPAN></P>
                                <P id=small>If you have any questions regarding this email, please
                                    contact our web team<br/> on 01256 707070 or email us at
                                    <A
                                        href="mailto:marketing@exertismicro-p.co.uk">webteam@exertis.co.uk</A></P></TD></TR></TBODY></TABLE><BR><BR><BR></TD>
            <TD style="BACKGROUND-COLOR: #f2f2f2" class=email-border
                width=20></TD></TR>
        <TR>
            <TD class=email-pre-footer colSpan=3><IMG
                    src="http://email.exertismicro-p.biz/img/pre-footer.jpg"
                    NOSEND="1"></TD></TR>
        <TR>
            <TD class=email-footer colSpan=3>
                <TABLE border=0 cellSpacing=0 cellPadding=0 width=650>
                    <TBODY>
                        <TR>
                            <TD></TD>
                            <TD align=right><IMG
                                    src="http://email.exertismicro-p.biz/img/powered-by.jpg"
                                    NOSEND="1"></TD></TD></TR></TBODY>
                </TABLE>
            </TD></TR>
    </TBODY>
</TABLE>