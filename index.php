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

    main {
        margin-bottom: 100px;
    }

    #map {
        width: 100%;
        height: 300px;
    }

    .panel {
        -webkit-box-shadow: 0 0 10px 0 rgba(240,240,240,1);
        -moz-box-shadow: 0 0 10px 0 rgba(240,240,240,1);
        box-shadow: 0 0 10px 0 rgba(240,240,240,1);
    }

    .location {
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

    @media screen and ( min-width: 768px ) {
        .form-group {
            margin-right: 10px;
        }
    }
</style>
<body>
<header>
    <div class="container">
        <div class="row">
            <div class="col-sm-3 text-center">
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
                    Filter Libraries By:
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
                        $form_data['police_districts'][] = $library->policeDistrict;
                        $form_data['council_districts'][] = $library->councilDistrict;
                    }

                    // remove duplicates and sort
                    foreach( $form_data as $key => $value ) {
                        $form_data[$key] = array_unique( $form_data[$key] );
                        sort( $form_data[$key] );
                    }
                    ?>
                    <form id="form" class="form-inline">
                        <div class="form-group">
                            <label for="form-zipcodes">Zipcodes</label>
                            <select id="form-zipcodes" data-obj-location="address">
                                <option value="0"></option>
                                <?php foreach ( $form_data['zip_codes'] as $zipcode ) {
                                    echo '<option value="' . $zipcode . '">' . $zipcode . '</option>';
                                } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="form-neighborhoods">Neighborhoods</label>
                            <select id="form-neighborhoods" data-obj-location="neighborhood">
                                <option value="0"></option>
                                <?php foreach ( $form_data['neighborhoods'] as $neighborhood ) {
                                    echo '<option value="' . $neighborhood . '">' . $neighborhood . '</option>';
                                } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="form-police-districts">Police Districts</label>
                            <select id="form-police-districts" data-obj-location="policeDistrict">
                                <option value="0"></option>
                                <?php foreach ( $form_data['police_districts'] as $police_district ) {
                                    echo '<option value="' . $police_district . '">' . $police_district . '</option>';
                                } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="form-council-districts">Council Districts</label>
                            <select id="form-council-districts" data-obj-location="councilDistrict">
                                <option value="0"></option>
                                <?php foreach ( $form_data['council_districts'] as $council_district ) {
                                    echo '<option value="' . $council_district . '">' . $council_district . '</option>';
                                } ?>
                            </select>
                        </div>
                        <!--<div class="form-group">
                            <label for="form-wheelchair">Wheelchair Accessibility?</label>
                            <select id="form-wheelchair" data-obj-location="wheelchairAccessible">
                                <option value="0"></option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>-->
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
<script src="js/script.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARht85CiqB7RclgGE58sw0i6oXl8bJLUs&callback=initMap" async defer></script>
</body>
</html>
