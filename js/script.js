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

function query_libraries( comparison_value, comparison_location ) {

    var results_count = $('#result-count');

    $.getJSON( 'libraries.json', function( data ) {

        var filtered_results = data.filter(function (library) {

            //return library[comparison_location] == comparison_value;
            return library.address.zipCode == comparison_value;

        });

        //console.log(filtered_results);
        //console.log(filtered_results.length);

        for( var key in filtered_results ) {

            // object iteration
            var obj = filtered_results[key];

            var address_obj = obj['address'],
                address = address_obj.streetName + '<br>' + address_obj.cityState + ', ' + address_obj.zipCode;

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

            place_markers(address, name);
        }

        if ( filtered_results.length > 1 ) {
            $(results_count).text(filtered_results.length + ' Locations');
        } else {
            $(results_count).text(filtered_results.length + ' Location');
        }

    });
}

function place_markers( address, name ) {

    geocoder.geocode( { 'address': address}, function(results, status) {

        //console.log(results);

        if (status == 'OK') {

            var results_obj = results[0],
                address_components = results_obj.address_components;

            map.setCenter(results_obj.geometry.location);
            map.setOptions({ zoom: 12 });

            var marker = new google.maps.Marker({
                map: map,
                position: results_obj.geometry.location
            });

            var location_address = address_components[0].short_name
                + ' ' + address_components[1].short_name
                + ',<br>' + address_components[3].short_name
                + ', ' + address_components[4].short_name
                + ' ' + address_components[6].short_name;

            var contentString = '<div id="content">'
                + '<strong>' + name + '</strong><br>'
                + location_address
                + '<br><a href="http://maps.google.com/maps?saddr=' + results_obj.formatted_address + '" target="_blank">Get Directions</a>' + '</div>';

            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });

            marker.addListener('click', function() {
                infowindow.open(map, marker);
                map.setCenter(results_obj.geometry.location);
            });

        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
    });
}