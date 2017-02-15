<?php
//CONNECT to DB
include('db-connect.inc.php');

function make_seed()
{
  list($usec, $sec) = explode(' ', microtime());
  return (float) $sec + ((float) $usec * 100000);
}
mt_srand(make_seed());

$sql_query_comments = 'SELECT * FROM `donations` WHERE `processed` = 1 AND `target` = "donate2016" ORDER BY timestamp desc';
$sql_result = $_DB_H->query($sql_query_comments) OR die('FAIL UPDATING: '.$sql_query_comments);
$fp = fopen('../data/donors-eur.csv', 'w');
$count=0;
fputcsv($fp, array('name','amount','currency','amount_eur','message','premium', 'timestamp')) OR die('FAILED writing header.');

$randval = mt_rand(0, 1);

// $launch_partners = array(array('Mapbox','It\'s a pleasure to chip in for this funding drive. Keep up the good work!'),array('Mapzen','Mapzen is happy to support the great work of the Operations Working Group!'));

// fputcsv($fp, array($launch_partners[$randval][0],'20000','USD','13046.19',$launch_partners[$randval][1],'true')) OR die('FAILED writing first partner line.');
// fputcsv($fp, array($launch_partners[1-$randval][0],'20000','USD','13046.19',$launch_partners[1-$randval][1],'true')) OR die('FAILED writing second partner line.');
$sql_query_matching_comments = 'SELECT sum(`amount_gbp`) `amount_for_matching_gbp` FROM `donations` WHERE `processed` = 1 AND `target` = "donate2016" AND `timestamp` >= "2016-11-11 23:00:00"';
$sql_matching_result = $_DB_H->query($sql_query_matching_comments) OR die('FAIL UPDATING: '.$sql_query_matching_comments);
$contrib_matching = $sql_matching_result->fetch_assoc() OR die('FAIL RESULT: '.$sql_query_matching_comments);
$contrib_matching_amount_eur = $contrib_matching['amount_for_matching_gbp'] * 1.17987;
if ($contrib_matching_amount_eur > 10000) $contrib_matching_amount_eur = 10000;
fputcsv($fp, array('Mapbox',number_format($contrib_matching_amount_eur, 2, '.', ''),'EUR',number_format($contrib_matching_amount_eur, 2, '.', ''),'Mapbox matched donations','true', date('Y-m-d H:i:s'))) OR die('FAILED writing first partner line.');

if ($sql_result AND $sql_result->num_rows > 0) {
  while($contrib = $sql_result->fetch_assoc()) {
    $count++;
    $name = $contrib['anonymous'] ? 'Anonymous' : $contrib['name'];
    // CSV looks like this:
    // name:str, amount:float, currency:str, amount_gbp:float, message:str, premium:bool
    fputcsv($fp, array($name, $contrib['amount'], $contrib['currency'], number_format(($contrib['amount_gbp'] * 1.17987), 2, '.', ''), $contrib['comment'], '', $contrib['timestamp'])) OR die('FAILED writing row');
  }
}
fclose($fp);
