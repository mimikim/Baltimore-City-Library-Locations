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


});

function query_libraries( zip_code ) {

    $.getJSON( 'libraries.json', function( data ) {

        console.log(data);

        for( var key in data ) {

            // object iteration
            var obj = data[key];

            var address_obj = obj['address'];

            if ( address_obj.zipCode == zip_code ) {

                var address = address_obj.streetName + '<br>' + address_obj.cityState + ', ' + address_obj.zipCode;

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