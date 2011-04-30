var map;
var markerMobil = [];
var latlangMobil = [];

function initGMap() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(<?php echo $_cfg['map_init_lat'].", ".$_cfg['map_init_lng'];  ?>);
    var myOptions = {
        zoom: <?php echo $_cfg['map_init_zoom']; ?>,
        center: latlng,
        mapTypeControl: true,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
        },
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    var map = new google.maps.Map(
        document.getElementById("peta"),
        myOptions);
    return map;
}


function setMarkers(map, pertanda) {
    $('#loading').fadeIn();
    var image = new google.maps.MarkerImage('media/truk.gif',
      new google.maps.Size(32, 15),
      new google.maps.Point(0,0),
      new google.maps.Point(0, 15));
    var shadow = new google.maps.MarkerImage('media/beachflag_shadow.png',
      new google.maps.Size(37, 32),
      new google.maps.Point(0,0),
      new google.maps.Point(0, 32));
    var shape = {
      coord: [1, 1, 1, 20, 18, 20, 18 , 1],
      type: 'poly'
    };
        
    for (var i = 0; i < pertanda.length; i++) {
        var tanda = pertanda[i];
        var myLatLng = new google.maps.LatLng(tanda[1], tanda[2]);
        latlangMobil[tanda[0]] = myLatLng;
        addMarker(myLatLng,map,shadow,image,shape,tanda[0],tanda[3],tanda[4]);
      }
    $('#loading').fadeOut();
}

function addMarker(myLatLng,map,shadow,image,shape,title,zIndex,tid) {
    var marker = new MarkerWithLabel({
        position: myLatLng,
        map: map,
        //shadow: shadow,
        icon: image,
        //shape: shape,
        title: title,
        labelContent: title,
        labelClass: "labelPeta",
        labelAnchor: new google.maps.Point(22, 0),
        zIndex: zIndex
    });
    markerMobil.push(marker);
    
    google.maps.event.addListener(marker, 'click', function(e) { bukaInfo(map,marker,title,myLatLng,tid);});
}

function bukaInfo(map,marker,title,latlng,tid) {
    $('#loading').fadeIn();
    geocoder.geocode({'latLng': latlng}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[0]) {
                alamat = results[0].formatted_address;
            }
        } else {
            alamat = status;
        }
        $('#loading').fadeOut();
        var contentString = "<div style='clear:both;'><strong>"
                     + title + "</strong> <input type='button' value=' Pantau ' />"
                     + " <input type='button' value=' Surat Jalan ' />"
                     + " <input type='button' value=' History ' />&nbsp;&nbsp;&nbsp;&nbsp;<br /><hr />"
                     + alamat + "<br />"
                     + "<table class='infobalon'>"
                     + "  <tr>"
                     + "    <th>Waktu</td>"
                     + "    <th>Muatan</td>"
                     + "    <th>Arah</td>"
                     + "    <th>Mesin</td>"
                     + "    <th>Kecepatan</td>"
                     + "  </tr>"
                     + "  <tr>"
                     + "    <td>00</td>"
                     + "    <td>-</td>"
                     + "    <td>--</td>"
                     + "    <td>--</td>"
                     + "    <td>0 Km/jam</td>"
                     + "  </tr>"
                     + "</table>"
                     + "</div>";
        var infowindow = new google.maps.InfoWindow({
            content: contentString
        });
        
        infowindow.open(map,marker);
    
    });
}

function deleteOverlays() {
    if (markerMobil) {
        for (i in markerMobil) {
            markerMobil[i].setMap(null);
        }
        markerMobil.length = 0;
    }
}

function refreshMap() {
    $.getJSON('markers.php', function(data) {
        deleteOverlays();
        setMarkers(map, data.mobil);
    });
}


function coba1() {
    $.get('http://nominatim.openstreetmap.org/reverse?lat=-6.9431445&lon=107.6486287&addressdetails=1&format=xml', function(data) {
        $(data).find("reversegeocode").each(function() {
            alert($(this).html());
          /*var marker = $(this);
          var point = {
            marker.attr("lat"),
            marker.attr("lng")
          };
          */
        });
    });
    
}
function temukan(nopol) {
    map.setCenter(latlangMobil[nopol]);
}