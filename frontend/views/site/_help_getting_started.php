<?php
use yii\bootstrap\Collapse;

?>
<!-- site/_help_faq.php -->

<p>Exertis Digital Stock Room (EDSR) manages your digital stock and provides a 
    quick an easy way to buy digital products instantly, 24/7. Here's a brief 
    overview of how to use EDSR.</p>


<div class="media">
  <div class="media-left media-middle">
    <a href="#">
      <?= \yii\helpers\Html::img('/img/gs_login.jpg', ['class'=>'gsimage']); ?>
    </a>
  </div>
  <div class="media-body">
    <h2>Log In to EDSR</h2>
<p>To use EDSR you must login. You can request a login by contacting your account 
    manager, or by placing an order for any digital product.</p>
<p>When we set up your login, you'll get an email containing a link. Click on the link
    to set up you own password, and to agree to our <?= \yii\helpers\Html::a('Terms and Conditions', 'site/legal') ?>.<p>
  </div>
</div>


<div class="media">
  <div class="media-left media-middle">
    <a href="#">
      <?= \yii\helpers\Html::img('/img/gs_menu.png', ['class'=>'gsimage']); ?>
    </a>
  </div>
  <div class="media-body">
    <h2>The Main Areas</h2>
<p>EDSR consists of 3 main areas:</p>
<ul>
    <li>Shop</li>
    <li>Stockroom</li>
    <li>Order History</li>
</ul>

    <p>Additionally, we have areas for News, Legal, Help, and Settings.</p>
<p>Below the main menu you will also see the name of your Stockroom, your 
    Company Logo (if set), a Delivery button and a Basket button.</p>
  </div>
</div>



<div class="media">
  <div class="media-left media-middle">
    <a href="#">
      <?= \yii\helpers\Html::img('/img/gs_shop.png', ['class'=>'gsimage']); ?>
    </a>
  </div>
  <div class="media-body">
    <h2>The Shop - buying keys</h2>
<p>You can buy your keys quickly and easily by clicking on Shop in the main menu 
    and using the 'Add to Basket' buttons alongside the digital products you want.</p>

<p>If you need to know more about the product, click on the 'Details' button.</p>
<p>You can also filer or search for products.</p>

    <p>The green basket icon button at top will show how many items you have in 
        your basket. Click on the basket icon button to view your basket.</p>
    
    <p>Note: There is a limit to the number of keys you can buy at a time.</p>
  </div>
</div>



<div class="media">
  <div class="media-left media-middle">
    <a href="#">
      <?= \yii\helpers\Html::img('/img/gs_stockroom.png', ['class'=>'gsimage']); ?>
    </a>
  </div>
  <div class="media-body">
    <h2>The Stockroom - taking delivery and managing keys</h2>
<p>Any key you buy, be it via your account manager, via our main website or via 
    EDSR, will be delivered into your online Stockroom. From here you can view your 
    stock of keys, and select keys that your want to deliver onwards to your end-customer.
    Check the checkbox on the far right hand side to mark the key for delivery. 
    It will appear in the green 'Items Picked For Delivery' button.</p>

<p>Once you've selected all the keys you want to manage, click on the 'Items 
    Picked For Delivery' button to see the keys and all the options for delivery.<p>

<p>If you need to know more about the product, click on the 'Details' button.</p>
<p>You can also filer or search for products.</p>

    <p>The green basket icon button at top will show how many items you have in 
        your basket. Click on the basket icon button to view your basket.</p>
    
    <p>Note: There is a limit to the number of keys you can buy at a time.</p>
  </div>
</div>



<div class="media">
  <div class="media-left media-middle">
    <a href="#">
      <?= \yii\helpers\Html::img('/img/gs_delivery.png', ['class'=>'gsimage']); ?>
    </a>
  </div>
  <div class="media-body">
    <h2>Delivery Manager - delivering your keys</h2>
<p>You can deal with your keys in 3 ways:</p>
<ul>
    <li>Mark them as Delivered</li>
    <li>Email them to any email address</li>
    <li>Print them to A4 paper</li>
</ul>
    
<p>Marking a key as delivered means you've handled the key some other way. Maybe 
    you've cut and pasted into an email. Marking it, means you'll know you've already used it.</p>

<p>When you email keys, you be asked for a Recipient name, email address, and some 
    reference so you can tie up the delivery later.</p>

<p>Printing the keys is mainly used by retail stores to give walk-in customers something to take away with them.</p>
    
   
<p>Once you've done any of the above, those keys will be moved into the Order History
Area. Effectively, they've been archived. You can still get to them if you need 
to, but they are no longer considered to be 'stock'.<p>

    <p>Note: There is a limit to the number of keys you can deliver at a time.</p>
  </div>
</div>



<div class="media">
  <div class="media-left media-middle">
    <a href="#">
      <?= \yii\helpers\Html::img('/img/gs_history.png', ['class'=>'gsimage']); ?>
    </a>
  </div>
  <div class="media-body">
    <h2>Order History - your archived keys</h2>
<p>You'll find all the keys you've dealt with in here. These keys shouldn't really 
    be needed, unless your customer never received them, or you need them again for some reason.</p>
  </div>
</div>


<div class="media">
  <div class="media-left media-middle">
    <a href="#">
      <?= \yii\helpers\Html::img('/img/gs_settings.png', ['class'=>'gsimage']); ?>
    </a>
  </div>
  <div class="media-body">
    <h2>Settings - do things your way</h2>
<p>Click on the 'cog' in the top left hand corner to reach your Settings page.
    This allows you to control how EDSR works for you. </p>
<p>You can:</p>
<ul>
    <li>Manage additional users on your account</li>
    <li>Control how your keys are delivered in your notifications</li>
    <li>Upload your own logo</li>
    <li>Check activity on your account</li>
</ul>
  </div>
</div>


<!--<div class="media">
  <div class="media-left media-middle">
    <a href="#">
      <?= \yii\helpers\Html::img('/img/adobe.png', ['class'=>'gsimage']); ?>
    </a>
  </div>
  <div class="media-body">
    <h2>Print Xbox Code - For Firefox users</h2>
<p>If you are using Mozilla Firefox and you trying to print an Xbox Code but the leaflet is blurry, follow these steps:</p>
<ul>
    <li>Open a new tab and navigate to <a href="https://get.adobe.com/uk/reader/">https://get.adobe.com/uk/reader/</a></li>
    <li>Follow the steps on the website, and install the Reader.</li>
    <li>When the installation process is done, go back to EDSR and refresh the page and try to print it.</li>
</ul>
<p>If your browser still opens the Xbox label with the default PDF Reader, follow these steps:</p>
<ul>
    <li>On the top right corner of your browser, click on the "list" icon and select "Options"</li>
    <li>When the new tab opens, select "applications" on the left menu.</li>
    <li>You need to change all the "Adobe Acrobat" files to "Use Adobe Reader", this will make Adobe Reader the default PDF Viewer.</li>
</ul>
  </div>
</div>-->