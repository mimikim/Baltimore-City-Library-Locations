<!DOCTYPE HTML>
<html>
<head>
    <title>Baltimore Library Locations</title>
    <link href="https://fonts.googleapis.com/css?family=PT+Sans|Volkhov" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/jquery-3.1.1.min.js"></script>
</head>
<style>
    html, body {
        padding: 0;
        margin: 0;
        background: #f7f9fc;
        font-family: 'PT Sans', sans-serif;
    }

    #map {
        width: 100%;
        height: 300px;
    }

    #form {

    }

    .panel {
        -webkit-box-shadow: 0px 0px 10px 0px rgba(240,240,240,1);
        -moz-box-shadow: 0px 0px 10px 0px rgba(240,240,240,1);
        box-shadow: 0px 0px 10px 0px rgba(240,240,240,1);
    }

    .location {
        margin: 0 0 0 15px;
        overflow-wrap: break-word;
    }

    .location:not(:first-of-type):before {
        content: '';
        display: block;
        border-top:1px solid #ddd;
        padding-top: 15px;
    }

    .location-hours {
        margin:15px 0;
    }

    header {
        background: #212121;
        background: -moz-linear-gradient(top, #373737, #212121);
        background: -webkit-linear-gradient(top, #373737, #212121);
        background: -ms-linear-gradient(top, #373737, #212121);
        background: -o-linear-gradient(top, #373737, #212121);
        background: linear-gradient(top, #373737, #212121);
        color: white;
        font-size: 48px;
        padding: 40px 0;
        margin-bottom: 40px;
        font-family: 'Volkhov', serif;
    }

    footer {
        background: #212121;
        color: white;
        padding: 40px 0;
        margin-top: 140px;
    }

    @media screen and ( min-width: 480px ) {

    }

    @media screen and ( min-width: 768px ) {

    }

    @media screen and ( min-width: 992px ) {

    }

    @media screen and ( min-width: 1200px ) {

    }

</style>
<body>
<header>
    <div class="container">
        <div class="row"> <div class="col-sm-3 text-center">
                <img src="enoch-pratt-library-logo.png" class="enoch-logo">
            </div>
            <div class="col-sm-9">
              Enoch Pratt Free Library Locations
            </div>

        </div>
    </div>
</header>
<main class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Filter Libraries
                </div>
                <div class="panel-body">
                    <?php
                    // grab file contents in php
                    $library_json = file_get_contents('libraries.json');
                    $libraries = json_decode( $library_json );
                    $form_data = [];

                    // assemble form data
                    foreach ( $libraries as $library ) {
                        $form_data['zip_codes'][] = $library->address->zipCode;
                        $form_data['neighborhoods'][] = $library->neighborhood;
                        $form_data['police_districts'][] = $library->policeDistrict;;
                        $form_data['council_districts'][] = $library->councilDistrict;
                    }

                    // remove duplicates and sort
                    foreach( $form_data as $key => $value ) {
                        $form_data[$key] = array_unique( $form_data[$key] );
                        sort( $form_data[$key] );
                    }
                    ?>
                    <form id="form" class="form-inline">
                        <label for="zipcodes">Zipcodes</label>
                        <select name="zipcodes">
                            <?php foreach ( $form_data['zip_codes'] as $zipcode ) {
                                echo '<option value="' . $zipcode . '">' . $zipcode . '</option>';
                            } ?>
                        </select>


                        <input type="submit" value="Submit">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Displaying <span id="result-count">0 Locations</span>
                </div>
                <div class="panel-body">
                    <div id="map"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-7">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Library Location Details
                </div>
                <div class="panel-body">
                    <div id="results"></div>
                </div>
            </div>
        </div>
    </div>
</main>
<footer>
    <div class="container">
        Baltimore City Library Locations maintained by <a href="https://github.com/mimikim" target="_blank">mimikim</a>
    </div>
</footer>

<script src="js/script.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARht85CiqB7RclgGE58sw0i6oXl8bJLUs&callback=initMap" async defer></script>
</body>
</html>
