<?php

$orderNumber    = $recipientDetails['orderNumber'];
$productDetails = reset($emailData['codes']);
$productKey     = reset($productDetails['keyItems']);

$downloadUrl = array_key_exists('downloadUrl', $productDetails) ? $productDetails['downloadUrl'] : null ;
if (is_array($downloadUrl)) {
    $downloadUrl = $downloadUrl[0];
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
      xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <!--[if !mso]><!-- -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <!--<![endif]-->
    <meta name="HandheldFriendly" content="True"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="format-detection" content="telephone=no">
    <title>ao.com</title>
    <style>
        .ReadMsgBody {
            width: 100%;
        }

        .ExternalClass {
            width: 100%;
            line-height: 100%;
        }

        body {
            width: 100% !important;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
            padding: 0;
            margin: 0 !important;
        }

        div[style*="margin: 16px 0"] {
            margin: 0 !important;
            font-size: 100% !important;
        }

        #outlook a {
            padding: 0;
        }

        @media screen and (min-width: 640px) {
            .Width640 {
                width: 640px !important;
            }

            .Width638 {
                width: 638px !important;
            }

            .deviceWidth {
                width: 319px !important;
            }
        }

        @media screen and (max-width: 640px) {
            .deviceWidth {
                width: 50% !important;
            }

            .deviceWidth2 {
                width: 95% !important;
            }

            .imgWidth {
                width: 100% !important;
                height: auto !important;
            }
        }

        @media only screen and (max-width: 480px) {
            .hideContent {
                display: none !important;
            }

            .deviceWidth {
                width: 100% !important;
                max-width: 100% !important;
            }

            .imgWidth {
                width: 100% !important;
                height: auto !important;
            }

            .center {
                text-align: center !important;
                margin: 0px auto !important;
                float: none !important;
            }

            .fontSize {
                font-size: 18px !important;
            }

            /* Mobile Nav */
            .mobNavShow {
                width: 100% !important;
                height: auto !important;
                display: table !important;
                background-color: #F3F3F3;
                padding-bottom: 10px;
            }

            .mobNavShow2 {
                display: block !important;
                width: 100% !important;
                max-height: inherit !important;
                overflow: visible !important;
            }

            .mobNavShow3 {
                display: block !important;
                width: 100% !important;
                height: auto !important;
                text-align: center !important;
                float: none !important;
            }

            .mobNavShow4 {
                width: 100% !important;
                height: auto !important;
                display: table !important;
            }

            .mobNavButtn:hover {
                background: #484442 !important;
                transition: 0.3s !important;
            }

            .mobNavButtn a:hover {
                color: #FFFFFF !important;
                transition: 0.3s !important;
            }
        }

        @media screen and (max-width: 300px) {
            .hidePad {
                padding: 0px !important;
            }
        }
    </style>
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml><![endif]-->
</head>
<body style="min-width:100%" bottommargin="0" topmargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
        <td align="center" style="font-family:Arial, Helvetica, sans-serif; color:#FFFFFF; font-size:1px;">Start using
            your software right away
        </td>
    </tr>
    <tr>
        <td align="center" valign="bottom"
            style="font-family:Arial, Helvetica, sans-serif; color:#837F7C; font-size:11px; line-height:130%"
            height="40">Download your software - <a
                    href="http://ao.com?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"
                    style="color:#837F7C; text-decoration:none">ao.com</a>.<br> <a href="#" style="color:#837F7C">View
                email online</a></td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
        <td align="center" valign="middle" height="90"><a
                    href="http://ao.com/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"><img
                        src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/AOlogo16.jpg" width="68" height="68"
                        border="0" title="ao logo" alt="ao logo"/></a></td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="hideContent">
    <tr>
        <td align="center" valign="middle"
            style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold">
            <table width="200" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="50" align="center" valign="middle"
                        style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold">
                        <a href="http://ao.com/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"
                           style="color:#484442; text-decoration:none">Home</a></td>
                    <td align="center" valign="middle" width="2" style="font-size:0px;line-height:0px"><img
                                src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/GreenPixelSpacer16.jpg" width="2"
                                height="13" border="0" style="display:block"/></td>
                    <td width="96" align="center" valign="middle"
                        style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold">
                        <a href="http://ao.com/help-and-advice?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"
                           style="color:#484442; text-decoration:none">Help &amp; Advice</a></td>
                    <td align="center" valign="middle" width="2" style="font-size:0px;line-height:0px"><img
                                src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/GreenPixelSpacer16.jpg" width="2"
                                height="13" border="0" style="display:block"/></td>
                    <td width="50" align="center" valign="middle"
                        style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold">
                        <a href="http://ao.com/deals.aspx?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"
                           style="color:#484442; text-decoration:none">Deals</a></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="hideContent">
    <tr>
        <td height="10" style="font-size:0px;line-height:0px">&nbsp;</td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
        <td align="center" valign="middle">
            <!--[if (mso)|(IE)]>
            <table border="0" cellspacing="0" cellpadding="0" width="640" align="center">
                <tr>
                    <td align="center" valign="middle" width="640"><![endif]-->
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#7FBA23"
                   style="max-width:640px" class="Width640">
                <tr>
                    <td height="2" style="font-size:0px;line-height:0px">&nbsp;</td>
                </tr>
            </table>
            <!--[if (mso)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]--></td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F3F3F3">
    <tr>
        <td height="10" style="font-size:0px;line-height:0px">&nbsp;</td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F3F3F3">
    <tr>
        <td align="center" valign="middle">
            <!--[if (mso)|(IE)]>
            <table border="0" cellspacing="0" cellpadding="0" width="640" align="center">
                <tr>
                    <td align="center" valign="middle" width="640"><![endif]-->
            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"
                   style="max-width:640px" class="Width640">
                <tr>
                    <td height="1" bgcolor="#CCCCCC" style="font-size:0px;line-height:0px"><!--[if mso 15]>&nbsp;
                        <![endif]--></td>
                </tr>
                <tr>
                <tr>
                    <td align="center" valign="middle"><!--[if (mso)|(IE)]>
                        <table border="0" cellspacing="0" cellpadding="0" width="640" align="center">
                            <tr>
                                <td align="center" valign="middle" width="640"><![endif]-->
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"
                               style="max-width:640px" class="Width640">
                            <tr>
                                <td width="1" align="left" valign="middle" bgcolor="#CCCCCC"><img
                                            src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/PixelSpacer16.jpg"
                                            width="1" height="1" border="0" style="display:block"/></td>
                                <td align="center" valign="bottom">
                                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="center" valign="middle"
                                                style="font-family:arial,helvetica,sans-serif; font-size:26px; color:#7FBA23"
                                                class="fontSize">Thanks for your order!
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="8" style="font-size:0px;line-height:0px">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="center" valign="middle"
                                                style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:16px">
                                                Order number: <?= $orderNumber ?></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                                <td width="1" align="right" valign="middle" bgcolor="#CCCCCC"><img
                                            src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/PixelSpacer16.jpg"
                                            width="1" height="1" border="0" style="display:block"/></td>
                            </tr>
                        </table>
                        <!--[if (mso)|(IE)]>
                        </td>
                        </tr>
                        </table>
                        <![endif]--></td>
                </tr>
                <tr>
                    <td height="1" bgcolor="#CCCCCC" style="font-size:0px;line-height:0px"><!--[if mso 15]>&nbsp;
                        <![endif]--></td>
                </tr>
            </table>
            <!--[if (mso)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]--></td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F3F3F3">
    <tr>
        <td height="10" style="font-size:0px;line-height:0px">&nbsp;</td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F3F3F3">
    <tr>
        <td align="center" valign="middle">
            <!--[if (mso)|(IE)]>
            <table border="0" cellspacing="0" cellpadding="0" width="640" align="center">
                <tr>
                    <td align="center" valign="middle" width="640"><![endif]-->
            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"
                   style="max-width:640px" class="Width640">
                <tr>
                    <td height="1" bgcolor="#CCCCCC" style="font-size:0px;line-height:0px"><!--[if mso 15]>&nbsp;
                        <![endif]--></td>
                </tr>
                <tr>
                    <td align="center" valign="middle">
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <td width="1" align="left" valign="middle" bgcolor="#CCCCCC"><img
                                        src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/PixelSpacer16.jpg" width="1"
                                        height="1" border="0" style="display:block"/></td>
                            <td align="center" valign="middle"><a
                                        href="http://ao.com/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"><img
                                            src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/ESDHeaderImg.jpg"
                                            width="638" class="imgWidth"
                                            style="display:block; max-width:638px !important" border="0"
                                            alt="Thanks for your order!" title="Thanks for your order!"/></a></td>
                            <td width="1" align="right" valign="middle" bgcolor="#CCCCCC"><img
                                        src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/PixelSpacer16.jpg" width="1"
                                        height="1" border="0" style="display:block"/></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="1" bgcolor="#CCCCCC" style="font-size:0px;line-height:0px"><!--[if mso 15]>&nbsp;
                        <![endif]--></td>
                </tr>
            </table>
            <!--[if (mso)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]--></td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F3F3F3">
    <tr>
        <td height="10" style="font-size:0px;line-height:0px">&nbsp;</td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F3F3F3">
    <tr>
        <td align="center" valign="middle">
            <!--[if (mso)|(IE)]>
            <table border="0" cellspacing="0" cellpadding="0" width="640" align="center">
                <tr>
                    <td align="center" valign="middle" width="640"><![endif]-->
            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"
                   style="max-width:640px" class="Width640">
                <tr>
                    <td height="1" bgcolor="#CCCCCC" style="font-size:0px;line-height:0px"><!--[if mso 15]>&nbsp;
                        <![endif]--></td>
                </tr>
                <tr>
                <tr>
                    <td align="center" valign="middle"><!--[if (mso)|(IE)]>
                        <table border="0" cellspacing="0" cellpadding="0" width="640" align="center">
                            <tr>
                                <td align="center" valign="middle" width="640"><![endif]-->
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"
                               style="max-width:640px" class="Width640">
                            <tr>
                                <td width="1" align="left" valign="middle" bgcolor="#CCCCCC"><img
                                            src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/PixelSpacer16.jpg"
                                            width="1" height="1" border="0" style="display:block"/></td>
                                <td align="center" valign="bottom">
                                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="left" valign="middle" class="center hidePad"
                                                style="padding-left:10px; padding-right:10px; color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%">
                                                Start using your software right away with one easy download. Simply
                                                follow the steps below and you'll be up and running in no time.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left" valign="middle"
                                                style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:18px; padding-top:20px; padding-left:10px"
                                                class="center"><strong>What to do next</strong></td>
                                        </tr>
                                        <tr>
                                            <td align="center" valign="top"><!--[if (mso)|(IE)]>
                                                <table border="0" cellspacing="0" cellpadding="0" width="638"
                                                       align="center">
                                                    <tr>
                                                        <td align="center" valign="top" width="638"><![endif]-->
                                                <table width="100%" border="0" align="center" cellpadding="0"
                                                       cellspacing="0" class="Width638" style="max-width:638px">
                                                    <tr>
                                                        <td align="center" valign="top"><!--[if (mso)|(IE)]>
                                                            <table border="0" cellspacing="0" cellpadding="0"
                                                                   width="319" align="left">
                                                                <tr>
                                                                    <td align="center" valign="top" width="319">
                                                            <![endif]-->
                                                            <table width="319" border="0" align="left" cellpadding="0"
                                                                   cellspacing="0" class="center deviceWidth">
                                                                <tr>
                                                                    <td align="center" valign="top">
                                                                        <table width="100%" border="0" align="center"
                                                                               cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td width="10">&nbsp;</td>
                                                                                <td align="center" valign="top">
                                                                                    <table width="100%" border="0"
                                                                                           align="center"
                                                                                           cellpadding="0"
                                                                                           cellspacing="0">
                                                                                        <tr>
                                                                                            <td height="10"
                                                                                                style="font-size:0px;line-height:0px">
                                                                                                &nbsp;</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td align="left"
                                                                                                valign="middle"
                                                                                                style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%; padding-top:10px; padding-bottom:10px"
                                                                                                class="center"><strong>1.</strong>
                                                                                                Highlight and copy this
                                                                                                code.
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td align="left"
                                                                                                valign="middle">
                                                                                                <table width="280"
                                                                                                       border="0"
                                                                                                       align="left"
                                                                                                       cellpadding="0"
                                                                                                       cellspacing="0"
                                                                                                       class="center deviceWidth2">
                                                                                                    <tr>
                                                                                                        <td align="left"
                                                                                                            valign="middle"
                                                                                                            style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%; padding-top:10px; padding-bottom:10px; padding-left:10px"
                                                                                                            class="center"
                                                                                                            bgcolor="#F3F3F3">
                                                                                                            <strong>
                                                                                                                <?= $productKey ?>
                                                                                                                <br/>
                                                                                                            </strong>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                </table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <?php if ($downloadUrl) { ?>
                                                                                            <tr>
                                                                                                <td align="left"
                                                                                                    valign="middle"
                                                                                                    style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%; padding-top:20px; padding-bottom:10px"
                                                                                                    class="center">
                                                                                                    <strong>2.</strong>
                                                                                                    Click this link.
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <td align="left"
                                                                                                    valign="middle">
                                                                                                    <table width="200"
                                                                                                           border="0"
                                                                                                           align="left"
                                                                                                           cellpadding="0"
                                                                                                           cellspacing="0"
                                                                                                           bgcolor="#7FBA23"
                                                                                                           class="center">
                                                                                                        <tr>
                                                                                                            <td width="150"
                                                                                                                height="40"
                                                                                                                align="left"
                                                                                                                valign="middle"
                                                                                                                style="font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#FFFFFF; font-weight:bold; padding-left:20px">
                                                                                                                <a href="<?= $downloadUrl ?>"
                                                                                                                   style="text-decoration:none; color:#FFFFFF; display:block; width:100%; line-height:40px">Download
                                                                                                                    now</a>
                                                                                                            </td>
                                                                                                            <td width="50"
                                                                                                                height="40"
                                                                                                                align="center"
                                                                                                                valign="middle">
                                                                                                                <a href="#"
                                                                                                                   style="line-height:40px"><img
                                                                                                                            src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/CTA-Arrow-White001.fw.png"
                                                                                                                            width="10"
                                                                                                                            height="16"
                                                                                                                            style="line-height:40px"
                                                                                                                            border="0"/></a>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    </table>
                                                                                                </td>
                                                                                            </tr>
                                                                                        <?php } // if $downloadUrl ?>
                                                                                    </table>
                                                                                </td>
                                                                                <td width="10">&nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!--[if (mso)|(IE)]>
                                                            </td>
                                                            </tr>
                                                            </table>
                                                            </td>
                                                            <td>
                                                                <table border="0" cellspacing="0" cellpadding="0"
                                                                       width="319" align="left">
                                                                    <tr>
                                                                        <td align="center" valign="top" width="319">
                                                            <![endif]-->
                                                            <table width="319" border="0" align="left" cellpadding="0"
                                                                   cellspacing="0" class="center deviceWidth">
                                                                <tr>
                                                                    <td align="center" valign="top">
                                                                        <table width="100%" border="0" align="left"
                                                                               cellpadding="0" cellspacing="0"
                                                                               bgcolor="#FFFFFF" class="center"
                                                                               style="mso-table-lspace:0;mso-table-rspace:0;max-width:319px;">
                                                                            <tr>
                                                                                <td width="10">&nbsp;</td>
                                                                                <td align="center" valign="top">
                                                                                    <table width="100%" border="0"
                                                                                           align="center"
                                                                                           cellpadding="0"
                                                                                           cellspacing="0">
                                                                                        <tr>
                                                                                            <td height="10"
                                                                                                style="font-size:0px;line-height:0px">
                                                                                                &nbsp;</td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td align="left"
                                                                                                valign="middle"
                                                                                                style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%; padding-top:10px; padding-bottom:10px"
                                                                                                class="center"><strong>3.</strong>
                                                                                                Register or sign in to
                                                                                                your Microsoft account.
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td align="left"
                                                                                                valign="middle"
                                                                                                style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%; padding-top:10px; padding-bottom:10px"
                                                                                                class="center"><strong>4.</strong>
                                                                                                Click 'My Account' and
                                                                                                hit 'Redeem'.
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td align="left"
                                                                                                valign="middle"
                                                                                                style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%; padding-top:10px"
                                                                                                class="center"><strong>5.</strong>
                                                                                                Paste in your code and
                                                                                                click confirm.
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td width="10">&nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                            <!--[if (mso)|(IE)]>
                                                            </td>
                                                            </tr>
                                                            </table>
                                                            <![endif]--></td>
                                                    </tr>
                                                </table>
                                                <!--[if (mso)|(IE)]>
                                                </td>
                                                </tr>
                                                </table>
                                                <![endif]--></td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                                <td width="1" align="right" valign="middle" bgcolor="#CCCCCC"><img
                                            src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/PixelSpacer16.jpg"
                                            width="1" height="1" border="0" style="display:block"/></td>
                            </tr>
                        </table>
                        <!--[if (mso)|(IE)]>
                        </td>
                        </tr>
                        </table>
                        <![endif]--></td>
                </tr>
                <tr>
                    <td height="1" bgcolor="#CCCCCC" style="font-size:0px;line-height:0px"><!--[if mso 15]>&nbsp;
                        <![endif]--></td>
                </tr>
            </table>
            <!--[if (mso)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]--></td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F3F3F3">
    <tr>
        <td height="10" style="font-size:0px;line-height:0px">&nbsp;</td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F3F3F3">
    <tr>
        <td align="center" valign="middle">
            <!--[if (mso)|(IE)]>
            <table border="0" cellspacing="0" cellpadding="0" width="640" align="center">
                <tr>
                    <td align="center" valign="middle" width="640"><![endif]-->
            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"
                   style="max-width:640px" class="Width640">
                <tr>
                    <td height="1" bgcolor="#CCCCCC" style="font-size:0px;line-height:0px"><!--[if mso 15]>&nbsp;
                        <![endif]--></td>
                </tr>
                <tr>
                <tr>
                    <td align="center" valign="middle"><!--[if (mso)|(IE)]>
                        <table border="0" cellspacing="0" cellpadding="0" width="640" align="center">
                            <tr>
                                <td align="center" valign="middle" width="640"><![endif]-->
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"
                               style="max-width:640px" class="Width640">
                            <tr>
                                <td width="1" align="left" valign="middle" bgcolor="#CCCCCC"><img
                                            src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/PixelSpacer16.jpg"
                                            width="1" height="1" border="0" style="display:block"/></td>
                                <td align="center" valign="bottom">
                                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="left" valign="middle"
                                                style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:18px; padding-bottom:10px; padding-left:10px"
                                                class="center"><strong>Need some help?</strong></td>
                                        </tr>
                                        <tr>
                                            <td align="left" valign="middle" class="center hidePad"
                                                style="padding-left:10px; padding-right:10px; color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%">
                                                Need some help installing your software? <a href="tel:0344 324 9222"
                                                                                            style="color:#484442; text-decoration:none">Give
                                                    us a call</a>, or live chat with us.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="center" valign="middle"><!--[if (mso)|(IE)]>
                                                <table border="0" cellspacing="0" cellpadding="0" width="638"
                                                       align="center">
                                                    <tr>
                                                        <td align="center" valign="middle" width="638"><![endif]-->
                                                <table width="100%" border="0" align="center" cellpadding="0"
                                                       cellspacing="0" class="Width638" style="max-width:638px">
                                                    <tr>
                                                        <td valign="middle"><!--[if (mso)|(IE)]>
                                                            <table border="0" cellspacing="0" cellpadding="0"
                                                                   width="319" align="left">
                                                                <tr>
                                                                    <td align="center" valign="middle" width="319">
                                                            <![endif]-->
                                                            <table width="319" border="0" align="left" cellpadding="0"
                                                                   cellspacing="0" class="center deviceWidth">
                                                                <tr>
                                                                    <td align="center" valign="middle">
                                                                        <table width="200" border="0" align="center"
                                                                               cellpadding="0" cellspacing="0"
                                                                               bgcolor="#179DDA">
                                                                            <tr>
                                                                                <td width="150" height="40" align="left"
                                                                                    valign="middle"
                                                                                    style="font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#FFFFFF; font-weight:bold; padding-left:20px">
                                                                                    <a href="http://ao.com/help-and-advice/help-with-my-order/contact-us?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"
                                                                                       style="text-decoration:none; color:#FFFFFF; display:block; width:100%; line-height:40px">Contact
                                                                                        us</a></td>
                                                                                <td width="50" height="40"
                                                                                    align="center" valign="middle"><a
                                                                                            href="http://ao.com/help-and-advice/help-with-my-order/contact-us?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"
                                                                                            style="line-height:40px"><img
                                                                                                src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/CTA-Arrow-White001.fw.png"
                                                                                                width="10" height="16"
                                                                                                style="line-height:40px"
                                                                                                border="0"/></a></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                </tr>
                                                            </table>
                                                            <!--[if (mso)|(IE)]>
                                                            </td>
                                                            </tr>
                                                            </table>
                                                            </td>
                                                            <td>
                                                                <table border="0" cellspacing="0" cellpadding="0"
                                                                       width="319" align="left">
                                                                    <tr>
                                                                        <td align="center" valign="middle" width="319">
                                                            <![endif]-->
                                                            <table width="319" border="0" align="left" cellpadding="0"
                                                                   cellspacing="0" class="center deviceWidth">
                                                                <tr>
                                                                    <td align="center" valign="middle">
                                                                        <table width="200" border="0" align="center"
                                                                               cellpadding="0" cellspacing="0"
                                                                               bgcolor="#179DDA">
                                                                            <tr>
                                                                                <td width="150" height="40" align="left"
                                                                                    valign="middle"
                                                                                    style="font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#FFFFFF; font-weight:bold; padding-left:20px">
                                                                                    <a href="http://ao.com/help-and-advice?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"
                                                                                       style="text-decoration:none; color:#FFFFFF; display:block; width:100%; line-height:40px">Ask
                                                                                        a question</a></td>
                                                                                <td width="50" height="40"
                                                                                    align="center" valign="middle"><a
                                                                                            href="http://ao.com/help-and-advice?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"
                                                                                            style="line-height:40px"><img
                                                                                                src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/CTA-Arrow-White001.fw.png"
                                                                                                width="10" height="16"
                                                                                                style="line-height:40px"
                                                                                                border="0"/></a></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>&nbsp;</td>
                                                                </tr>
                                                            </table>
                                                            <!--[if (mso)|(IE)]>
                                                            </td>
                                                            </tr>
                                                            </table>
                                                            <![endif]--></td>
                                                    </tr>
                                                </table>
                                                <!--[if (mso)|(IE)]>
                                                </td>
                                                </tr>
                                                </table>
                                                <![endif]--></td>
                                        </tr>
                                        <tr>
                                            <td align="left" valign="middle" class="center hidePad"
                                                style="padding-left:10px; padding-right:10px; padding-bottom:10px; color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%">
                                                Don't forget!
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left" valign="middle" class="center hidePad"
                                                style="padding-left:10px; padding-right:10px; color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%">&bull;
                                                You've bought a yearly subscription that'll need renewing in 12
                                                months.<br>&bull; It's a good idea to back up any data on your device
                                                before installing software.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                                <td width="1" align="right" valign="middle" bgcolor="#CCCCCC"><img
                                            src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/PixelSpacer16.jpg"
                                            width="1" height="1" border="0" style="display:block"/></td>
                            </tr>
                        </table>
                        <!--[if (mso)|(IE)]>
                        </td>
                        </tr>
                        </table>
                        <![endif]--></td>
                </tr>
                <tr>
                    <td height="1" bgcolor="#CCCCCC" style="font-size:0px;line-height:0px"><!--[if mso 15]>&nbsp;
                        <![endif]--></td>
                </tr>
            </table>
            <!--[if (mso)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]--></td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F3F3F3">
    <tr>
        <td height="10" style="font-size:0px;line-height:0px">&nbsp;</td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="hideContent">
    <tr>
        <td align="center" valign="middle">
            <!--[if (mso)|(IE)]>
            <table border="0" cellspacing="0" cellpadding="0" width="640" align="center">
                <tr>
                    <td align="center" valign="middle" width="640"><![endif]-->
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#7FBA23"
                   style="max-width:640px" class="Width640">
                <tr>
                    <td height="2" style="font-size:0px;line-height:0px">&nbsp;</td>
                </tr>
            </table>
            <!--[if (mso)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]--></td>
    </tr>
