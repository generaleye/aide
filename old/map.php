<?php
if ((isset($_GET['lat']))&&(isset($_GET['long']))) {
    $lat=$_GET['lat'];
    $long=$_GET['long'];
} else {
    $lat=7.517734199217642;
    $long=4.526349925848308;
}
?>
<!DOCTYPE html>
<html>
<head>

    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <style>
        #myMap {
            height: 350px;
            width: 680px;
        }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript">
        var map;
        var marker;
        var myLatlng = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
        var geocoder = new google.maps.Geocoder();
        var infowindow = new google.maps.InfoWindow();
        function initialize(){
            var mapOptions = {
                zoom: 15,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("myMap"), mapOptions);

            marker = new google.maps.Marker({
                map: map,
                position: myLatlng,
                draggable: true
            });

            geocoder.geocode({'latLng': myLatlng }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        $('#address').val(results[0].formatted_address);
                        $('#latitude').val(marker.getPosition().lat());
                        $('#longitude').val(marker.getPosition().lng());
                        infowindow.setContent(results[0].formatted_address);
                        infowindow.open(map, marker);
                    }
                }
            });


            google.maps.event.addListener(marker, 'dragend', function() {

                geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            $('#address').val(results[0].formatted_address);
                            $('#latitude').val(marker.getPosition().lat());
                            $('#longitude').val(marker.getPosition().lng());
                            infowindow.setContent(results[0].formatted_address);
                            infowindow.open(map, marker);
                        }
                    }
                });
            });

        }

        google.maps.event.addDomListener(window, 'load', initialize);
    </script>
</head>
<body>
<div id="myMap"></div><br/>
<div>
    <input id="address"  type="text" style="width:600px;"/>
    <br/>
    <input type="text" id="latitude" placeholder="Latitude"/>
    <input type="text" id="longitude" placeholder="Longitude"/>


</div>
</body>
</html>