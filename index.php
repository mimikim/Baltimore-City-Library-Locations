<!HTML DOCTYPE>
<html>
<head>
    <title>Baltimore Library Locations</title>
    <script src="jquery-3.1.1.min.js"></script>
</head>
<body>
<h1>Baltimore's Enoch Pratt Free Library Locations</h1>
<p>
    This web app finds and maps the location of every Baltimore city public library listed on the Enoch Pratt Library website. It makes use of a JSON data set located at <a href="https://data.baltimorecity.gov/Culture-Arts/Libraries/tgtv-wr5u">https://data.baltimorecity.gov/Culture-Arts/Libraries/tgtv-wr5u</a>
</p>
<div id="map" style="height:400px; width: 400px;"></div>
<br><br>
<form id="form">
    <select>
        <option value="21201">21201</option>
        <option value="21225">21225</option>
        <option value="21224">21224</option>
        <option value="21225">21225</option>
        <option value="21213">21213</option>
        <option value="21229">21229</option>
        <option value="21216">21216</option>
        <option value="21212">21212</option>
        <option value="21214">21214</option>
        <option value="21211">21211</option>
        <option value="21213">21213</option>
        <option value="21230">21230</option>
        <option value="21218">21218</option>
        <option value="21224">21224</option>
        <option value="21217">21217</option>
        <option value="21215">21215</option>
        <option value="21210">21210</option>
        <option value="21216">21216</option>
        <option value="21230">21230</option>
        <option value="21218">21218</option>
        <option value="21224">21224</option>
        <option value="21231">21231</option>
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

            var zipcode = $(this).find(':selected').val();

            query_libraries( zipcode );
        });

        function query_libraries( zipcode ) {

            $.getJSON( "libraries.json", function( data ) {

                jQuery.each( data['data'], function() {

                    if ( this[9] == zipcode ) {

                        var address_json = JSON.parse(this[13][0]),
                            address = address_json.address + ', '
                                + address_json.city + ', '
                                + address_json.state + ', '
                                + this[9];

                        var library_location = '<div style="margin-bottom:20px">' + this[8] + ' Library' + ', ' + this[10] + '<br>' + address + '</div>';
                        $(library_location).appendTo('#results');

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
                });
            });
        }
    });
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARht85CiqB7RclgGE58sw0i6oXl8bJLUs&callback=initMap" async defer></script>
</body>
</html>