<?php
session_start();
include 'db.php';

// IF
if(isset($_POST["update"])){
  $kinship = $_POST["kinship"];
  $lastName = $_POST["lastName"];
  $fName = $_POST["fName"];
  $mName = $_POST["mName"];
  $age = $_POST["age"];
  $presentAdd = mysqli_real_escape_string($conn, $_POST["presentAdd"]);
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


  if(empty($kinship) || empty($lastName) || empty($fName) || empty($mName) || empty($presentAdd)||empty($age)|| empty($provAdd) || empty($civilStat) || empty($dateOfBirth) || empty($placeOfBirth) || empty($height) || empty($weight) || empty($contactNo) || empty($religion) || empty($emailAdd)||
  empty($famComposition)|| empty($pwd)){
    echo "Please Fill Out The Form!";
    exit;
  }

  $presentAddEscaped = mysqli_real_escape_string($conn, $presentAdd);

  $query = "UPDATE tbl_residents SET kinship = '$kinship', lastName = '$lastName', fName = '$fName', mName = '$mName', age = '$age', presentAdd = '$presentAdd', 
  provAdd = '$provAdd', sex = '$sex', civilStat = '$civilStat, dateOfBirth = '$dateOfBirth', placeOfBirth = '$placeOfBirth', height = '$height',
  weight = '$weight', contactNo = '$contactNo', religion = '$religion', emailAdd = '$emailAdd', famComposition = '$famComposition', pwd = '$pwd', latitude = ' $latitude', longitude = ' $longitude' where id=$residentID";
  
  $query_run =mysqli_query($conn, $query);

  if($query_run){
    header("Location: index.php");
    exit(0);
  }
  
}
?>
