<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<title>GoldTrack :: Web-Based Tracking - by GoLD track</title>
    <link rel="stylesheet" type="text/css" href="media/style.css">
</head>
<body>

<form name="frmLogin" action="loginx.php" method="post">
<input type="hidden" name="ref" value="desktoplogin" />
<div id="loginbox">
	<div style="background-color: #fff">
        <br />
		<img src="media/logo.gif" alt="Logo" />
	</div>
	<br />
	<?php
		if($_GET['error'] == 1)
			echo "<div class=\"error\">User tidak ditemukan</div>";
		if($_GET['error'] == 2)
			echo "<div class=\"error\">Password yang Anda masukkan Salah.</div>";	
	?>
	<table>
		<tr>
			<td>UserName</td>
			<td>: <input type="text" name="username" size="16" /></td>
		</tr>
		<tr>
			<td>Password</td>
			<td>: <input type="password" name="password" size="16" /></td>
		</tr>
		<tr>
			<td colspan="2">
				<br />
				<input type="submit" value="   Login   &raquo;   " style="height:30px;" />
			</td>
		</tr>
	</table>
	<br />
	<a href="http://www.gold-track.com/samator/">Demo Integration</a>
	<br />
	<br />
</div>
</form>


</body>
</html>