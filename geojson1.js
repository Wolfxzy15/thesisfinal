let barangays = {};


fetch('geojson/tabucsuba.geojson')
    .then(response => response.json())
    .then(geojsonData => {
        barangays.tabucSuba = geojsonData;
        L.geoJSON(geojsonData, {
            style: function (feature) {
                return { color: "#ff0000", weight: 2 };
            },
            onEachFeature: function (feature, layer) {
                if (feature.properties && feature.properties.name) {
                    layer.bindPopup(feature.properties.name);
                }
            }
        }).addTo(map);
    });

fetch('geojson/cubay.geojson')
    .then(response => response.json())
    .then(geojsonData => {
        barangays.cubay = geojsonData;
        L.geoJSON(geojsonData, {
            style: function (feature) {
                return { color: "#ff0000", weight: 2 };
            },
            onEachFeature: function (feature, layer) {
                if (feature.properties && feature.properties.name) {
                    layer.bindPopup(feature.properties.name);
                }
            }
        }).addTo(map);
    });

fetch('geojson/sanisidro.geojson')
    .then(response => response.json())
    .then(geojsonData => {
        barangays.sanIsidro = geojsonData;
        L.geoJSON(geojsonData, {
            style: function (feature) {
                return { color: "#ff0000", weight: 2 };
            },
            onEachFeature: function (feature, layer) {
                if (feature.properties && feature.properties.name) {
                    layer.bindPopup(feature.properties.name);
                }
            }
        }).addTo(map);
    });

fetch('geojson/quintinsalas.geojson')
    .then(response => response.json())
    .then(geojsonData => {
        barangays.quintinSalas = geojsonData;
        L.geoJSON(geojsonData, {
            style: function (feature) {
                return { color: "#ff0000", weight: 2 };
            },
            onEachFeature: function (feature, layer) {
                if (feature.properties && feature.properties.name) {
                    layer.bindPopup(feature.properties.name);
                }
            }
        }).addTo(map);
    });
