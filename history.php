<?php
// desktop app
session_start();

// loading include file *in this order*
require_once('inc.config.php');
require_once('inc.database.php');
require_once('inc.functions.php');

// check authentication
fAuth();
if(isset($_GET['tid'])) {
    $tid = $_GET['tid'];
    $license = $_GET['license'];
} else {
    echo "Silakan pilih Kendaraan.";
    exit(0);
}

if(isset($_GET['newtab'])) {
    $newtab = ($_GET['newtab']=='true')?true:false;
}

// $tid = "358266019136469";
if($_cfg['db_driver'] == "mssql")
    $sql = "select top 53 lat,lon,[time] from tblocations where tid=".$tid." and fixgps=1 order by [time] desc";
else
    $sql = "select lat,lon,`time` from tblocations where tid=".$tid." and fixgps=1 order by `time` desc limit 53";

//$sth = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
//$sth->bindParam(':TID', $tid, PDO::PARAM_INT);
//$sth->execute();
$sth = $db->query($sql);
$rows = $sth->fetchAll(PDO::FETCH_ASSOC); 

foreach($rows as $r) {
    $rutenya[] = array($r['lat'], $r['lon']);
}

$curlat = (isset($rutenya[0][0]))?$rutenya[0][0]:$_cfg['map_init_lat'];
$curlon = (isset($rutenya[0][1]))?$rutenya[0][1]:$_cfg['map_init_lng'];

$rutenya = array_reverse($rutenya);

?><!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>GoLDtrack :: History Kendaraan</title>
    <link rel="stylesheet" type="text/css" href="media/desktop.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="media/redmond/jquery-ui-1.8.4.custom.css"media="screen" />
    
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript" src="media/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="media/jquery-ui-1.8.4.custom.min.js"></script>
    
	<script type="text/javascript">
        var histMap;
        
        var polyOptions;
        var poly;
        var path;
        
        var tmp_path;
        var tmp_path_index;
        
		$(document).ready(function() {
            var myLatlng = new google.maps.LatLng(<?php echo $curlat.",".$curlon; ?>);
            var myOptions = {
              zoom: 15,
              center: myLatlng,
              mapTypeId: google.maps.MapTypeId.ROADMAP
            }
            histMap = new google.maps.Map(document.getElementById("historydiv"), myOptions);
            
            polyOptions = {
                strokeColor: '#33ff33',
                strokeOpacity: 0.8,
                strokeWeight: 4
            }
            poly = new google.maps.Polyline(polyOptions);
            poly.setMap(histMap);
            path = poly.getPath();
            
            <?php
            if(isset($rutenya)) {
                foreach($rutenya as $rute) {
                    echo "path.push(new google.maps.LatLng(".$rute[0].",".$rute[1]."));";
                }
            }
            ?>

        });
        
        function jalankan() {
            var path = poly.getPath();
            tmp_path = path.getArray();
            tmp_path_index = 0;
            
            poly.setMap(null);
            poly = new google.maps.Polyline(polyOptions);
            
            poly.setMap(histMap);
            
            startAnimation();
        }
        
        function startAnimation() {
            path = poly.getPath();
            path.push(tmp_path[tmp_path_index]);
            histMap.panTo(tmp_path[tmp_path_index]);
            tmp_path_index++;
            if(tmp_path_index < tmp_path.length) {
                setTimeout('startAnimation()', 400);
            }
        }

	</script>
    <style>
        #label2 {
            position: absolute;
            z-index: 100;
        }
        #labelnya {
            background-color: #ffffff;
            text-align: center;
            padding: 3px 12px;
        }
        #historydiv {
            width: 100%;
            height: 448px;
        }
    </style>
    
</head>
<body>
<div id="label2"></div>
<div id="labelnya">
    History <strong>'<?php echo $license; ?>'</strong>
    | <strike>Set Tanggal <input type="text" name="tgl_awal" value="<?php echo date('Y-m-d'); ?> 00:00:00" />
    s.d <input type="text" name="tgl_awal" value="<?php echo date('Y-m-d'); ?> 23:59:59" />
    <input type="button" name="btn_eksekusi" value=" Tampilkan " />
    </strike>
    <input type="button" name="btn_anim" value=" Jalankan " onclick="jalankan();" />
</div>

<div id="historydiv">History...</div>
</body>
</html>
