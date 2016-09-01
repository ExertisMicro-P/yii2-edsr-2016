<?php
    $user = $users[0] ;
?>

<h3 xmlns="http://www.w3.org/1999/html">Customer <?= $user->account->customer->name?></h3>
<h4>You are about to log into the shop as <?= $user->email ?></h4>

<h3>If this is the correct user, you can enter the shop <a href="/dashboard/masquerade?id=<?= $user->id ?>">here</a></h3>