</table>
<!--[if !mso]><!-->
<table cellpadding="0" cellspacing="0" border="0" align="center" style="display:none;" class="mobNavShow">
    <tr style="width:0; overflow:hidden; float:left; display:none; max-height:0px; mso-hide:all;" class="mobNavShow2">
        <td align="center" valign="middle" class="mobNavShow3">
            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" valign="middle" width="480" height="40"
                        style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold; border:1px solid #CCCCCC; background-color:#FFFFFF"
                        class="mobNavButtn"><a
                                href="http://ao.com/laundry/washing-machines/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"
                                style="color:#484442; text-decoration:none; display:block; line-height:40px"
                                class="mobNavButtn">Washing Machines</a></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" align="center" style="display:none;" class="mobNavShow">
    <tr style="width:0; overflow:hidden; float:left; display:none; max-height:0px; mso-hide:all;" class="mobNavShow2">
        <td align="center" valign="middle" class="mobNavShow3">
            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" valign="middle" width="480" height="40"
                        style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold; border:1px solid #CCCCCC; background-color:#FFFFFF"
                        class="mobNavButtn"><a
                                href="http://ao.com/dishwashers/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"
                                style="color:#484442; text-decoration:none; display:block; line-height:40px"
                                class="mobNavButtn">Dishwashers</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" align="center" style="display:none;" class="mobNavShow">
    <tr style="width:0; overflow:hidden; float:left; display:none; max-height:0px; mso-hide:all;" class="mobNavShow2">
        <td align="center" valign="middle" class="mobNavShow3">
            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" valign="middle" width="480" height="40"
                        style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold; border:1px solid #CCCCCC; background-color:#FFFFFF"
                        class="mobNavButtn"><a
                                href="http://ao.com/cooling/fridge-freezers/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"
                                style="color:#484442; text-decoration:none; display:block; line-height:40px"
                                class="mobNavButtn">Fridges &amp; Freezers</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" align="center" style="display:none;" class="mobNavShow">
    <tr style="width:0; overflow:hidden; float:left; display:none; max-height:0px; mso-hide:all;" class="mobNavShow2">
        <td align="center" valign="middle" class="mobNavShow3">
            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" valign="middle" width="480" height="40"
                        style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold; border:1px solid #CCCCCC; background-color:#FFFFFF"
                        class="mobNavButtn"><a
                                href="http://ao.com/cooking/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"
                                style="color:#484442; text-decoration:none; display:block; line-height:40px"
                                class="mobNavButtn">Cooking</td>
                </tr>
            </table>
        </td>

    </tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" align="center" style="display:none;" class="mobNavShow">
    <tr style="width:0; overflow:hidden; float:left; display:none; max-height:0px; mso-hide:all;" class="mobNavShow2">
        <td align="center" valign="middle" class="mobNavShow3">
            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" valign="middle" width="480" height="40"
                        style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold; border:1px solid #CCCCCC; background-color:#FFFFFF"
                        class="mobNavButtn"><a
                                href="http://ao.com/small-appliances?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"
                                style="color:#484442; text-decoration:none; display:block; line-height:40px"
                                class="mobNavButtn">Small Appliances</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table cellpadding="0" cellspacing="0" border="0" align="center" style="display:none;" class="mobNavShow">
    <tr style="width:0; overflow:hidden; float:left; display:none; max-height:0px; mso-hide:all;" class="mobNavShow2">
        <td align="center" valign="middle" class="mobNavShow3">
            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center" valign="middle" width="480" height="40"
                        style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold; border:1px solid #CCCCCC; background-color:#FFFFFF"
                        class="mobNavButtn"><a
                                href="http://ao.com/sound-and-vision/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"
                                style="color:#484442; text-decoration:none; display:block; line-height:40px"
                                class="mobNavButtn">Sound &amp; Vision</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<!--<![endif]-->
