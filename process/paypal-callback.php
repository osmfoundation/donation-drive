<?php
// Extensively based on Very_Horrible Paypal sample code.

ob_start();

$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
  $keyval = explode ('=', $keyval);
  if (count($keyval) == 2)
     $myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
   $get_magic_quotes_exists = true;
}
foreach ($myPost as $key => $value) {
   if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
        $value = urlencode(stripslashes($value));
   } else {
        $value = urlencode($value);
   }
   $req .= "&$key=$value";
}

// post back to PayPal system to validate
$ch = curl_init('https://www.paypal.com/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

// In wamp like environments that do not come bundled with root authority certificates,
// please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path
// of the certificate as shown below.
// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
if( !($res = curl_exec($ch)) ) {
    error_log("Got " . curl_error($ch) . " when processing IPN data");
    curl_close($ch);
    exit;
}
curl_close($ch);

// assign posted variables to local variables
$item_name			= get_magic_quotes_gpc() ? stripslashes($_REQUEST['item_name'])		: $_REQUEST['item_name'] ;
$item_number		= get_magic_quotes_gpc() ? stripslashes($_REQUEST['item_number'])		: $_REQUEST['item_number'];
$payment_status		= get_magic_quotes_gpc() ? stripslashes($_REQUEST['payment_status'])	: $_REQUEST['payment_status'];
$payment_amount		= get_magic_quotes_gpc() ? stripslashes($_REQUEST['mc_gross'])			: $_REQUEST['mc_gross'];
$payment_currency	= get_magic_quotes_gpc() ? stripslashes($_REQUEST['mc_currency'])		: $_REQUEST['mc_currency'];
$txn_id				= get_magic_quotes_gpc() ? stripslashes($_REQUEST['txn_id'])			: $_REQUEST['txn_id'];
$receiver_email		= get_magic_quotes_gpc() ? stripslashes($_REQUEST['receiver_email'])	: $_REQUEST['receiver_email'];
$payer_email		= get_magic_quotes_gpc() ? stripslashes($_REQUEST['payer_email'])		: $_REQUEST['payer_email'];
$business			= get_magic_quotes_gpc() ? stripslashes($_REQUEST['business'])			: $_REQUEST['business'];
$option_selection1	= get_magic_quotes_gpc() ? stripslashes($_REQUEST['option_selection1']) : $_REQUEST['option_selection1'];
$option_name1		= get_magic_quotes_gpc() ? stripslashes($_REQUEST['option_name1'])		 : $_REQUEST['option_name1'];

$first_name			= get_magic_quotes_gpc() ? stripslashes($_REQUEST['first_name'])		 : $_REQUEST['first_name'];
$last_name			= get_magic_quotes_gpc() ? stripslashes($_REQUEST['last_name'])		 : $_REQUEST['last_name'];

$full_name			= $first_name.' '.$last_name;


//if (!$fp) {
//	// HTTP ERROR
//	error_log('Verify Failed Callback: '.var_export($_REQUEST, TRUE));
//} else {
//	fputs ($fp, $header . $req);
//	while (!feof($fp)) {
//		$res = fgets ($fp, 1024);
		if (strcmp ($res, 'VERIFIED') == 0) {
			// check the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your Primary PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment

			//CONNECT to DB
			include('../scripts/db-connect.inc.php');

			if ($payment_status == 'Completed' AND $option_name1=='contribution_tracking_id' AND $business == 'treasurer@openstreetmap.org') {
				if ($payment_currency=='GBP') {
					$payment_amount_gbp = $payment_amount;
				} else {
					$payment_amount_gbp = 0;
					$sql_query_exc_rate = 'SELECT `rate` FROM `currency_rates` WHERE `currency`="'.$payment_currency.'" LIMIT 1';
					$sql_result = mysql_query($sql_query_exc_rate, $_DB_H) OR error_log('FAIL UPDATING: '.$sql_query_exc_rate);
					if ($sql_result AND mysql_num_rows($sql_result)==1) {
						$exc_rate = mysql_fetch_array($sql_result ,MYSQL_ASSOC);
						$payment_amount_gbp = $payment_amount / $exc_rate['rate'];
					}
				}
				$sql_update_donation = 'UPDATE `donations` SET `processed` = 1, '.
										'`amount_gbp` = "'.$payment_amount_gbp.'", '.
										'`name` = \''.mysql_real_escape_string($full_name, $_DB_H).'\''.
										'WHERE `uid`="'.mysql_real_escape_string($option_selection1, $_DB_H).'" LIMIT 1';
				mysql_query($sql_update_donation, $_DB_H) OR error_log('SQL FAIL: '.$sql_update_donation);
			}

			$sql_insert_callback = 'INSERT INTO `paypal_callbacks` (`amount`, `currency` , `status`, `donation_id`, `callback`) VALUES (\''.
									mysql_real_escape_string($payment_amount, $_DB_H).'\',\''.
									mysql_real_escape_string($payment_currency, $_DB_H).'\',\''.
									mysql_real_escape_string($payment_status, $_DB_H).'\',\''.
									mysql_real_escape_string($option_selection1, $_DB_H).'\',\''.
									mysql_real_escape_string(serialize($_REQUEST), $_DB_H).
									'\')';
			mysql_query($sql_insert_callback, $_DB_H) OR error_log('SQL FAIL: '.$sql_insert_callback);
		} else if (strcmp ($res, 'INVALID') == 0) {
			// log for manual investigation
			error_log('Invalid Callback: '.var_export($_REQUEST, TRUE));
		}
//	}
//	fclose ($fp);
///}
