<?php
use yii\bootstrap\Collapse;

?>
<!-- site/_help_faq.php -->


<?php



echo Collapse::widget([
    'items' => [
        
        [
            'label' => 'Changes - August 2016',
            'content' => '
                <h4>Upgrade</h4>
                <p>NOTICE: 2016-07-28 15:56 - Essential Scehduled Upgrade to Servers completed.</p>
                <h4>Shop</h4>
                <p>We have expanded our product range with the addition of 4 McAfee Products. You can now order via you account manager or
                within the EDSR Shop.</p>
                <p>We will be enabling the EDSR Shop for all resellers during August and September allowing self service 24/7 instant key delivery.</p>
                ',
            // open its content by default
            'contentOptions' => ['class' => 'in']
        ],
        
        [
            'label' => 'Changes - April 2016',
            'content' => '
                <h3>Fixes</h3>
                <p>RESOLVED: 2016-04-08 11:40 - Emailing multiple codes should now work. Order History will now correctly show multiple keys also.</p>
                <p>RESOLVED: 2016-03-30 16:14 - Credit limits should now be okay.</p> 
                <h3>Changes</h3>
                <h4>Shop</h4>
                <p>There is now a limit on the number of items you can put 
                in your basket at a time. The default is 10 items. This is so we comply with Microsoft\'s Agency Model.</p> 
                <p>We can now control which product you see on an account by account 
                basis. For example, you will need approval from your Exertis Account Manager if you want to buy Xbox products.</p>
                <h4>Settings</h4>
                <p>Logo Uploading should now work! Upload your own company logo and in the future we will 
                use it to brand your emails to your customers.</a>
                <p>Now displays the Main User\'s email address. This is where all notifications will be sent to.</p>
                ',
            // open its content by default
            //'contentOptions' => ['class' => 'in']
        ],
        
                [
            'label' => 'Changes - February 2016',
            'content' => '
                <h3>Shop</h3>
                <p>Our range of digital products continues to expand with the addition 
                of Xbox Gift Card and Subscription codes. More products including Security products and PC games are coming soon.
                We\'ve made general improvements into how the shop is presented and
                how it works. Pricing is now accurate for "Agency Model" items such as Xbox Codes</p>
                

               <h3>Settings</h3>
               <p>You can now have multiple users on a single account.
               Create your own users and manage them in the Settings section. Click on the <span class="glyphicon glyphicon-cog"></span>
               You can also see recent activity on your account.
               </p>
               
                ',
            // open its content by default
            //'contentOptions' => ['class' => 'in']
        ],
        [
            'label' => 'Changes - July 2015',
            'content' => '
                <h3>Stockrooms</h3>
                <p>Stockroom page has been simplified so it now just lists
                every item you have purchased, the most recent at the top. Items
                are no longer grouped, so you do not need to click into each product
                to view the keys. Instead, click the checkbox to the right of each
                item to select one or more. Then click the green "Items Picked for
                Delivery" button at the top, and either:</p>
                <ul>
                <li>Mark them as "Delivered"</li>
                <li>Send them by Email</li>
                <li>Print them</li>
                </ul>
                <p>
                As well as Marking/Emailing/Printing the keys, they will be moved
                out of your Stockroom and
                into your Order History area. You can always visit your Order History to
                see older keys.
                </p>

                <h3>Stockrooms - Buy More</h3>
                <p>You can now re-order directly from your Stockroom by clicking the
                "Buy More" button alongside any item in your stockroom. You must have
                enough credit in your account. Items are normally delivered instantly into you Stockroom.
                <h3>Shop</h3>
                You can now buy any available digital product directly from within
                Exertis Digital Stockroom. You can add any number of items as long as
                you have enough credit in your account. Items are normally delivered instantly into you Stockroom.
                </p>
                <h3>News</h3>
                <p>We\'ve added a section showing the latest updates and changes.
                ',
            // open its content by default
            //'contentOptions' => ['class' => 'in']
        ],


    ]

]);

?>