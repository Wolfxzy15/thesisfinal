// Initialize the map and set the view
var map = L.map('map').setView([10.7332, 122.5585], 13);

// Add the tile layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Object to store barangay GeoJSON layers
let barangays = {};

// Load GeoJSON for each barangay and store the layers
fetch('geojson/tabucsuba.geojson')
    .then(response => response.json())
    .then(geojsonData => {
        barangays.tabucSuba = geojsonData;
        L.geoJSON(geojsonData).addTo(map);
    });

fetch('geojson/cubay.geojson')
    .then(response => response.json())
    .then(geojsonData => {
        barangays.cubay = geojsonData;
        L.geoJSON(geojsonData).addTo(map);
    });

fetch('geojson/sanisidro.geojson')
    .then(response => response.json())
    .then(geojsonData => {
        barangays.sanIsidro = geojsonData;
        L.geoJSON(geojsonData).addTo(map);
    });

fetch('geojson/quintinsalas.geojson')
    .then(response => response.json())
    .then(geojsonData => {
        barangays.quintinsalas = geojsonData;
        L.geoJSON(geojsonData).addTo(map);
    });

// Custom icon for markers
var familyIcon = L.icon({
    iconUrl: 'images/home2.svg',
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32]
});

var marker;

// Function to handle map clicks and update the address
function onMapClick(e) {
    // Remove the old marker if it exists
    if (marker) {
        map.removeLayer(marker);
    }

    // Add a new marker at the clicked location
    marker = L.marker(e.latlng).addTo(map);

    // Update the latitude and longitude fields with the clicked location
    document.getElementById('latitude').value = e.latlng.lat;
    document.getElementById('longitude').value = e.latlng.lng;

    // Fetch the address based on the clicked coordinates
    fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('presentAddress').value = data.display_name;
        })
        .catch(error => console.error('Error:', error));

    // Now, check which barangay the clicked point is inside
    checkBarangay(e.latlng);
}

// Function to check if a point is inside a polygon (ray-casting algorithm)
function isPointInPolygon(point, polygon) {
    let x = point[1], y = point[0]; // Latitude and Longitude
    let inside = false;

    for (let poly of polygon) {
        for (let i = 0, j = poly.length - 1; i < poly.length; j = i++) {
            let xi = poly[i][1], yi = poly[i][0];
            let xj = poly[j][1], yj = poly[j][0];

            let intersect = ((yi > y) !== (yj > y)) &&
                (x < ((xj - xi) * (y - yi)) / (yj - yi) + xi);
            if (intersect) inside = !inside;
        }
    }

    return inside;
}

// Function to check which barangay the point is inside
function checkBarangay(latlng) {
    let barangayName = '';
    const point = [latlng.lng, latlng.lat];

    // Check each barangay GeoJSON layer
    if (barangays.tabucSuba && isPointInPolygon(point, barangays.tabucSuba.features[0].geometry.coordinates)) {
        barangayName = '1';
    } else if (barangays.cubay && isPointInPolygon(point, barangays.cubay.features[0].geometry.coordinates)) {
        barangayName = '2';
    } else if (barangays.sanIsidro && isPointInPolygon(point, barangays.sanIsidro.features[0].geometry.coordinates)) {
        barangayName = '3';
    } else if (barangays.quintinsalas && isPointInPolygon(point, barangays.quintinsalas.features[0].geometry.coordinates)) {
        barangayName = '4';
    }

    // Update the input field with the detected barangay
    const barangayInput = document.getElementById('barangay');
    if (barangayInput) {
        barangayInput.value = barangayName || 'No barangay found';
    } else {
        console.error('Barangay input field not found.');
    }
}

// Event listener for map clicks
map.on('click', onMapClick);

// Function to place a marker with address
function placeMarker(lat, lng, address) {
    if (marker) {
        map.removeLayer(marker);
    }
    marker = L.marker([lat, lng], {icon: familyIcon}).addTo(map);
    map.setView([lat, lng], 16);
    document.getElementById('presentAddress').value = address;
}

// Check for saved latitude and longitude to place a marker
var savedLatitude = parseFloat(document.getElementById('latitude').value);
var savedLongitude = parseFloat(document.getElementById('longitude').value);
var savedAddress = document.getElementById('presentAddress').value;

if (!isNaN(savedLatitude) && !isNaN(savedLongitude) && savedLatitude !== 0 && savedLongitude !== 0) {
    placeMarker(savedLatitude, savedLongitude, savedAddress);
} else {
    console.warn('Invalid or missing latitude/longitude.');
}

// Function to calculate age based on the date of birth
function calculateAge(formCount) {
    const dobInput = document.getElementById(`dateOfBirth${formCount}`);
    const ageInput = document.getElementById(`age${formCount}`);

    const dobValue = dobInput.value;
    if (dobValue) {
        const dob = new Date(dobValue);
        const today = new Date();

        // Calculate age
        let age = today.getFullYear() - dob.getFullYear();
        const monthDifference = today.getMonth() - dob.getMonth();
        if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        ageInput.value = age; // Update the age field
    } else {
        ageInput.value = ''; // Clear the age field if no date is selected
    }
}
