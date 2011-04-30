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
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>GoLDtrack :: Web-Based Tracking</title>
    <link rel="stylesheet" type="text/css" href="media/desktop-inner.css" media="screen" />
    
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    
	<script type="text/javascript">
    </script>
</head>
<!--
<table class='infobalon'>
  <tr>
    <th>Waktu</td>
    <th>Muatan</td>
    <th>Arah</td>
    <th>Mesin</td>
    <th>Kecepatan</td>
  </tr>
  <tr>
    <td>00</td>
    <td>-</td>
    <td>-</td>
    <td>-</td>
    <td>0 Km/jam</td>
  </tr>
</table>
-->
<?php

echo "No Polisi : ".$_GET['license']." <br />";
echo "Latitude  : ".$_GET['lat']." <br />";
echo "Longitude : ".$_GET['lng']." <br />";

?>
</body>
</html>