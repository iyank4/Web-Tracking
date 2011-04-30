<?php
// other function required by apps

/* Security */
function fCleanInput($input) {
    $out = strip_tags($input);
    $out = htmlentities($out);
    
    $search=array("\\","\0","\n","\r","\x1a","'",'"');
    $replace=array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
    $out = str_replace($search,$replace,$out);
    
    return $out;
}


/* Auth */
function fAuth() {    
    if(!isset($_SESSION['username'])) {
        header('location: desktoplogin.php');
        exit;
    }
    
    
}

/* Cookie Auth */
function fReadCookie() {

}

function fWriteCookie() {

}

/* Humanize the Number */
function fArah($narah=0){
	$arah= "-";
	if ($narah >= 1 && $narah <=22){
		$arah = "Utara";
	}
	
	if ($narah >= 23 && $narah <=68){
		$arah = "Timur Laut";
	}
	
	if ($narah >= 69 && $narah <=114){
		$arah = "Timur";
	}
	if ($narah >= 115 && $narah <=160){
		$arah = "Tenggara";
	}
	
	if ($narah >= 161 && $narah <=206){
		$arah = "Selatan";
	}
	
	if ($narah >= 207 && $narah <= 252 ){
		$arah = "Barat Daya";
	}
	if ($narah >= 253 && $narah <=298){
		$arah = "Barat";
	}
	
	if ($narah >= 299 && $narah <=344){
		$arah = "Barat Laut";
	}
	
	if ($narah >= 344 && $narah <=360){
		$arah = "Utara";
	}
	if ($narah == 0){
		$arah = "-";
	}
	return $arah;	
}

function fJarak($latitudeFrom, $longitudeFrom, $latituteTo, $longitudeTo) {
    $degreeRadius = deg2rad(1);
 
    $latitudeFrom  *= $degreeRadius;
    $longitudeFrom *= $degreeRadius;
    $latituteTo    *= $degreeRadius;
    $longitudeTo   *= $degreeRadius;
 
    $d = sin($latitudeFrom) * sin($latituteTo) + cos($latitudeFrom)
       * cos($latituteTo) * cos($longitudeFrom - $longitudeTo);
 	
    $jarak = round((6371.0 * acos($d)),2);
    if ($jarak == "NAN") ($jarak=0);
    return $jarak;
}

function fAlamat($lat=0,$lon=0){
	include_once('./curl.class.php');
	$curl = new Curl();
//	$a = $curl->post('http://ws.geonames.org/extendedFindNearby?lng='.$lon.'&lat='.$lat);
	$a = $curl->post('http://maps.google.com/maps/geo?q='.$lat.','.$lon.'&gl=id&output=xml');
    
/*	
	$r["negara"] 	= $a["geonames"]["geoname"][2]["name"];
	$r["propinsi"] 	= $a["geonames"]["geoname"][3]["name"];
	$r["kota"]		= $a["geonames"]["geoname"][4]["name"];
*/	
	$r["koordinat"]		= $a['kml']['Response']['name']; 
	$r["alamat"]	= $a['kml']['Response']['Placemark']['address'];
	$r["negara"]		= $a['kml']['Response']['Placemark']['AddressDetails']['Country']['CountryName'];
	$r["propinsi"]		= $a['kml']['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea']['AdministrativeAreaName'];
    
	//if (empty($a['kml']['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['SubAdministrativeAreaName'])){
        $r["kota"]			= $a['kml']['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['LocalityName'];
        $r["daerah"]		= $a['kml']['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['DependentLocalityName'];
        $r["jalan"]			= $a['kml']['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['Thoroughfare']['ThoroughfareName'];
	//} else {
	//	$r["kota"]			= $a['kml']['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['LocalityName'] . "," . $a['kml']['Response']['Placemark'][0]['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['SubAdministrativeAreaName'];
	//	$r["daerah"]		= $a['kml']['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['DependentLocality']['DependentLocalityName'];
	//	$r["jalan"]			= $a['kml']['Response']['Placemark']['AddressDetails']['Country']['AdministrativeArea']['SubAdministrativeArea']['Locality']['DependentLocality']['Thoroughfare']['ThoroughfareName'];
	//}	
	
	return $r;
}
?>
