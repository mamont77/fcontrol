<?php

// Param should be changed to 0 in production.
error_reporting(E_ALL);

ini_set('upload_max_filesize', '16M');
ini_set('post_max_size', '20M');

if (!defined('PATH_SEPARATOR')) define('PATH_SEPARATOR', getenv('COMSPEC') ? ';' : ':');
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . dirname(__FILE__));

// {{{ Includes
include_once "DbSimple/Generic.php";
$DB1 = DbSimple_Generic::connect("mysql://root:@localhost/test");
$DB2 = DbSimple_Generic::connect("mysql://root:@localhost/fcontrol");
mysql_query("set names 'utf8'");

// Устанавливаем обработчик ошибок.
$DB1->setErrorHandler('databaseErrorHandler');
$DB2->setErrorHandler('databaseErrorHandler');
//$DB->setLogger('myLogger');

// Код обработчика ошибок SQL.
function databaseErrorHandler($message, $info)
{
    // Если использовалась @, ничего не делать.
    if (!error_reporting()) return;
    // Выводим подробную информацию об ошибке.
    echo "SQL Error: $message<br><pre>";
    print_r($info);
    echo "</pre>";
    exit();
}

//test tables

//$count = $DB1->selectCell('SELECT COUNT(*) FROM a_country');
//print_r($count);
//print "\r\n";
//$count = $DB2->selectCell('SELECT COUNT(*) FROM library_country');
//print_r($count);
//print "\r\n";
//$count = $DB1->selectCell('SELECT COUNT(*) FROM a_city');
//print_r($count);
//print "\r\n";
//$count = $DB2->selectCell('SELECT COUNT(*) FROM library_city');
//print_r($count);
//print "\r\n";
//$count = $DB1->selectCell('SELECT COUNT(*) FROM a_airports');
//print_r($count);
//print "\r\n";
//$count = $DB2->selectCell('SELECT COUNT(*) FROM library_airport');
//print_r($count);
//print "\r\n";
//
//$count = $DB1->select('SELECT * FROM a_airports WHERE id=3671');
//print_r($count);
//print "\r\n";
//$count = $DB2->select('SELECT * FROM library_airport WHERE id=3671');
//print_r($count);
//print "\r\n";
//
//exit();

//end test tables

$DB2->query('TRUNCATE TABLE library_country');
$result = $DB1->select('SELECT * FROM a_country');
foreach ($result as $item) {
    //echo'<pre>';print_r($result);echo'</pre>';
    $DB2->query('INSERT INTO library_country (id, region_id, name, code)
        VALUES ("' . $item['id'] . '", 6, "' . $item['country'] . '", "' . $item['iso'] . '")');
}

$DB2->query('TRUNCATE TABLE library_city');
$result = $DB1->select('SELECT * FROM a_city');
foreach ($result as $item) {
    //echo'<pre>';print_r($result);echo'</pre>';
    $DB2->query('INSERT INTO library_city (id, country_id, name)
        VALUES ("' . $item['id'] . '", "' . $item['country_id'] . '", "' . $item['city'] . '")');
}

$DB2->query('TRUNCATE TABLE library_airport');
$result = $DB1->select('SELECT * FROM a_airports');
foreach ($result as $item) {
    //echo'<pre>';print_r($result);echo'</pre>';
    $DB2->query('INSERT INTO library_airport (id, city_id, name, short_name, code_icao, code_iata, latitude, longitude)
        VALUES ("' . $item['id'] . '", "' . $item['city_id'] . '", "' . $item['name'] . '", "' . $item['name'] . '", "' . $item['icao'] . '", "' . $item['iata'] . '", "' . $item['latitude'] . '", "' . $item['longitude'] . '")');
}
