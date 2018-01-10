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
