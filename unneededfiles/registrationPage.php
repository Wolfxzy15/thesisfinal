<?php

require 'residentFunction.php';
if (isset($_SESSION["residentID"])) {
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>index</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>
<style>
    #notification {
        display: none;
        position: fixed;
        background-color: #28a745;
        color: white;
        left: 50%;
        top: 12%;
        transform: translate(-50%, -50%);
        width: 80%;
        text-align: center;
        padding: 10px;
        border-radius: 5px;
        z-index: 1000;
        }
        #formIncompleteNotification {
        display: none;
        position: fixed;
        background-color: #dc3545;
        color: white;
        left: 50%;
        top: 12%;
        transform: translate(-50%, -50%);
        width: 80%;
        text-align: center;
        padding: 10px;
        border-radius: 5px;
        z-index: 1000;
        }
        #map {
            height: 400px;
        }

</style>

<body style="background-image: url('https://dynamic-media-cdn.tripadvisor.com/media/photo-o/06/e0/4c/2d/iloilo-city-hall.jpg?w=1200&h=-1&s=1');
  background-repeat: no-repeat;
  background-size: cover;">
     <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">
    <img src="https://www.clipartmax.com/png/middle/98-987300_seal-of-the-barangay-barangay-logo-png.png" 
    width="85" height="70" class="d-inline-block align-center" alt="">
  </a>
  <h4 style="color: aliceblue;">Barangay</h4>
    <!-- <a class="btn btn-dark ml-auto" href="register.php" role="button">ADMIN</a>  -->
  </div>
</nav><br>
<div id="notification">Registration Successful</div>
<div id="formIncompleteNotification">Please fill up the form completely</div>

    <div class="container" style="background-color: aliceblue; border-radius: 5px; padding: 12px; margin: auto;">
        <form autocomplete="off" action="" method="post">
            <input type="hidden" id="action" value="register">
            <h2 style="text-align: center;">REGISTRATION FORM</h2><br>
            <div class="row row-cols-2 mb-3">
                <div class="col-md-4 mb-2">
                    <label for="lastName">Lastname:</label>
                    <input type="text" class="form-control" placeholder="Last name" id="lastName" name="lastName">
                </div>
                <div class="col-md-4">
                    <label for="fName">Firstname:</label>
                    <input type="text" class="form-control" placeholder="First name" id="fName" name="fName">
                </div>
                <div class="col-md-4">
                    <label for="mName">Middlename:</label>
                    <input type="text" class="form-control" placeholder="Middle name" id="mName" name="mName">
                </div>
                <div class="col-md-4">
                    <label for="age">Age:</label>
                    <input type="text" class="form-control" placeholder="" id="age" name="age">
                </div>
                <div class="col-md-7">
                <label for="kinship">Kinship Position:</label>
                    <select id="kinship" name="kinship" class="form-control">
                        <option value="">--Position--</option>
                        <option value="Head of Family">Head of Family</option>
                        <option value="Spouse">Spouse</option>
                        <option value="Solo Parent">Solo Parent</option>
                        <option value="Solo Living">Solo Living</option>
                        <option value="Dependent">Dependent</option>
                    </select>
                </div>
            </div>
            <hr>
            <div class="row row-cols-2 mb-3"> <!-- Added mb-3 class to add margin-bottom -->
                <div class="col-md-6">
                    <label for="presentAdd">Present Address:</label>
                    <input type="text" class="form-control" placeholder="Present Address" id="presentAdd" name="presentAdd" required readonly>
                </div>
                <div id="map"></div>
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
                <div class="col-md-6">
                    <label for="provAdd">Provincial Address:</label>
                    <input type="text" class="form-control" placeholder="Provincial Address" id="provAdd" name="provAdd">
                </div>
            </div>
            <div class="row row-cols-2 mb-3"> <!-- Added mb-3 class to add margin-bottom -->
            <div class="col-md-2 mb-2">
                    <label for="sex">Sex:</label>
                    <input type="radio" value="Female" id="female" name="sex">Female
                    <input type="radio" value="Male"  id="male" name="sex">Male
                </div>
                <div class="col-md-4 mb-2">
                    <label for="civilStat">Civil Status:</label>
                    <select id="civilStat" name="civilStat" class="form-control">
                        <option value="">--Status--</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Widowed">Widowed</option>
                        <option value="Separated">Separated</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="dateOfBirth">Date Of Birth:</label>
                    <input type="text" class="form-control" placeholder="mm/dd/yyyy" id="dateOfBirth" name="dateOfBirth">
                </div>
                <div class="col-md-4">
                    <label for="placeOfBirth">Place Of Birth:</label>
                    <input type="text" class="form-control" placeholder="" id="placeOfBirth" name="placeOfBirth">
                </div>
            </div>
            <div class="row row-cols-2 mb-3"> <!-- Added mb-3 class to add margin-bottom -->
                <div class="col-md-2 mb-2">
                    <label for="height">Height:</label>
                    <input type="number" class="form-control" placeholder="cm" id="height" name="height">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="weight">Weight:</label>
                    <input type="number" class="form-control" placeholder="kg" id="weight" name="weight">
                </div>
                <div class="col-md-4 mb-2">
                    <label for="contactNo">Contact Number:</label>
                    <input type="text" class="form-control" placeholder="" id="contactNo" name="contactNo">
                </div>
                <div class="col-md-4 mb-4">
                    <label for="religion">Religion:</label>
                    <input type="text" class="form-control" placeholder="ex.Roman Catholic" id="religion" name="religion">
                </div>
                <div class="col-md-4">
                    <label for="emailAdd">Email Address:</label>
                    <input type="text" class="form-control" placeholder="@email.com" id="emailAdd" name="emailAdd">
                </div>
                <div class="col-md-4">
                    <label for="famComposition">Number of Household Occupants:</label>
                    <input type="num" class="form-control" placeholder="" id="famComposition" name="famComposition">
                </div>
                <div class="col-md-4">
                    <label for="pwd">Are you a person with disability?</label>
                    <input type="radio" value="YES" id="yes" name="pwd">YES
                    <input type="radio" value="NO"  id="no" name="pwd">NO
                </div>
                

            </div>
            <button type="button" onclick="submitData();" class="btn btn-dark">Register</button>
            <?php require 'residentScript.php'; ?>
        </form>

    </div>
    <script>
        var map = L.map('map').setView([10.7335, 122.5557], 16); 
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker;

    function onMapClick(e) {
           if (marker) {
               map.removeLayer(marker);
           }
           marker = L.marker(e.latlng).addTo(map);
           document.getElementById('latitude').value = e.latlng.lat;
           document.getElementById('longitude').value = e.latlng.lng;

          
           fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
               .then(response => response.json())
               .then(data => {
                   document.getElementById('presentAdd').value = data.display_name;
               })
               .catch(error => console.error('Error:', error));
       }

       map.on('click', onMapClick);

    </script>
    
    <br>

</body>

</html>