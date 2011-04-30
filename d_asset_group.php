<?php
// desktop app
session_start();

// loading include file *in this order*
require_once('inc.config.php');
require_once('inc.database.php');
require_once('inc.functions.php');

// check authentication
fAuth();
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Customer Aset Group</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="media/desktop-inner.css" media="screen" />
    
</head>
<body>
<h2>Asset Group</h2>
<hr />
<?php
echo dbCustAssetGroup();
?>
</body>
</html>