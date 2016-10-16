var map_div = document.getElementById('map');
function initMap() {
    geocoder = new google.maps.Geocoder();
    map = new google.maps.Map(map_div, {
        center: {lat: 39.299236, lng: -76.609383 },
        zoom: 11,
        scrollwheel: false
    });
}

// place map markers on googlemap
function place_markers( address, name ) {
    geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == 'OK') {
            var results_obj = results[0];
            map.setCenter(results_obj.geometry.location);
            map.setOptions({ zoom: 12 });

            var marker = new google.maps.Marker({
                map: map,
                position: results_obj.geometry.location
            });

            var contentString = '<div id="content">'
                + '<strong>' + name + '</strong><br>'
                + address
                + '<br><a href="http://maps.google.com/maps?saddr=' + results_obj.formatted_address + '" target="_blank">Get Directions</a>'
                + '</div>';

            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });

            marker.addListener('click', function() {
                infowindow.open(map, marker);
                map.setCenter(results_obj.geometry.location);
            });
        }
    })
}

// sort and remove dupes
function library_sort_dupes( array ) {
    var return_array = [];

    // if element length is max 2, sort numerically
    if( array[0].length <= 2 ) {
        array.sort(function(a, b){ return a - b; });
    } else {
        // else sort alphabetically (default)
        array.sort();
    }

    array.forEach(function(element, index){
        // for each element in array,
        // if element does not already exist, please push
        if ( return_array.indexOf(element) == -1 ) {
            return_array.push(element);
        }
    });
    return return_array;
}

// define module
var app = angular.module('libraryApp', []);

// define controller, and use $http service
app.controller('libraryFilters', function($scope, $http) {
    $http.get('libraries.json').then(function(response) {

        var libraries = response.data,
            zipCodes = [],
            neighborhoods = [],
            policeDistricts = [],
            councilDistricts = [];

        // for each element in the libraries object
        for( var key in libraries ) {
            zipCodes.push( libraries[key]['address'].zipCode );
            neighborhoods.push( libraries[key]['neighborhood'] );
            policeDistricts.push( libraries[key]['policeDistrict'] );
            councilDistricts.push( libraries[key]['councilDistrict'] );
        }

        // library data
        $scope.zipCodes = library_sort_dupes( zipCodes );
        $scope.neighborhoods = library_sort_dupes( neighborhoods );
        $scope.policeDistricts = library_sort_dupes( policeDistricts );
        $scope.councilDistricts = library_sort_dupes( councilDistricts );

        // on select change
        $scope.selectChange = function(obj_location) {
            var results_container = document.getElementById('results'),
                results_count = document.getElementById('result-count'),
                is_zip = false;

            if ( obj_location === 'address' ) { is_zip = true; }

            // clear previous results
            results_container.innerHTML = '';
            initMap();

            // get filtered results
            var filtered_results = libraries.filter(function (library) {
                if ( is_zip ) {
                    return library[obj_location].zipCode === $scope.filterSelect;
                } else {
                    return library[obj_location] === $scope.filterSelect;
                }
            });

            for( var key in filtered_results ) {
                var obj = filtered_results[key],
                    address_obj = obj['address'],
                    address = address_obj.streetName + '<br>' + address_obj.cityState + ', ' + address_obj.zipCode,
                    hours_obj = obj['hoursOfOperation'],
                    hours_div = '';

                hours_obj.forEach(function(element, index){
                    hours_div  += '<tr><td><strong>' + element.day + ':</strong>&nbsp;&nbsp;</td><td>' + element.hours + '</td></tr>';
                });

                results_container.innerHTML += '<div class="location">'
                + '<strong>' + obj['locationName'] + '</strong><br>'
                + address + '<br>'
                + obj['phoneNumber'] + '<br>'
                + '<a href="mailto:' + obj['contactEmail'] + '" target="_blank">' + obj['contactEmail'] + '</a>' + '<br>'
                + '<a href="' +  obj['website'] + '" target="_blank">' + obj['website'] + '</a>' + '<br>'
                + 'Wheelchair Accessible? ' + obj['wheelchairAccessible']
                + '<table class="location-hours">' + hours_div + '</table>'
                + '</div>';

                place_markers(address, obj['locationName']);
            }

            if ( filtered_results.length === 1 ) {
                results_count.innerHTML = filtered_results.length + ' Location';
            } else {
                results_count.innerHTML = filtered_results.length + ' Locations';
            }
        };
    });
});