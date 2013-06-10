<?php

// Param should be changed to 0 in production.
error_reporting(E_ALL);

ini_set('upload_max_filesize', '16M');
ini_set('post_max_size', '20M');

if (!defined('PATH_SEPARATOR')) define('PATH_SEPARATOR', getenv('COMSPEC')? ';' : ':');
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

$DB2->query('TRUNCATE TABLE library_country');
$countries = $DB1->select('SELECT * FROM a_country');
foreach ($countries as $country) {
    //echo'<pre>';print_r($country);echo'</pre>';
    $DB2->query('INSERT INTO library_country (id, region, name, code)
        VALUES ("' . $country['id'] . '", 6, "' . $country['country'] . '", "' . $country['iso'] . '")');
}

$DB2->query('TRUNCATE TABLE library_airport');
$airports = $DB1->select('SELECT
    a_airports.id as id,
    a_city.id as city_id,
    a_airports.name,
    a_airports.iata,
    a_airports.icao,
    a_airports.latitude,
    a_airports.longitude,
    a_city.country_id as country
    FROM a_airports
    JOIN a_city
    ON a_airports.city_id = a_city.id');
foreach ($airports as $airport) {
    echo'<pre>';print_r($airport);echo'</pre>';
    $DB2->query('INSERT INTO library_airport (id, country, name, short_name, code_icao, code_iata, city_id, latitude, longitude)
        VALUES ("' . $airport['id'] . '", "' . $airport['country'] . '", "' . $airport['name'] . '", "' . $airport['name'] . '", "' . $airport['icao'] . '", "' . $airport['iata'] . '", "' . $airport['city_id'] . '", "' . $airport['latitude'] . '", "' . $airport['longitude'] . '")');
}



