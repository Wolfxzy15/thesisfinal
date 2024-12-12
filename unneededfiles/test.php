<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        #map {position: absolute; top: 50px; bottom: 50px; left: 25%; right: 50px;}
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div id='map'>
    <script>
        var x = <?php echo json_encode($latitude); ?>;
        var y = <?php echo json_encode($longitude); ?>;
        var evac1_long = 10.730668;
        var evac1_lat = 122.560212;
        var evac2_long =10.733978;
        var evac2_lat = 122.557465;
        var evac3_long = 10.733936;
        var evac3_lat = 122.561885;
        var evac1_distance, evac2_distance, evac3_distance;

        var map = L.map('map').setView([10.731019, 122.558467], 15);
        L.tileLayer('https://api.maptiler.com/maps/streets-v2/256/{z}/{x}/{y}.png?key=ngrPpvE2X0m7KBaoLLex', {
            attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
        }).addTo(map);

        // Red marker icon
        var redIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });

        // Add red marker
        var redMarker = L.marker([x, y], {icon: redIcon}).addTo(map);

        // Other markers
        var markers = [
            {coords: [evac1_long, evac1_lat], name: 'Marker 1'},
            {coords: [evac2_long, evac2_lat], name: 'Marker 2'},
            {coords: [evac3_long, evac3_lat], name: 'Marker 3'}
        ];

        // Store distances in variables


        // Calculate distance between red marker and other markers
        markers.forEach(function(markerData) {
            var marker = L.marker(markerData.coords).addTo(map);
            var distance = redMarker.getLatLng().distanceTo(marker.getLatLng());
            marker.bindPopup('Distance to ' + markerData.name + ': ' + distance.toFixed(2) + ' meters').openPopup();
            

            
        });

        if (markerData.name === 'Marker 1') {
                evac1_distance = distance.toFixed(2) + ' meters';
            } else if (markerData.name === 'Marker 2') {
                evac2_distance = distance.toFixed(2) + ' meters';
            } else if (markerData.name === 'Marker 3') {
                evac3_distance = distance.toFixed(2) + ' meters';
            }
    </script>
    </div>
    <div style="display: inline-block;">

    <table class="table" >
  <thead>
    <tr>
      <th scope="col">Evacuation Center</th>
      <th scope="col">Distance</th>
    
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1</th>
      <td><script>document.write(evac1_distance);</script></td>
      
    </tr>
    <tr>
      <th scope="row">2</th>
      <td></td>

    </tr>
    <tr>
      <th scope="row">3</th>
      <td></td>

    </tr>
  </tbody>
</table>
        <script>
            document.write(evac1_distance);
        </script>
    </div>
    
</body>
</html>
