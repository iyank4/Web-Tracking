<?php
// login execuable
session_start();

// loading include file *in this order*
require_once('inc.config.php');
require_once('inc.database.php');
require_once('inc.functions.php');


$uname = fCleanInput($_POST['username']);
$passw = fCleanInput($_POST['password']);

$ret = dbCheckLogin($uname, $passw);
if($ret == "prikitiwww") {
    dbUserLogin($uname);
    header("location: index.php");
} else {
    header("location: ".$_POST['ref'].".php?error=$ret");
}

?>