<?php
// function call for interface to database

$db = dbConnect($_cfg['db_driver'],$_cfg['db_host'], 
                $_cfg['db_user'],$_cfg['db_password'], $_cfg['db_database']);

if(!$db) {
    echo "<h2>Database connection failed.</h2>";
    exit;
}

// koneksi ke database
function dbConnect($dbdriver,$dbhost, $dbuser,$dbpass, $dbdatabase) {
    $pdo_conn = false;
    $DSN = $dbdriver.":host=".$dbhost.";dbname=".$dbdatabase;

    try {
        $pdo_conn = new PDO($DSN, $dbuser, $dbpass);
    } catch(PDOException $e) {
        //echo "Failed on Database Connection:<br>".$e->getMessage();
        //exit;
    }
    
    return $pdo_conn;
}


/* Authentication */
function dbCheckLogin($uname, $passw) {
    
    $passw = sha1(md5($passw));
    
    $sql = "select newpass from tbusers where userName like '$uname'";
    $row = dbFirstRow($sql);

    if($row) {
		// kalo usernya ada
		if($passw == $row['newpass'])
			$ret = "prikitiwww";
		else
			$ret = "2";
	} else {
		$ret = "1"; // user ngga ada
	}
	
	/*
	$ret: 
	1 = user ngga ada
	2 = password salah
	*/
	return $ret;
}

function dbUserLogin($username){
    
    $_SESSION['username'] = $username;
    $sql = "select userGroupID from tbusers where userName='$username'";
    $row = dbFirstRow($sql);
    $_SESSION['userGroupID'] = $row['userGroupID'];
    
    $sql = "select minNo,maxNo from tbusergroup where userGroupID = ".$row['userGroupID'];
    $row = dbFirstRow($sql);
    $_SESSION['minNo'] = $row['minNo'];
    $_SESSION['maxNo'] = $row['maxNo'];
    
    dbLicenseToSession();
    
    // that is!
}


/* Database Abstraction */

// mengambil baris pertama dari query
function dbFirstRow($sql) {
    global $db;
    
    $aq = $db->prepare($sql);
    $aq->execute();
    $row = $aq->fetch(PDO::FETCH_ASSOC);
    
    return $row;
}

// mengambil data mobil yang dimiliki user dan menyimpannya ke dalam session
function dbLicenseToSession() {
    global $db;
    
    $sql = "select customerID, license from tblicensetran where customerID >= :minNo AND "
          ."customerID <= :maxNo AND endTime >= :endTime ORDER BY license";
      
    $sth = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->bindParam(':minNo', $_SESSION['minNo'], PDO::PARAM_INT);
    $sth->bindParam(':maxNo', $_SESSION['maxNo'], PDO::PARAM_INT);
    $sth->bindParam(':endTime', date("Y-m-d H:i:s"), PDO::PARAM_STR);
    $sth->execute();

    $rows = $sth->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $r)
        $_SESSION['license'][] = array($r['customerID'],$r['license']);
}


/* Customer Tree Menu */
function dbCustAssetTree($idname) {
    global $db;
    try {
        $sql = "select usergroupid,des,minno,maxno from tbusergroup where minno >= :minNo and maxno <= :maxNo order by minno asc, maxno desc";
        $sth = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindParam(':minNo', $_SESSION['minNo'], PDO::PARAM_INT);
        $sth->bindParam(':maxNo', $_SESSION['maxNo'], PDO::PARAM_INT);
        $sth->execute();
        $rows = $sth->fetchAll(PDO::FETCH_ASSOC); 

        foreach($rows as $r) {
            $cust_asset[] = array($r['usergroupid'], $r['des'], $r['minno'], $r['maxno']);
        }
    } catch (PDOException $e) {
        print $e->getMessage();
        exit;
    }
    
    $items = $cust_asset;

    $root = _getRoot($items);
    $ret = $items[$root][1];
    $menu = "<ul id=\"$idname\">\n"
           ."<li><a href=\"javascript:resetMap();\"><strong>".$ret."</strong></a><br />";   
    $menu .= _subTree($root, $items);
    $menu .= "</li>"
            ."</ul>";
    return $menu;
}

