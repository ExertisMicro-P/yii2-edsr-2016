<?php

/**
 * The naming of this file is important
 * email_Norton_AO.one-pdfs.php
 * "McAfee" matches the Publisher from ztorm_catalogue_cache
 * "AO" matches the storeAlias for the Account
 * ".one" means send each key in its own email
 * "-pdfs" means attach a PDF to the email
 */ 

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
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--[if !mso]><!-- -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!--<![endif]-->
<meta name="HandheldFriendly" content="True" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="format-detection" content="telephone=no">
<title>ao.com</title>
<style>
 .ReadMsgBody {width: 100%;}
 .ExternalClass {width: 100%; line-height:100%;}
  body {width:100% !important; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; padding:0; margin:0 !important;}
  div[style*="margin: 16px 0"] {margin:0 !important; font-size:100% !important;}
  #outlook a {padding:0;} 
 .AOLcolor a {color:#484442; text-decoration:underline;}
 .Outlookcolor a {color:#837F7C; text-decoration:none;}
  
  @media screen and (min-width: 640px){
  .Width640 {width:640px !important;}
  .Width638 {width:638px !important;}
  .deviceWidth {width:319px !important;}
  }
  
  @media screen and (max-width: 640px){
  .deviceWidth {width:50% !important;}
  .deviceWidth2 {width:95% !important;}
  .imgWidth {width:100% !important; height:auto !important;}
  }
  
  @media only screen and (max-width: 480px){
  .hideContent {display:none !important;}
  .deviceWidth {width:100% !important; max-width:100% !important;}
  .imgWidth {width:100% !important; height:auto !important;}
  .center {text-align: center !important; margin: 0px auto !important; float: none !important;}
  .fontSize {font-size:18px !important;}
  
  /* Mobile Nav */
  .mobNavShow {width:100% !important; height:auto !important; display:table !important; background-color:#F3F3F3; padding-bottom:10px;}
  .mobNavShow2 {display:block!important; width:100%!important; max-height:inherit!important; overflow:visible!important;}
  .mobNavShow3 {display: block!important; width:100% !important;height:auto !important;text-align:center!important; float:none!important;}
  .mobNavShow4 {width:100% !important; height:auto !important; display:table !important;}
  .mobNavButtn:hover {background:#484442 !important; transition: 0.3s !important;}
  .mobNavButtn a:hover {color:#FFFFFF !important; transition: 0.3s!important;}
  } 
  
  @media screen and (max-width: 300px){
  .hidePad {padding:0px !important;}
  }
</style>
<!--[if gte mso 9]><xml>
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
	href="http://ao.com?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1" 
	style="color:#837F7C; text-decoration:none">ao.com</a>.<br>  <a href="#" style="color:#837F7C">View 
	email online</a> </td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td align="center" valign="middle" height="90"><a 
	href="http://ao.com/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1"><img 
	src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/AOlogo16.jpg" width="68" height="68" 
	border="0"  title="ao logo" alt="ao logo" /></a></td>
  </tr>
    <tr>
        <td align="center" valign="middle" height="90"><a
                    href=""><img
                        src="http://res.cloudinary.com/exertis-uk/w_118,h_50,c_pad/manu_logos/Norton_Horiz_RGB_1.jpg"
                        border="0" title="norton logo" alt="norton logo"/></a></td>
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
		<a href="http://ao.com/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1" 
		style="color:#484442; text-decoration:none">Home</a></td>
        <td align="center" valign="middle" width="2" style="font-size:0px;line-height:0px"><img 
		src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/GreenPixelSpacer16.jpg" width="2" 
		height="13" border="0" style="display:block" /></td>
        <td width="96" align="center" valign="middle" 
		style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold">
		<a href="http://ao.com/help-and-advice?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1" 
		style="color:#484442; text-decoration:none">Help &amp; Advice</a></td>
        <td align="center" valign="middle" width="2" style="font-size:0px;line-height:0px"><img 
		src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/GreenPixelSpacer16.jpg" width="2" 
		height="13" border="0" style="display:block" /></td>
        <td width="50" align="center" valign="middle" 
		style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold">
		<a href="http://ao.com/deals.aspx?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1" 
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
			width="1" height="1" border="0" style="display:block" /></td>
            <td align="center" valign="bottom">
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td>&nbsp;</td>
              </tr>
               <tr>
                 <td align="center" valign="middle" 
				 style="font-family:arial,helvetica,sans-serif; font-size:26px; color:#7FBA23" 
				 class="fontSize">Your Norton Antivirus is here. Download now!
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
			width="1" height="1" border="0" style="display:block" /></td>
          </tr>
          </table><!--[if (mso)|(IE)]>
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
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F3F3F3" class="hideContent">
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
		height="1" border="0" style="display:block" /></td>
            <td align="center" valign="middle"><a href="http://www.norton.com/setup"><img 
			src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/NortonAV111016.jpg" width="638" 
			style="display:block; max-width:638px !important" border="0" 
			alt="Download your software" title="Download your software" class="imgWidth" /></a>
			</td>
            <td width="1" align="right" valign="middle" bgcolor="#CCCCCC"><img 
			src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/PixelSpacer16.jpg" width="1" 
			height="1" border="0" style="display:block" /></td>
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
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F3F3F3" class="hideContent">
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
          <td align="center" valign="middle" width="640"><![endif]--><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" 
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
          <td align="center" valign="middle" width="640"><![endif]--><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" 
		  style="max-width:640px" class="Width640">
          <tr>
            <td width="1" align="left" valign="middle" bgcolor="#CCCCCC"><img 
			src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/PixelSpacer16.jpg" 
			width="1" height="1" border="0" style="display:block" /></td>
            <td align="center" valign="bottom">
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td>&nbsp;</td>
              </tr>
               <tr>
                 <td align="left" valign="middle" class="center hidePad" 
				 style="padding-left:10px; padding-right:10px; color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%">
				 Thanks for buying Norton antivirus from <a 
				 href="http://ao.com/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1" 
				 style="color:#484442; text-decoration:none">ao.com</a>.  
				 Your software comes as a digital download to make sure that you get the latest version. 
				 Simply follow the 6 steps below and you can use your antivirus instantly.  
				 </td>
               </tr>
            <tr>
               <td>&nbsp;</td>
            </tr>
               <tr>
                <td align="left" valign="middle" 
				style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:18px; padding-left:10px; padding-right:10px" 
				class="center hidePad"><strong>6 simple steps to download. Get 
				started...</strong></td>
              </tr>
            <tr>
               <td>&nbsp;</td>
            </tr>
            <tr>
               <td align="left" 
			   valign="middle" 
			   style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; padding-left:10px; padding-right:10px" 
			   class="center hidePad"><strong>Step 1.</strong> Highlight and copy this code (this is your product key)
			   </td>
            </tr>
            <tr>
               <td height="10" style="font-size:0px;line-height:0px">&nbsp;</td>
            </tr>
            <tr>
               <td align="left" valign="middle" 
			   style="padding-left:10px; padding-right:10px" class="center hidePad">
			   <table width="280" border="0" align="left" cellpadding="0" 
			   cellspacing="0" class="center deviceWidth2">
                  <tr>
                    <td align="left" valign="middle" 
					style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%; padding-top:10px; padding-bottom:10px; padding-left:10px" 
					class="center" 
					bgcolor="#F3F3F3">
					<strong>
					<?= $productKey ?>
					</strong>
					</td>
                  </tr>
                </table>
				</td>
            </tr>
            <tr>
               <td>&nbsp;</td>
            </tr>
            <tr>
               <td align="left" 
			   valign="middle" 
			   style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%;  padding-left:10px; padding-right:10px" 
			   class="center hidePad"><strong>Step 2.</strong> Go to: <span 
			   class="AOLcolor"><a href="https://norton.com/setup" 
			   style="color:#484442; text-decoration:underline"><strong>norton.com/setup</strong></a></span> 
			   (this will open in a new tab).</td>
            </tr>
           <tr>
               <td>&nbsp;</td>
            </tr>
            <tr>
               <td align="left" valign="middle" style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%;  padding-left:10px; padding-right:10px" class="center hidePad"><strong>Step 3.</strong> Sign in to your Norton account.<br>
                 It's okay if you dont have an account - it only takes a couple of minutes to make one.</td>
            </tr>
            <tr>
               <td>&nbsp;</td>
            </tr>
            <tr>
              <td align="left" valign="middle" style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%;  padding-left:10px; padding-right:10px" class="center hidePad"><strong>Step 4.</strong> Paste in your code from Step 1 (your product key) and click the arrow.</td>
            </tr>
            <tr>
              <td align="left" valign="middle" style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%;  padding-left:10px; padding-right:10px" class="center hidePad"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
           <tr>
              <td align="left" valign="middle" style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%;  padding-left:10px; padding-right:10px" class="center hidePad"><strong>Step 5.</strong> If you want your Norton subscription to be automatically renewed, click 'Get Started' and follow the on-screen instructions.<br><br>If you don't want automatic renewal, you can skip this step (shown below)</td>
            </tr>
            
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
               <td align="left" valign="middle" style="padding-left:10px; padding-right:10px" class="center hidePad"><img src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/NortonSS1210.jpg" width="270" height="131" style="display:block" border="0" alt="Screen Shot" title="Screen shot" class="center" /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td align="left" valign="middle" style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%;  padding-left:10px; padding-right:10px" class="center hidePad"><strong>Step 6.</strong> Click 'Agree and Download'.</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
            </tr>
           </table></td>
            <td width="1" align="right" valign="middle" bgcolor="#CCCCCC"><img src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/PixelSpacer16.jpg" width="1" height="1" border="0" style="display:block" /></td>
          </tr>
          </table><!--[if (mso)|(IE)]>
          </td>
          </tr>
       </table>
    <![endif]--></td>
      </tr>
      <tr>
        <td height="1" bgcolor="#CCCCCC" style="font-size:0px;line-height:0px"><!--[if mso 15]>&nbsp;<![endif]--></td>
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
          <td align="center" valign="middle" width="640"><![endif]--><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="max-width:640px" class="Width640">
      <tr>
         <td height="1" bgcolor="#CCCCCC" style="font-size:0px;line-height:0px"><!--[if mso 15]>&nbsp;<![endif]--></td>
      </tr>
      <tr>
         <tr>
        <td align="center" valign="middle"><!--[if (mso)|(IE)]>
    <table border="0" cellspacing="0" cellpadding="0" width="640" align="center">
      <tr>
          <td align="center" valign="middle" width="640"><![endif]--><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:640px" class="Width640">
          <tr>
            <td width="1" align="left" valign="middle" bgcolor="#CCCCCC"><img src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/PixelSpacer16.jpg" width="1" height="1" border="0" style="display:block" /></td>
            <td align="center" valign="bottom"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td align="left" valign="middle" style="color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:18px; padding-right:10px; padding-left:10px" class="center hidePad"><strong>Need some help?</strong></td>
              </tr>
              <tr>
               <td height="10" style="font-size:0px;line-height:0px">&nbsp;</td>
              </tr>
               <tr>
                 <td align="left" valign="middle" class="center hidePad" style="padding-left:10px; padding-right:10px; color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%">Visit our comprehensive guide to downloading your software.</td>
               </tr>
               <tr>
                  <td>&nbsp;</td>
               </tr> 
               <tr>
                 <td align="left" valign="middle" class="center hidePad" style="padding-left:10px; padding-right:10px"><table width="200" border="0" align="left" cellpadding="0" cellspacing="0" bgcolor="#179DDA" class="center">
                  <tr>
                    <td width="130" height="40" align="left" valign="middle" style="font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#FFFFFF; font-weight:bold; padding-left:20px"><a href="http://ao.com/help-and-advice?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1" style="text-decoration:none; color:#FFFFFF; display:block; width:100%; line-height:40px">Find out more</a></td>
                    <td width="50" height="40" align="center" valign="middle"><a href="http://ao.com/help-and-advice?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1" style="line-height:40px"><img src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/CTA-Arrow-White001.fw.png" width="10" height="16" style="line-height:40px" border="0"/></a></td>
                  </tr>
                </table></td>
               </tr>  
               <tr>
                  <td>&nbsp;</td>
               </tr>    
       <tr>
          <td align="left" valign="middle" class="center hidePad" style="padding-left:10px; padding-right:10px; color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%">Don't forget!</td>
       </tr>
          <tr>
               <td height="10" style="font-size:0px;line-height:0px">&nbsp;</td>
          </tr>
          <tr>
             <td align="left" valign="middle" class="center hidePad" style="padding-left:10px; padding-right:10px; color:#484442; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:130%">&bull; You've bought a yearly subscription that'll need renewing in 12 months.<br>&bull; It's a good idea to back up any data on your device before installing software.</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
              </table></td>
            <td width="1" align="right" valign="middle" bgcolor="#CCCCCC"><img src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/PixelSpacer16.jpg" width="1" height="1" border="0" style="display:block" /></td>
          </tr>
          </table><!--[if (mso)|(IE)]>
          </td>
          </tr>
       </table>
    <![endif]--></td>
      </tr>
      <tr>
        <td height="1" bgcolor="#CCCCCC" style="font-size:0px;line-height:0px"><!--[if mso 15]>&nbsp;<![endif]--></td>
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
          <td align="center" valign="middle" width="640"><![endif]--><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#7FBA23" style="max-width:640px" class="Width640">
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
    <td align="center" valign="middle" class="mobNavShow3"><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="center" valign="middle" width="480" height="40" style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold; border:1px solid #CCCCCC; background-color:#FFFFFF" class="mobNavButtn"><a href="http://ao.com/laundry/washing-machines/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1" style="color:#484442; text-decoration:none; display:block; line-height:40px" class="mobNavButtn">Washing Machines</a></td>
          </tr>
         </table></td>
  </tr>
</table>
 <table cellpadding="0" cellspacing="0" border="0" align="center" style="display:none;" class="mobNavShow">
   <tr style="width:0; overflow:hidden; float:left; display:none; max-height:0px; mso-hide:all;" class="mobNavShow2">
    <td align="center" valign="middle" class="mobNavShow3"><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="center" valign="middle" width="480" height="40" style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold; border:1px solid #CCCCCC; background-color:#FFFFFF" class="mobNavButtn"><a href="http://ao.com/dishwashers/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1" style="color:#484442; text-decoration:none; display:block; line-height:40px" class="mobNavButtn">Dishwashers</td>
          </tr>
         </table></td>
   </tr>
 </table>
 <table cellpadding="0" cellspacing="0" border="0" align="center" style="display:none;" class="mobNavShow">
   <tr style="width:0; overflow:hidden; float:left; display:none; max-height:0px; mso-hide:all;" class="mobNavShow2">
    <td align="center" valign="middle" class="mobNavShow3"><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="center" valign="middle" width="480" height="40" style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold; border:1px solid #CCCCCC; background-color:#FFFFFF" class="mobNavButtn"><a href="http://ao.com/cooling/fridge-freezers/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1" style="color:#484442; text-decoration:none; display:block; line-height:40px" class="mobNavButtn">Fridges &amp; Freezers</td>
          </tr>
         </table></td>
   </tr>
 </table>
 <table cellpadding="0" cellspacing="0" border="0" align="center" style="display:none;" class="mobNavShow">
   <tr style="width:0; overflow:hidden; float:left; display:none; max-height:0px; mso-hide:all;" class="mobNavShow2">
    <td align="center" valign="middle" class="mobNavShow3"><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="center" valign="middle" width="480" height="40" style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold; border:1px solid #CCCCCC; background-color:#FFFFFF" class="mobNavButtn"><a href="http://ao.com/cooking/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1" style="color:#484442; text-decoration:none; display:block; line-height:40px" class="mobNavButtn">Cooking</td>
          </tr>
         </table></td>

   </tr>
 </table>
  <table cellpadding="0" cellspacing="0" border="0" align="center" style="display:none;" class="mobNavShow">
   <tr style="width:0; overflow:hidden; float:left; display:none; max-height:0px; mso-hide:all;" class="mobNavShow2">
    <td align="center" valign="middle" class="mobNavShow3"><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="center" valign="middle" width="480" height="40" style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold; border:1px solid #CCCCCC; background-color:#FFFFFF" class="mobNavButtn"><a href="http://ao.com/small-appliances?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1" style="color:#484442; text-decoration:none; display:block; line-height:40px" class="mobNavButtn">Small Appliances</td>
          </tr>
         </table></td>
   </tr>
 </table>
 <table cellpadding="0" cellspacing="0" border="0" align="center" style="display:none;" class="mobNavShow">
   <tr style="width:0; overflow:hidden; float:left; display:none; max-height:0px; mso-hide:all;" class="mobNavShow2">
    <td align="center" valign="middle" class="mobNavShow3"><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="center" valign="middle" width="480" height="40" style="font-family:Arial, Helvetica, sans-serif; color:#484442; font-size:12px; font-weight:bold; border:1px solid #CCCCCC; background-color:#FFFFFF" class="mobNavButtn"><a href="http://ao.com/sound-and-vision/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1" style="color:#484442; text-decoration:none; display:block; line-height:40px" class="mobNavButtn">Sound &amp; Vision</td>
          </tr>
         </table></td>
   </tr>
 </table>
<!--<![endif]-->
<!--[if !mso]><!-->
<table cellpadding="0" cellspacing="0" border="0" align="center" style="display:none;" class="mobNavShow4">
   <tr style="width:0; overflow:hidden; float:left; display:none; max-height:0px; mso-hide:all;" class="mobNavShow2">
    <td align="center" valign="middle" class="mobNavShow3"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td align="center" valign="middle"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#7FBA23">
      <tr>
        <td width="480" height="2" style="font-size:0px;line-height:0px">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table></td>
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
    <td align="center" valign="middle"><table width="280" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td width="60" align="center" valign="middle"><a href="http://ao.com/life/?WT.z_PT=MDA&WT.z_MT=Retention&WT.z_RTM=Email&WT.z_EMT=transactional&WT.z_EMN=NortonDL&WT.srch=1"><img src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/Icon216.jpg" alt="ao Life" width="60" height="29" title="ao Life" style="display:block" border="0" /></a></td>
        <td width="10">&nbsp;</td>
        <td width="168" align="center" valign="middle"><a href="https://uk.trustpilot.com/review/www.ao.com"><img src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/Icon316.jpg" alt="Trustpilot - Rated higher than Currys, Argos &amp; John Lewis" width="163" height="29" title="Trustpilot - Rated higher than Currys, Argos &amp; John Lewis" style="display:block" border="0" /></a></td>
        <td width="10">&nbsp;</td>
        <td width="32" align="center" valign="middle"><a href="https://www.facebook.com/AOLetsGo"><img src="http://wpm.ccmp.eu/wpm/100104/ContentUploads/Icon516.jpg" alt="facebook" width="28" height="25" title="facebook" style="display:block" border="0" /></a></td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="12" style="font-size:0px;line-height:0px">&nbsp;</td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td align="center" valign="top" style="font-family:Arial, Helvetica, sans-serif; color:#837F7C; font-size:11px; line-height:20px; padding-left:10px; padding-right:10px" height="70"><a href="#" style="color:#837F7C; text-decoration:none">You have received this email because you have placed an order with ao.com. <br class="hideContent">AO Retail Limited, 5a The Parklands, Lostock, Bolton. BL6 4SD United Kingdom.</a><br><span class="Outlookcolor"><a href="tel:0344 324 9222" style="color:#837F7C; text-decoration:none">0344 324 9222</a></span></td>
  </tr>
</table>
</body>
</html>