<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Field Manager</title>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDrfV23_zTrI2a8RCZBTF0hZNRVUCCi_Q4&libraries=places"></script>
    <style>
        #map {
            height: 850px;
            width: 100%;
        }
    </style>
</head>

<body>
    <h1>Track Field Manager: {{ $fieldManager->name }}</h1>
    <div id="map"></div>

    <script>
        let map, marker, directionsService, directionsRenderer;
        let userLocation = {
            lat: parseFloat("{{ $user->latitude ?? 0 }}"),
            lng: parseFloat("{{ $user->longitude ?? 0 }}")
        };

        // Request location access
        function requestLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        initMap();
                    },
                    error => {
                        if (error.code === error.PERMISSION_DENIED) {
                            alert("Please enable location access in your browser.");
                        } else {
                            console.error("Location access failed:", error.message);
                            alert("Unable to retrieve your location. Please try again.");
                        }
                    }
                );
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        // Initialize the map
        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: userLocation
            });

            new google.maps.Marker({
                position: userLocation,
                map: map,
                label: "User Location",
                icon: {
                    url: '/backend/images/user_wait.png',
                    scaledSize: new google.maps.Size(70, 70)
                }
            });

            marker = new google.maps.Marker({
                position: {
                    lat: parseFloat("{{ $fieldManager->latitude }}"),
                    lng: parseFloat("{{ $fieldManager->longitude }}")
                },
                map: map,
                icon: {
                    url: '/backend/images/delivery-route.png',
                    scaledSize: new google.maps.Size(70, 70)
                }
            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                suppressMarkers: true
            });

            calculateAndDisplayRoute(marker.getPosition(), userLocation);

            // Pusher Integration
            const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
                cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
                forceTLS: true
            });

            const channel = pusher.subscribe('location-tracking.{{ $fieldManager->id }}');
            channel.bind('App\\Events\\LocationUpdated', function(data) {
                const newPosition = {
                    lat: parseFloat(data.latitude),
                    lng: parseFloat(data.longitude)
                };
                marker.setPosition(new google.maps.LatLng(newPosition.lat, newPosition.lng));
                calculateAndDisplayRoute(newPosition, userLocation);
            });
        }

        // Function to calculate and display the route
        function calculateAndDisplayRoute(origin, destination) {
            directionsService.route({
                    origin: origin,
                    destination: destination,
                    travelMode: google.maps.TravelMode.DRIVING
                },
                (response, status) => {
                    if (status === "OK") {
                        directionsRenderer.setDirections(response);
                    } else {
                        console.error("Directions request failed: " + status);
                    }
                }
            );
        }
        window.onload = requestLocation;
    </script>
</body>

</html>