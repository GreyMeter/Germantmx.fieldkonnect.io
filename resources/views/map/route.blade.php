<!DOCTYPE html>
<html>

<head>
    <title>Route Map</title>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCaQE4KPeMlUtOToahksBb7k7TUNx7MISo"></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAVSDwHbKULnZa93kYpYINTqX4eaWy9q18"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAVSDwHbKULnZa93kYpYINTqX4eaWy9q18&libraries=marker"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-marker-clusterer/1.0.0/markerclusterer.js"></script>

</head>

<body>
    <div id="map"></div>
    <script>
    function initMap() {
        var coordinates = @json($coordinates);
        console.log(coordinates[0].latitude, coordinates[0].longitude)
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 8,
            center: {
                lat: parseFloat(coordinates[0].latitude),
                lng: parseFloat(coordinates[0].longitude)
            }
        });

        if (coordinates.length === 0) {
            console.error('No coordinates found');
            return;
        }

        var markers = [];
        coordinates.forEach(function(coord) {
            var latLng = {
                lat: parseFloat(coord.latitude),
                lng: parseFloat(coord.longitude)
            };
            var clr, path;
            if (coord.name == 'Punch In') {
                clr = 'green';
                path = "M15 14l-4-4 4-4v3h4v2h-4v3zm-3-5h-4v2h4v-2zm0-2H8v2h4v-2zm0-2H8v2h4V7zm0-2H8v2h4V5zm0-2H8v2h4V3z";
            } else if (coord.name == 'Punch Out') {
                clr = 'yellow';
                path = "M10 17l5-5-5-5v4H2v2h8v4zm8-14H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 14H4V5h14v12z";
            } else {
                clr = 'blue';
                path = "M-1.547 12l6.563-6.609-1.406-1.406-5.156 5.203-2.063-2.109-1.406 1.406zM0 0q2.906 0 4.945 2.039t2.039 4.945q0 1.453-0.727 3.328t-1.758 3.516-2.039 3.070-1.711 2.273l-0.75 0.797q-0.281-0.328-0.75-0.867t-1.688-2.156-2.133-3.141-1.664-3.445-0.75-3.375q0-2.906 2.039-4.945t4.945-2.039z";
            }

            const svgMarker = {
                path: path,
                fillColor: clr,
                fillOpacity: 0.8,
                strokeWeight: 1,
                rotation: 0,
                scale: 1.5,
                anchor: new google.maps.Point(0, 20),
            };

            var marker = new google.maps.Marker({
                position: latLng,
                map: map,
                title: coord.name + '(' + coord.time + ')',
                icon: svgMarker
            });

            markers.push(marker);
        });

        var lineSymbol = {
            path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
            scale: 3,
            strokeColor: '#121dab8a'
        };

        var routePath = new google.maps.Polyline({
            path: markers.map(marker => marker.getPosition()),
            geodesic: true,
            strokeColor: '#121dab8a',
            strokeOpacity: 0.9,
            strokeWeight: 4,
            icons: [{
                icon: lineSymbol,
                offset: '100%',
                repeat: '25px' 
            }]
        });

        routePath.setMap(map);

        // Initialize the MarkerClusterer
        new MarkerClusterer(map, markers, {
            imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
            gridSize: 50,
            maxZoom: 13,
        });
    }

    window.onload = initMap;
</script>

</body>

</html>