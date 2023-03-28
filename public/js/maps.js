function initMap() {
    var lat=$("input[name='location_lat']").val();
    var lng=$("input[name='location_lng']").val();
    markers = [];
    map = new google.maps.Map(document.getElementById('gmap'), {
        center: {lat:38.898648, lng:77.037692},
        zoom: 1
    });


    var input = /** @type {!HTMLInputElement} */(
        document.getElementById('pac-input'));

    var types = document.getElementById('type-selector');
    //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);

    var infowindow = new google.maps.InfoWindow();
    var marker = new google.maps.Marker({
        map: map,
        anchorPoint: new google.maps.Point(0, -29)
    });

    autocomplete.addListener('place_changed', function () {
        infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            window.alert("Autocomplete's returned place contains no geometry");
            return;
        }

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
        }
        $("input[name='location_lat']").val(place.geometry.location.lat());
        $("input[name='location_lng']").val(place.geometry.location.lng());
        var component_count=place.address_components.length;
        $("input[name='country_code']").val(place.address_components[component_count-1].short_name);

        marker.setIcon(/** @type {google.maps.Icon} */({
            url: place.icon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(35, 35)
        }));

        marker.setPosition(place.geometry.location);
        marker.setVisible(true);
        markers.push(marker);


        var address = '';
        if (place.address_components) {
            address = [
                (place.address_components[0] && place.address_components[0].short_name || ''),
                (place.address_components[1] && place.address_components[1].short_name || ''),
                (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
        }

        infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
        infowindow.open(map, marker);
    });
    // Sets a listener on a radio button to change the filter type on Places
    // Autocomplete.


    /* map.addListener('tilesloaded', function() {
     alert("finished");

     });*/
    //getLocationName(lat,lng);
    map.addListener('click', function(e) {
        deleteMarkers();
        getLocationName(e.latLng.lat(),e.latLng.lng());
        //placeMarkerAndPanTo(e.latLng, map);

    });

    // Sets the map on all markers in the array.
    function setMapOnAll(map) {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(map);
        }
    }

    // Removes the markers from the map, but keeps them in the array.
    function clearMarkers() {
        setMapOnAll(null);
    }


    // Deletes all markers in the array by removing references to them.
    function deleteMarkers() {
        clearMarkers();
        markers = [];
    }



}
function getLocationName(lat,lng)
{
    latLng=new google.maps.LatLng(lat,lng);
    var pos = new google.maps.LatLng(lat,lng);
    map.setCenter(pos);
    map.setZoom(17);

    var geocoder = new google.maps.Geocoder;
    var infowindow = new google.maps.InfoWindow;
    $("input[name='location_lat']").val(lat);
    $("input[name='location_lng']").val(lng);

    var latlng = {lat: parseFloat(lat), lng: parseFloat(lng)};
    geocoder.geocode({'location': latlng}, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                var marker = new google.maps.Marker({
                    position: latlng,
                    map: map
                });
                markers.push(marker);
                infowindow.setContent(results[1].formatted_address);
                infowindow.open(map, marker);
                document.getElementById("pac-input").value=results[1].formatted_address;
                $("input[name='country_code']").val(getCountry(results[0].address_components));

            } else {
                window.alert('No results found');
            }
        } else {
            window.alert('Geocoder failed due to: ' + status);
        }
    });
}
function getCountry(addrComponents) {
    for (var i = 0; i < addrComponents.length; i++) {
        if (addrComponents[i].types[0] == "country") {
            return addrComponents[i].short_name;
        }
        if (addrComponents[i].types.length == 2) {
            if (addrComponents[i].types[0] == "political") {
                return addrComponents[i].short_name;
            }
        }
    }
    return false;
}

function getCurrentLocation() {
    navigator.geolocation.getCurrentPosition(function(position){
        var lat=position.coords.latitude;
        var lng= position.coords.longitude;
        $("input[name='location_lat']").val(lat);
        $("input[name='location_lng']").val(lng);


        // initMap(lat,lng);
        latLng=new google.maps.LatLng(lat,lng);
        var pos = new google.maps.LatLng(lat,lng);
        map.setCenter(pos);
        map.setZoom(17);
       getLocationName(lat,lng);

    });

}


$(document).ready(function()
{
    // Stop user to press enter in textbox
    $("input[name=address]").keypress(function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
});