/* Customer Asset List Array */
function dbCustAsset() {
    global $db;
    try {
        $sql = "select usergroupid,des,minno,maxno from tbusergroup where minno >= :minNo and maxno <= :maxNo order by minno asc, maxno desc";
        $sth = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindParam(':minNo', $_SESSION['minNo'], PDO::PARAM_INT);
        $sth->bindParam(':maxNo', $_SESSION['maxNo'], PDO::PARAM_INT);
        $sth->execute();
        $rows = $sth->fetchAll(PDO::FETCH_ASSOC);
        foreach($rows as $r) {
            $cust_asset[] = array($r['usergroupid'], $r['des'], $r['minno'], $r['maxno']);
        }
    } catch (PDOException $e) {
        print $e->getMessage();
        exit;
    }
    
    return $cust_asset;
}

/* Customer Asset Group Array */
function dbCustAssetGroup() {
    global $db;
    try {
        $sql = "select usergroupid,des,minno,maxno from tbusergroup where minno >= :minNo and maxno <= :maxNo order by minno asc, maxno desc";
        $sth = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindParam(':minNo', $_SESSION['minNo'], PDO::PARAM_INT);
        $sth->bindParam(':maxNo', $_SESSION['maxNo'], PDO::PARAM_INT);
        $sth->execute();
        $rows = $sth->fetchAll(PDO::FETCH_ASSOC); 

        foreach($rows as $r) {
            $cust_asset[] = array($r['usergroupid'], $r['des'], $r['minno'], $r['maxno']);
        }
    } catch (PDOException $e) {
        print $e->getMessage();
        exit;
    }
    
    $items = $cust_asset;

    $root = _getRoot($items);
    $ret = $items[$root][1];
    $menu = "<ul id=\"$idname\">\n"
           ."<li><strong>".$ret."</strong><br />";   
    $menu .= _subTree_group($root, $items);
    $menu .= "</li>"
            ."</ul>";
    return $menu;
}

function _getRoot($items) {
    $ide = NULL;
    $emin = NULL;
    $emax = NULL;
    foreach($items as $k=>$d) {
        if($emin == NULL) $emin = $d[2];
        if($emax == NULL) $emax = $d[3];
        if($d[3] >= $emax && $d[2] <= $emin) {
            $emax = $d[3];
            $emin = $d[2];
            $ide = $k;
        }
    }
    return $ide;
}

function _subTree($root, $items, $depth=5) {
    $min = $items[$root][2];
    $max = $items[$root][3];
    
    $depth--;
    $hasil = false;
    
    $leaf = _leaf_first($root, $min, $max, $items);
    if($leaf) {
        $hasil = "\n<ul>\n";
        while($leaf) {
            $nama  = $items[$leaf][1];
            $hasil.= "\t<li>$nama";
            if($depth > 0) {  // antisipasi jika data error dan terjadi unlimited recrussion
                $ret = _subTree($leaf, $items, $depth);
                if($ret)
                    $hasil.= $ret;
                else
                    $hasil.= _printMenuLicense($items[$leaf][2],$items[$leaf][3]);
            }
            $hasil.= "</li>\n";
            $leaf = _leaf_next($leaf, $min, $max, $items);        
        }
        $hasil.= "</ul>\n\t";
    } else {
        $hasil = _printMenuLicense($items[$root][2],$items[$root][3]);
    }
    
    return $hasil;
}

