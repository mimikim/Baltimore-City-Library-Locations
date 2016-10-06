<!DOCTYPE HTML>
<html>
<head>
    <title>Baltimore Library Locations</title>
    <script src="jquery-3.1.1.min.js"></script>
</head>
<style>
    body {
        padding-bottom: 50px;
    }

    #map {
        width: 100%;
        height: 400px;
        margin-bottom: 30px;
    }

    .location {
        margin: 15px 0;
    }

    @media screen and ( min-width: 768px ) {
        #map {
            width: 400px;
        }
    }
</style>
<body>
<h1>Baltimore's Enoch Pratt Free Library Locations</h1>
<p>
    This web app finds and maps the location of every Baltimore city public library listed on the Enoch Pratt Library website.
</p><p>
    This was inspired by a public JSON data set located at <a href="https://data.baltimorecity.gov/Culture-Arts/Libraries/tgtv-wr5u">https://data.baltimorecity.gov/Culture-Arts/Libraries/tgtv-wr5u</a>. Unfortunately the file was incomplete and could have been organized better, so I went through the Pratt Library website to collect data and created my own JSON file to reference.
</p>
<div id="map"></div>
<?php

// grab file contents in php
$library_json = file_get_contents('libraries.json');
$libraries = json_decode( $library_json );
$zip_codes = [];

// get zip codes
$zip_codes = array_map(function($library) {
    return $library->address->zipCode;
}, $libraries);

// remove duplicates
$zip_codes = array_unique($zip_codes);

// sort by ascending order
sort($zip_codes);

?>
<form id="form">
    <select>
        <?php foreach ( $zip_codes as $zip ) {
            echo '<option value="' . $zip . '">' . $zip . '</option>';
        } ?>
    </select>
    <input type="submit" value="Submit">
</form>
<div id="results"></div>
<script>

    var map;
    var geocoder;

    function initMap() {
        geocoder = new google.maps.Geocoder();

        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 39.299236, lng: -76.609383 },
            zoom: 11
        });
    }

    $(document).ready(function() {

        $('#form').on('submit', function(e){

            e.preventDefault();
            $('#results').empty();
            initMap();

            var zip_code = $(this).find(':selected').val();
            query_libraries( zip_code );
        });

        function query_libraries( zip_code ) {

            $.getJSON( 'libraries.json', function( data ) {

                //console.log(data);

                for( var key in data ) {

                    // object iteration
                    var obj = data[key];

                    var address_obj = obj['address'];

                    if ( address_obj.zipCode == zip_code ) {

                        var address = address_obj.streetName + '<br>' + address_obj.city + ', ' + address_obj.state + ', ' + address_obj.zipCode;

                        var name = obj['locationName'],
                            phone = obj['phoneNumber'],
                            email = obj['contactEmail'],
                            website = obj['website'];

                        var hours_obj = obj['hoursOfOperation'],
                            hours_div = '';

                        $(hours_obj).each( function(){
                            hours_div  += '<div>' + this.day + ': ' + this.hours + '</div>';
                        });

                        var results = '<div class="location">'
                            + '<strong>' + name + '</strong><br>'
                            + address + '<br>'
                            + phone + '<br>'
                            + email + '<br>'
                            + '<a href="' + website + '" target="_blank">' + website + '</a>'
                            //+ hours_div
                            + '</div>';

                        $(results).appendTo('#results');

                        geocoder.geocode( { 'address': address}, function(results, status) {

                            if (status == 'OK') {

                                map.setCenter(results[0].geometry.location);

                                var marker = new google.maps.Marker({
                                    map: map,
                                    position: results[0].geometry.location
                                });

                            } else {
                                alert('Geocode was not successful for the following reason: ' + status);
                            }
                        });

                    }
                }
            });
        }
    });
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARht85CiqB7RclgGE58sw0i6oXl8bJLUs&callback=initMap" async defer></script>
</body>
</html>
