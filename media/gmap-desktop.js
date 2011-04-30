var the_map;
var def_lat, def_lng, def_zoom;
var mark_latlng = [];
var mark_image = [];
var mark_marker = [];

function map_init(i_lat, i_lng, i_zoom) {
    def_lat = i_lat;
    def_lng = i_lng;
    def_zoom = i_zoom;
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(i_lat, i_lng);
    var myOptions = {
        zoom: i_zoom,
        center: latlng,
        navigationControl: true,
        mapTypeControl: true,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
        },
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    the_map = new google.maps.Map(
        document.getElementById("peta"),
        myOptions);
}

function map_reset() {
    map_init(def_lat, def_lng, def_zoom);
    map_show();
}

function map_show() {
    $('#loading').fadeIn();
    $.getJSON('markers.php', function(data) {
        for (var i = 0; i < data.mobil.length; i++) {
            var tanda = data.mobil[i];
            
            var kecepatan = tanda[4];
            var arah = tanda[5];
            var arah_text = "N.A";
            var file = "suv";
            if(arah > 337 || (arah>=1 && arah <=22)) {
                file+= "0.gif";
                arah_text = "Utara";
            } else if(arah > 22 && arah <=67) {
                file+= "45.gif";
                arah_text = "Timur Laut";
            } else if(arah > 67 && arah <=112) {
                file+= "90.gif";
                arah_text = "Timur";
            } else if(arah > 112 && arah <=157) {
                file+= "135.gif";
                arah_text = "Tenggara";
            } else if(arah > 157 && arah <=202) {
                file+= "180.gif";
                arah_text = "Selatan";
            } else if(arah > 202 && arah <=247) {
                file+= "225.gif";
                arah_text = "Barat Daya";
            } else if(arah > 247 && arah <=292) {
                file+= "270.gif";
                arah_text = "Barat";
            } else if(arah > 292 && arah <=337) {
                file+= "315.gif";
                arah_text = "Barat Laut";
            } else {
                file+= "truk.gif";
            }
            
            var ikon  = new google.maps.MarkerImage('media/' + file,
                        new google.maps.Size(30, 30),
                        new google.maps.Point(0, 0),
                        new google.maps.Point(30, 30));

            var marker = createMarker(tanda[0], tanda[1], tanda[2], tanda[3], ikon, kecepatan, arah_text);
            
            
            mark_image[tanda[0]]  = ikon;
            mark_marker[tanda[0]] = marker;
        }
        
        if(lic_center != null) {
            the_map.panTo(mark_latlng[lic_center]);
        }
            
        $('#loading').fadeOut();
    });
    
}

function createMarker(license, lat, lng, TID, ikon, kecepatan, arah) {

    var myLatLng = new google.maps.LatLng(lat, lng);
    
    var marker = new MarkerWithLabel({
        position: myLatLng,
        map: the_map,
        icon: ikon,
        flat: true,
        title: "Arah: " + arah + ", " + kecepatan + " Km/jam",
        labelContent: license,
        labelClass: "labelPeta",
        labelAnchor: new google.maps.Point(22, 0)
    });
            
    
    google.maps.event.addListener(marker, 'click', function(e) { 
        bukaInfo(marker, license, myLatLng, lat, lng, TID);
    });

    mark_latlng[license] = myLatLng;

    return marker;
}

function bukaInfo(marker,license,latlng,lat,lng,TID) {
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
        var contentString = "<div style='clear:both;'>"
                     + "<span>" + TID + " - <strong>" + license + "</strong></span>"
                     + "<hr />"
                     + alamat + "<br />"
                     + "<iframe src=\"d_infowindow.php?license="+ license +"&lat=" + lat + "&lng=" + lng + "\" frameborder=\"0\">...</iframe>"
                     + "<div><input type=\"button\" value=\" History \" onclick=\"history_track('" + TID + "','" + license + "')\" />&nbsp;&nbsp;&nbsp;&nbsp;</div>"
                     + "</div>";
        var infowindow = new google.maps.InfoWindow({
            content: contentString
        });
        
        infowindow.open(the_map,marker);
    
    });
}		

function map_removeall() {
    if (mark_marker) {
        for (i in mark_marker) {
            mark_marker[i].setMap(null);
        }
        mark_marker.length = 0;
    }
}

function map_reload() {
    map_removeall();
    map_show();
}
