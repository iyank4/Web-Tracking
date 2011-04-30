<pre>
<?php
$ua111 = $_SERVER['HTTP_USER_AGENT'];

$browser = get_browser($ua111, true);
print_r($browser);
?>