<!--[if !mso]><!-->
<table cellpadding="0" cellspacing="0" border="0" align="center" style="display:none;" class="mobNavShow4">
    <tr style="width:0; overflow:hidden; float:left; display:none; max-height:0px; mso-hide:all;" class="mobNavShow2">
        <td align="center" valign="middle" class="mobNavShow3">
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
                <tr>
                    <td align="center" valign="middle">
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#7FBA23">
                            <tr>
                                <td width="480" height="2" style="font-size:0px;line-height:0px">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<!--<![endif]-->
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td height="15" style="font-size:0px;line-height:0px">&nbsp;</td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle">
            <table width="280" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="60" align="center" valign="middle"><a
                                href="http://ao.com/life/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=ESD&WT.srch=1"><img
                                    src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/Icon216.jpg" alt="ao Life"
                                    width="60" height="29" title="ao Life" style="display:block" border="0"/></a></td>
                    <td width="10">&nbsp;</td>
                    <td width="168" align="center" valign="middle"><a
                                href="https://uk.trustpilot.com/review/www.ao.com"><img
                                    src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/Icon316.jpg"
                                    alt="Trustpilot - Rated higher than Currys, Argos &amp; John Lewis" width="163"
                                    height="29" title="Trustpilot - Rated higher than Currys, Argos &amp; John Lewis"
                                    style="display:block" border="0"/></a></td>
                    <td width="10">&nbsp;</td>
                    <td width="32" align="center" valign="middle"><a href="https://www.facebook.com/AOLetsGo"><img
                                    src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/Icon516.jpg" alt="facebook"
                                    width="28" height="25" title="facebook" style="display:block" border="0"/></a></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td height="12" style="font-size:0px;line-height:0px">&nbsp;</td>
    </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
        <td align="center" valign="top"
            style="font-family:Arial, Helvetica, sans-serif; color:#837F7C; font-size:11px; line-height:20px; padding-left:10px; padding-right:10px"
            height="70"><a href="#" style="color:#837F7C; text-decoration:none">You have received this email because you
                have placed an order with ao.com. <br class="hideContent">AO Retail Limited, 5a The Parklands, Lostock,
                Bolton. BL6 4SD United Kingdom.</a><br><a href="tel:0344 324 9222"
                                                          style="color:#837F7C; text-decoration:none">0344 324 9222</a>
        </td>
    </tr>
</table>
</body>
</html>
