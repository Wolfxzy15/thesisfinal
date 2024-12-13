var barangays = {};

var geojsonPromises = [
    fetch('geojson/tabucsuba.geojson').then(response => response.json()).then(data => { barangays.tabucSuba = data; }),
    fetch('geojson/cubay.geojson').then(response => response.json()).then(data => { barangays.cubay = data; }),
    fetch('geojson/sanisidro.geojson').then(response => response.json()).then(data => { barangays.sanIsidro = data; }),
    fetch('geojson/quintinsalas.geojson').then(response => response.json()).then(data => { barangays.quintinsalas = data; })
];

// Once all GeoJSON files are loaded, add them to the map
Promise.all(geojsonPromises).then(function() {
    // Add all the GeoJSON layers to the map
    for (var barangay in barangays) {
        L.geoJSON(barangays[barangay]).addTo(map);
    }
});

