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
    <link rel="stylesheet" type="text/css" href="media/desktop.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="media/jquery.treeview.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="media/redmond/jquery-ui-1.8.4.custom.css"media="screen" />
    <link rel="stylesheet" type="text/css" href="media/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
    
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript" src="media/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="media/jquery-ui-1.8.4.custom.min.js"></script>
    <script type="text/javascript" src="media/jquery.treeview.pack.js"></script>
	<script type="text/javascript" src="media/markerwithlabel.js"></script>
    <script type="text/javascript" src="media/gmap-desktop.js"></script>
    <script type="text/javascript" src="media/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
    
	<script type="text/javascript">
		var lic_center = null;
        
        $(document).ready(function() {
			fitWindow();
            $("a.fancylink").fancybox({'width': '85%', 'height': '90%', 'title': $('a.fancylink:selected').text() });
            $("#cust_asset").treeview({
                control: "#tcontrol",
                collapsed: false
            });
            
            map_init(<?php echo $_cfg['map_init_lat'].", ".$_cfg['map_init_lng'].", ".$_cfg['map_init_zoom'];  ?>);
            map_show();
            
            $('#btnMaksimalkan').toggle(function() {
                $('#btnMaksimalkan').val(' Minimize ');
                $('#petawrap').addClass("petamax");
                $('#peta').css("height", ($(window).height()-90)+ 'px');
            }, function() {
                $('#btnMaksimalkan').val(' Maximize ');
                $('#petawrap').removeClass("petamax");
                $('#peta').css("height", ($(window).height()-50)*0.70 + 'px');
                fitWindow();
            });
            
            $('#btnSegarkan').click(function() {
                segarkan();
            });
            
            $('#mmenu li').hover(function() {
                $(this).find('ul').fadeIn('slow');
            }, function() {
                $(this).find('ul').fadeOut('fast');
            });
            getEvent();
    	});
		
        function fitWindow() {
            $('#kiri').css("height", $(window).height()-50 + 'px');
            $('#peta').css("height", ($(window).height()-50)*0.70 + 'px');
            $('#event').css("height", ($(window).height()-50)*0.23 + 'px');
        }
        
        function getEvent() {
            $('#event').load('alarm.php');           
            setTimeout('getEvent()', 60000);
        }
        
        function segarkan() {                
            det = $('#selSegar').val();
            if(det > 0) {
                map_reload();
                setTimeout('segarkan()', det*1000);
            }
        }
        
        function history_track(TID, license) {
            $.fancybox({
                        'type':'iframe',
                        'href': 'history.php?tid=' + TID + '&license=' + license,
                        'title': ' History ' + license,
                        'width': '90%',
                        'height': '90%'
            });
        }
	
        function temukan(license) {
            if(mark_latlng[license]) {
                the_map.setCenter(mark_latlng[license]);
                lic_center = license;
                if(the_map.getZoom() < 16)
                    the_map.setZoom(16);
            } else {
                alert('Maaf, tidak dapat menemukan data lokasi terakhir: ' + license);
            }
        }

        function resetMap() {
            lic_center = null;
            map_reset();
        }
    </script>
    <style>
    #logo {
        float: left;
    }
    #mmenuwrap {
        float: left;
        position: relative;
        height: 36px;
    }
    #mmenu {
        position: absolute;
        list-style-type: none;
        bottom: 0;
        left: 30px;
    }
    #mmenu li {
        display: table-cell;
        padding: 0 10px;
        text-align: center;
    }
    #mmenu li a {
        padding: 0 10px;
        text-align: center;
    }
    #mmenu li:hover {
        background-color: #f33;
        color: #fff;
    }
    #mmenu ul {
        display: none;
        background-color: #33f;
        position: absolute;
        margin-left: -10px;
        text-align: center;
    }
    #mmenu ul li {
        display: block;
    }
    #mmenu ul li a {
        color: #fff;
        text-decoration: none;
    }
    </style>

</head>
<body>
<div id="loading">Loading.....</div>
<div id="headbar">
	&nbsp;&nbsp;&nbsp;
	<div style="float: left">
        <div id="logo">
            <a href="/"></a><img src="media/logo40.gif" alt="Logo" /></a>
        </div>
        <div id="mmenuwrap">
            <ul id="mmenu">
                <li>ASSET
                    <ul>
                        <li><a class="fancylink iframe" href="d_asset_list.php">Asset List</a></li>
                        <li><a class="fancylink iframe" href="d_asset_group.php">Asset Groups</a></li>
                    </ul>
                </li>
                <li><strike>TRACKING</strike>
                    <ul>
                        <li><a href="#"><strike>Route</strike></a></li>
                        <li><a href="#"><strike>Over-speed / Ngebut</strike></a></li>
                    </ul>
                </li>
                <li><strike>FLEET</strike></li>
                <li><strike>ALERT</strike></li>
            </ul>
        </div>
	</div>
	<div style="float: right;margin-top: 12px;">
		<?php echo $_SESSION['username']; ?> | 
        <input type="button" onclick="window.location='logout.php'" value=" Log Out " style="color: #f33;font-weight: bold;"/>
	</div>
	<div style="clear: both"></div>
</div>

<div id="container">
	<div id="kiri">
    <div class="fleft txtMedium">Customer Asset</div>
    <div id="tcontrol" class="fright">
    <a href="#">&nbsp;-&nbsp;</a>|
    <a href="#">&nbsp;+&nbsp;</a>|
    <a href="#">[]</a>
    &nbsp;&nbsp;&nbsp;
    </div>
    <hr class="fclear" />
        <?php
            echo dbCustAssetTree("cust_asset");
        ?>	
	</div>
    <div id="kanan">
    
	    <div id="petawrap">
	    <div id="peta"></div>
        <div id="peta_btn">
            <input type="button" value=" Maximize " id="btnMaksimalkan">
            
            Refresh Otomatis: 
            <select name="selSegar" id="selSegar">
                <option value="0" selected>Dimatikan</option>
                <option value="20">20 Detik</option>
                <option value="30">30 Detik</option>
                <option value="35">35 Detik</option>
                <option value="40">40 Detik</option>
                <option value="45">45 Detik</option>
                <option value="50">50 Detik</option>
                <option value="55">55 Detik</option>
                <option value="60">1 Menit</option>
                <option value="120">2 Menit</option>
                <option value="180">3 Menit</option>
                <option value="240">4 Menit</option>
                <option value="300">5 Menit</option>
            </select>            
            <input type="button" value=" Refresh " id="btnSegarkan">
        </div>
        </div>
        <div id="event">
            <?php
                //echo dbAlarm('alarm');
            ?>
        </div>
    </div>
</div>

</body>
</html>
