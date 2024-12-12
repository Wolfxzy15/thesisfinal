<?php


include 'db.php';

// IF
if(isset($_POST["action"])){
  if($_POST["action"] == "register"){
    register();
  }
  else if($_POST["action"] == "login"){
    login();
  }
  
}

// REGISTER RESIDENT
function register(){
  global $conn;

  $kinship = $_POST["kinship"];
  $lastName = $_POST["lastName"];
  $fName = $_POST["fName"];
  $mName = $_POST["mName"];
  $age = $_POST["age"];
  $presentAdd = $_POST["presentAdd"];
  $provAdd = $_POST["provAdd"];
  $sex = $_POST["sex"];
  $civilStat = $_POST["civilStat"];
  $dateOfBirth = $_POST["dateOfBirth"];
  $placeOfBirth = $_POST["placeOfBirth"];
  $height = $_POST["height"];
  $weight = $_POST["weight"];
  $contactNo = $_POST["contactNo"];
  $religion = $_POST["religion"];
  $emailAdd = $_POST["emailAdd"];
  $famComposition = $_POST["famComposition"];
  $pwd = $_POST["pwd"];
  $latitude = $_POST['latitude'];
  $longitude = $_POST['longitude'];

  if(empty($kinship) || empty($lastName) || empty($fName) || empty($mName) || empty($presentAdd)||empty($age)|| empty($provAdd) || empty($civilStat) || empty($dateOfBirth) || empty($placeOfBirth) || empty($height) || empty($weight) || empty($contactNo) || empty($religion) || empty($emailAdd)
  || empty($famComposition) || empty($pwd)){
    echo "Please Fill Out The Form!";
    exit;
  }
  $presentAddEscaped = mysqli_real_escape_string($conn, $presentAdd);
  $query = "INSERT INTO tbl_residents (kinship, lastName, fName, mName, age, presentAdd, provAdd, sex, civilStat, dateOfBirth,
  placeOfBirth, height, weight, contactNo, religion, emailAdd, famComposition, pwd, latitude, longitude) VALUES('$kinship', '$lastName', '$fName', '$mName', '$age', '$presentAdd', '$provAdd', '$sex',
  '$civilStat', '$dateOfBirth', '$placeOfBirth', '$height', '$weight', '$contactNo', '$religion', '$emailAdd', '$famComposition', '$pwd', '$latitude', '$longitude')";
  $result = mysqli_query($conn, $query);

  if($result){
    echo"Resident Added Successfully!";
  }
  echo " " .mysqli_error($conn);
}



