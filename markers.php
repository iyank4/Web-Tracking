<?php
session_start();

// loading include file *in this order*
require_once('inc.config.php');
require_once('inc.database.php');
require_once('inc.functions.php');

// check authentication
fAuth();

foreach($_SESSION['license'] as $r) {
    $lics.= "'".$r[1]."',";
}
$lics = substr($lics,0,-1);

$sql = "select license,latitude,longitude,TID,speed,course from tblatestlocation where license IN ($lics)";
$sth = $db->query($sql);
$rows = $sth->fetchAll(PDO::FETCH_ASSOC); 

$mobilList = array();
foreach($rows as $r) {
    //                0                   1              2               3          4           5
    $mobil = array($r['license'], $r['latitude'], $r['longitude'], $r['TID'], $r['speed'], $r['course']);
	$mobilList[] = $mobil;
}

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo "{ \"mobil\": ";
echo json_encode($mobilList);
echo "}";

?>