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
    <title>Customer Aset List</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="media/desktop-inner.css" media="screen" />
    
</head>
<body>
<h2>Asset List</h2>
<hr />
<?php
echo "<table id=\"tbldata\">";
echo "<tr>
        <th>ID</th>
        <th>License Number</th>
      </tr>";
foreach($_SESSION['license'] as $v) {
    echo "<tr>
            <td>$v[0]</td>
            <td>$v[1]</td>
          </tr>";
}
echo "</table>";

?>
</body>
</html>