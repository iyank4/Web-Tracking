<?php
session_start();

// loading include file *in this order*
require_once('inc.config.php');
require_once('inc.database.php');
require_once('inc.functions.php');

// check authentication
fAuth();
$lics = substr($lics,0,-1);

$ret = "<table id=\"alarm\">"
      ."    <thead>"
      ."    <tr>"
      ."        <th>License</th>"
      ."        <th>Nama Jalan</th>"
      ."        <th>Waktu</th>"
      ."        <th>Speed</th>"
      ."        <th>Kontak</th>"
      ."        <th>Arah</th>"
      ."        <th>Tegangan</th>"
      ."    </tr>"
      ."    </thead>"
      ."    <tbody>";
if(count($_SESSION['license']) > 0 ) {
    try {
        foreach($_SESSION['license'] as $r) {
            $lics.= "'".$r[1]."',";
        }
        $lics = substr($lics,0,-1);

        if($_cfg['db_driver'] == "mysql") {
            $sql = "select license,latitude,longitude,DATE_FORMAT(DATE_ADD(`time`, INTERVAL 7 HOUR),'%d/%m/%y') as tanggal,DATE_FORMAT(DATE_ADD(`time`, INTERVAL 7 HOUR),'%H:%i:%s') as jam,speed,course,`input power` as power,`ignition on` as kontak "
                  ."from tblatestlocation where license  IN ($lics) order by `time` desc";
        } else {      
            $sql = "select license,latitude,longitude,CONVERT(VARCHAR(8), DATEADD(hour, 7,time), 3) AS tanggal,CONVERT(VARCHAR(8), DATEADD(hour, 7,time), 108) AS jam,speed,course,[input power] as power,[ignition on] as kontak "
                  ."from tblatestlocation where license  IN ($lics) order by time desc";
        }
        
        $sth = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute();
        $rows = $sth->fetchAll(PDO::FETCH_ASSOC); 

        if(count($rows) > 0 ) {
            foreach($rows as $r) {
                $alamat = fAlamat($r['latitude'],$r['longitude']);
                
                $ret .= "<tr>"
                        ."  <td><span onclick=\"temukan('".$r['license']."');\" style=\"cursor:pointer;\">".$r['license']."</span></td>"
                        ."  <td>".$alamat["alamat"]."</td>"
                        ."  <td align=\"center\">".$r['tanggal']." ".$r['jam']."</td>"
                        ."  <td align=\"right\">".round($r['speed'],0)." km/jam</td>"
                        ."  <td align=\"center\">".(($r['kontak']=='0')?'OFF':'ON')."</td>"
                        ."  <td align=\"center\">".fArah($r['course'])."</td>"
                        ."  <td align=\"center\">".dbBaterai($r['power'])."</td>"
                        ."</tr>";
            }
        }
    } catch (PDOException $e) {
        print $e->getMessage();
        exit;
    }
}
$ret .= "</tbody>";
$ret .= "</table>";

echo $ret;


?>
