var map,
    geocoder,
    map_div = document.getElementById('map');

function initMap() {
    geocoder = new google.maps.Geocoder();

    map = new google.maps.Map(map_div, {
        center: {lat: 39.299236, lng: -76.609383 },
        zoom: 11
    });
}

$(document).ready(function() {

    $('#form').on('submit', function(e){
        e.preventDefault();

        // clear previous results
        $('#results').empty();
        initMap();

        // get results
        var zip_code = $(this).find(':selected').val();
        query_libraries( zip_code );
    });
});


function query_libraries( comparison_value ) {

    $.getJSON( 'libraries.json', function( data ) {

        for( var key in data ) {

            // object iteration
            var obj = data[key];

            var address_obj = obj['address'];

            if ( address_obj.zipCode == comparison_value ) {

                var address = address_obj.streetName + '<br>' + address_obj.cityState + ', ' + address_obj.zipCode;

                var name = obj['locationName'],
                    phone = obj['phoneNumber'],
                    email = obj['contactEmail'],
                    website = obj['website'];

                var hours_obj = obj['hoursOfOperation'],
                    hours_div = '';

                $(hours_obj).each( function(){
                    hours_div  += '<tr><td><strong>' + this.day + ':</strong>&nbsp;&nbsp;</td><td>' + this.hours + '</td></tr>';
                });

                var results = '<div class="location">'
                    + '<strong>' + name + '</strong><br>'
                    + address + '<br>'
                    + phone + '<br>'
                    + '<a href="mailto:' + email + '" target="_blank">' + email + '</a>' + '<br>'
                    + '<a href="' + website + '" target="_blank">' + website + '</a>'
                    + '<table class="location-hours">' + hours_div + '</table>'
                    + '</div>';

                $(results).appendTo('#results');

                geocoder.geocode( { 'address': address}, function(results, status) {

                    if (status == 'OK') {

                        map.setCenter(results[0].geometry.location);
                        map.setOptions({ zoom: 12 });

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