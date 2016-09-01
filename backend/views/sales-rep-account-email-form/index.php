<?php
/* @var $this yii\web\View */

use backend\models\SalesRepUserEmailSetupForm;
use kartik\widgets\Alert;
use yii\helpers\VarDumper;

$session = Yii::$app->session;
?>

<h1><?php echo Yii::t('app', 'Setup New EDSR Account Email Details') ?></h1>


<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
  Help
</button>
<div class="collapse" id="collapseExample">
  <div class="well">
    <p>Type in the account number. If the account is not displayed, it means it is 
    already setup in EDSR. Be careful to look for the warnings about Account Status!</p>
<p>If a matching account is displayed, enter the email address of the first user 
    (the Exertis customer's email address).</p>
<p>EDSR will try to check if the account is already "EDI Ready" in Oracle. 
    If it isn't, you will need to choose an EDI Sales Rep, or enter the Rep name 
    manually. Finance will be sent an email aksing them to check the account in Oracle. 
    The Shop will be enabled once accounts have confirmed that the account is setup correctly.</p>
  </div>
</div>





<?php
	$flashes = $session->getFlash('error');
	if ($flashes) {
		//echo('here='.print_r($flashes,true));
		//die();
		$flashes = is_array($flashes) ? $flashes : array($flashes);
	    foreach ($flashes as $alert) {
	        echo Alert::widget([
	            'type' => Alert::TYPE_DANGER,
	            'title' => 'Not Saved!',
	            'icon' => 'glyphicon glyphicon-ok-sign',
	            'body' => $alert,
	            'showSeparator' => true,
	            'delay' => 20000
	        ]);
	    }  // foreach
	} // if

	$flashes = $session->getFlash('success');
	if ($flashes) {
		$flashes = is_array($flashes) ? $flashes : array($flashes);
	    foreach ($flashes as $alert) {
	        echo Alert::widget([
	            'type' => Alert::TYPE_SUCCESS,
	            'title' => 'Saved!',
	            'icon' => 'glyphicon glyphicon-exclamation-sign',
	            'body' => $alert,
	            'showSeparator' => true,
	            'delay' => 20000
	        ]);
	    } // foreach
	} // if


echo $this->render('_form', [
    'model' => $model,
    //'account_typeahead_data' => $account_typeahead_data,
]);
?>