function _subTree_group($root, $items, $depth=5) {
    $min = $items[$root][2];
    $max = $items[$root][3];
    
    $depth--;
    $hasil = false;
    
    $leaf = _leaf_first($root, $min, $max, $items);
    if($leaf) {
        $hasil = "\n<ul>\n";
        while($leaf) {
            $nama  = $items[$leaf][1];
            $hasil.= "\t<li>$nama";
            if($depth > 0) {  // antisipasi jika data error dan terjadi unlimited recrussion
                $ret = _subTree_group($leaf, $items, $depth);
                if($ret)
                    $hasil.= $ret;
            }
            $hasil.= "</li>\n";
            $leaf = _leaf_next($leaf, $min, $max, $items);        
        }
        $hasil.= "</ul>\n\t";
    }
    
    return $hasil;
}

function _leaf_next($pref_leaf, $min, $max, $items) {
    $ret = false;
    $mimin = null;
    $mamax = null;
    
    $lastmax = $items[$pref_leaf][3];
    
    foreach($items as $k=>$v) {
        if($v[2] > $lastmax && $v[3] <= $max) {
            if($mamax == null) {
                $mamax = $v[3];
                $ret = $k;
            }
            if($v[3] < $mamax && $mamax <= $v[2]) {
                $mamax = $v[3];
                $ret = $k;
            }
        }
    }
    
    return $ret;
}

function _leaf_first($root, $min, $max, $items) {
    $ret = false;
    
    $mimin = null;
    $mamax = null;
    foreach($items as $k=>$v) {
        if($v[2] >= $min && $v[3] < $max && $k != $root) {    // jika dalam range data dan bukan rootnya
            if($mamax == null) {    // data yang ketemu pertama
                $mimin = $v[2];
                $mamax = $v[3];
                $ret = $k;
                break;
            }
        }
    }
    
    return $ret;
}

function _printMenuLicense($min,$max) {
    $mel = "<ul>";
    if(count($_SESSION['license']) > 0 ) {
        foreach($_SESSION['license'] as $v) {
            if($v[0] >= $min && $v[0] <= $max)
                $mel.= "<li onclick=\"temukan('".$v[1]."');\">".$v[1]."</li>";
        }
    } else {
        $mel.= "N/A";
    }
    $mel.= "</ul>";
    
    return $mel;
}

function dbBaterai($dc) {
	$sql = "select ac from tbadc where DC='$dc'";
	$r = dbFirstRow($sql);
    if (!empty($r)) {
        $baterai = $r["ac"];
	} else {
        $baterai = "0";
	}
    
    return $baterai;
}

// Alarm
function dbAlarm($idname) {
    global $db;
    foreach($_SESSION['license'] as $r) {
        $lics.= "'".$r[1]."',";
    }
    $lics = substr($lics,0,-1);

    $ret = "<table id=\"$idname\">"
          ."    <thead>"
          ."    <tr>"
          ."        <th>License</th>"
          ."        <th>Nama Jalan</th>"
          ."        <th>Waktu</th>"
          ."        <th>Kecepatan</th>"
          ."        <th>Arah</th>"
          ."        <th>Tegangan Aki</th>"
          ."    </tr>"
          ."    </thead>"
          ."    <tbody>";
    try {
        foreach($_SESSION['license'] as $r) {
            $lics.= "'".$r[1]."',";
        }
        $lics = substr($lics,0,-1);

        $sql = "select license,latitude,longitude,`time` as waktu,speed,course,`input power` as power "
              ."from tblatestlocation where license IN ($lics)";
        $sth = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->execute();
        $rows = $sth->fetchAll(PDO::FETCH_ASSOC); 

        foreach($rows as $r) {
            $alamat = fAlamat($r['latitude'],$r['longitude']);
            
            $ret .= "<tr>"
                    ."  <td>".$r['license']."</td>"
                    ."  <td>".$alamat["alamat"]."</td>"
                    ."  <td>".$r['waktu']."</td>"
                    ."  <td>".round($r['speed'],0)."</td>"
                    ."  <td>".fArah($r['course'])."</td>"
                    ."  <td>".dbBaterai($r['power'])."</td>"
                    ."</tr>";
        }
    } catch (PDOException $e) {
        print $e->getMessage();
        exit;
    }

    $ret .= "</tbody>";
    $ret .= "</table>";
    
    return $ret;
}

?>
