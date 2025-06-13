<!DOCTYPE html>
<html>
<head>
    <title>Field Manager Location Tracker</title>
</head>
<body>
    <h1>Location Tracker</h1>
    <p>Your location is being tracked.</p>

    <script>
        if ('geolocation' in navigator) {
            navigator.geolocation.watchPosition(function(position) {
                sendLocation(position.coords.latitude, position.coords.longitude);
            }, function(error) {
                console.error('Error getting location:', error);
            }, {
                enableHighAccuracy: true,
                maximumAge: 30000,
                timeout: 27000
            });
        } else {
            alert('Geolocation is not supported by this browser.');
        }

        function sendLocation(latitude, longitude) {
            fetch('/field-manager/{{ $fieldManager->id }}/location/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: latitude,
                    longitude: longitude
                })
            }).then(response => response.json())
            .then(data => {
                console.log('Location sent successfully:', data);
            }).catch(error => {
                console.error('Error sending location:', error);
            });
        }
    </script>
</body>
</html>


<!--
laravel make tracking system like zomato


example user can track location to delivery man.


my need is : My project is real estate. i have send template whatsapp message with track location button. user open whatsapp and click track location then view the live location to the field manager.and distant to how many time to destination.

Field manager location is dynamic update.Fiedl manager is login after move dashbord then first enable google chrome location like GPS Location after used.

location is enable then this is dynamic update on table.and user can track field manager.

make using laravel step by step my real estate website user can track location to field manager

used google API

step by step give me


same as zomato

This is example

Live tracking in food delivery services like Zomato, Swiggy, and Uber Eats relies on a combination of technologies and methodologies to provide real-time updates on the location of delivery personnel. Here’s a breakdown of how it generally works and the technology stack involved:






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Field Manager</title>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
</head>
<body>
    <h1>Track Field Manager: {{ $fieldManager->id }}</h1>
    <div id="map"></div>

    <script>
        let map;
        let marker;

        function initMap() {
            const initialLocation = {
                lat: "{{ $fieldManager->latitude }}",
                lng: "{{  $fieldManager->longitude }}"
            };
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: initialLocation
            });

            marker = new google.maps.Marker({
                position: initialLocation,
                map: map
            });
        }

        window.onload = initMap;

        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true
        });

        const channel = pusher.subscribe('location-tracking.{{ $fieldManager->id }}');
        channel.bind('App\\Events\\LocationUpdated', function(data) {
            const newPosition = {
                lat: data.latitude,
                lng: data.longitude
            };
            marker.setPosition(new google.maps.LatLng(data.latitude, data.longitude));
            map.setCenter(marker.getPosition());
        });
    </script>
</body>
</html> -->
