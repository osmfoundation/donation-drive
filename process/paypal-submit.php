<?php
/* Paypal Submission
// FIXME add paypal language support
lc = AU, DE FR IT GB ES US
*/

ob_start();

//CONNECT to DB
include('../scripts/db-connect.inc.php');

$data = array();
$data['comment']		= $_POST['comment'];
$data['comment-option']	= $_POST['comment-option'];
$data['anonymous'] 		= ($data['comment-option'] == 'comment' ? '0' : '1');
$data['amount']			= $_POST['amountGiven'];
$data['currency']		= $_POST['currency_code'];

$data['target']		= $_POST['target'];
if (empty($data['target'])) $data['target'] = 'default';

$sql_insert =	'INSERT INTO `donations` (`amount_gbp`,`amount`,`currency`,`anonymous`,`comment`,`target`) VALUES (\''.
					'0'.'\',\''.
					$_DB_H->real_escape_string($data['amount']).'\',\''.
					$_DB_H->real_escape_string($data['currency']).'\',\''.
					$_DB_H->real_escape_string($data['anonymous']).'\',\''.
					$_DB_H->real_escape_string($data['comment']).'\',\''.
					$_DB_H->real_escape_string($data['target']).
				'\')';
$sql_insert_result = $_DB_H->query($sql_insert) OR error_log('SQL FAIL: '.$sql_insert);
$sql_insert_id = $_DB_H->insert_id;

if (!$sql_insert_id) die('Error creating donation tracking ID');

$_PAYPAL_URL = 'https://www.paypal.com/cgi-bin/webscr';

$paypal_fields = array();
$paypal_fields['on0']			= 'contribution_tracking_id';
$paypal_fields['os0']			= $sql_insert_id;
$paypal_fields['business']		= 'treasurer@openstreetmap.org';
$paypal_fields['item_number']	= 'DONATE';
$paypal_fields['cmd']			= '_xclick';
$paypal_fields['no_note']		= '0';
$paypal_fields['notify_url']	= 'https://donate.openstreetmap.org/process/paypal-callback.php';
$paypal_fields['return']		= 'https://wiki.openstreetmap.org/wiki/DonationThankYou';
$paypal_fields['currency_code']	= (empty($data['currency']) ?  'GBP' : $data['currency']);
$paypal_fields['charset']		= 'utf-8';
if (isset($_POST['monthly'])) {
  $paypal_fields['item_name'] = 'Monthly donation';
  $paypal_fields['a3']  = (double) $data['amount'];
  $paypal_fields['p3']  = '1';  # 1 = every
  $paypal_fields['t3']  = 'M';  # M = month
  $paypal_fields['src'] = '1';  # means recurring until cancelled
} else {
  $paypal_fields['item_name']		= 'One-time donation';
  $paypal_fields['amount']		= (double) $data['amount'];
}
$pp_url_part=array();
foreach($paypal_fields AS $pp_key => $pp_value) {
	$pp_url_part[] = $pp_key.'='.urlencode($pp_value);
}
header('Location: '.$_PAYPAL_URL.'?'.implode('&',$pp_url_part));
exit();
