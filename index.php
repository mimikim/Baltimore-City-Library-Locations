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
foreach( $libraries as $library ) {
    array_push( $zip_codes, $library->address->zipCode );
}

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
<script src="script.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARht85CiqB7RclgGE58sw0i6oXl8bJLUs&callback=initMap" async defer></script>
</body>
</html>