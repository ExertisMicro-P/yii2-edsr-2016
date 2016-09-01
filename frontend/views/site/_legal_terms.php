<?php
use cebe\markdown;

$parser = new \cebe\markdown\Markdown();
$parser->html5 = true;
?>
<!-- site/_legal_terms.php -->


<?php

$markdown = <<<TERMS
#Terms and Conditions

   "Site" means the Exertis Digital Stock Room web portal provided at http://stockroom.exertis.co.uk.

   "Exertis" means Exertis UK Ltd, Registered Office Address: Shorten Brook Way, Altham Business Park, Altham, Accrington, BB5 5YJ; Registered in England, Registration number: 1511931, VAT number: GB864438791

   This Site and service is provide free of charge and without warranty.

   Exertis make no guarantees as to its availablity or any of of its features being fit for purpose. It is provided as a useful tool and no more.

   It is the user's responsibility to:

   1. Take delivery of any license keys as soon as they are made available in the EDSR portal.
   1. Keep your own record of purchased license keys.
   1. Keep the details of any license key secret until it is used or activated.
   1. Verify that your order is for the product and quantity you require.
   1. Keep your login credentials used to access this site secret.
   1. Notify Exertis *immediately* if you suspect that your credentials have become known to a third party.
   1. Change your password regularly.
   1. Use this site in such a way that your actions do not degrade its performance or availability.
   1. Not use this site for criminal or illegal activities.

   Exertis are unable to cancel, refund or replace orders or license keys once an orders has been placed. It is your responsibility to check your order before placing it.

   Exertis makes no claim or warranty regarding the delivery of license keys via this service, or the time it takes to deliver license keys.

   Exertis reserves the right to change the features of the service, discontinue it or selectively prevent access to it, without notice and without giving reason.


   Additionally, you agree to the Terms and Conditions of our main website, and confirm that you have <a href="http://www.exertismicro-p.co.uk/cmcPage.asp?idPage=3551" target="_blank">[read them here]</a>.

   By using this site you agree to these conditions.

TERMS;

echo $parser->parse($markdown);
