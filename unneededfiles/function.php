<?php
session_start();

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

// REGISTER
function register(){
  global $conn;

  $lastName = $_POST["lastName"];
  $firstName = $_POST["firstName"];
  $middleName = $_POST["middleName"];
  $address = $_POST["address"];
  $birthDate = $_POST["birthDate"];
  $contactNum = $_POST["contactNum"];
  $username = $_POST["username"];
  $password = $_POST["password"];

  if(empty($lastName) || empty($firstName) || empty($middleName) || empty($address)|| empty($birthDate) || empty($contactNum) || empty($username) || empty($password)){
    echo "Please Fill Out The Form!";
    exit;
  }

  $user = mysqli_query($conn, "SELECT * FROM tbl_user WHERE username = '$username'");
  if(mysqli_num_rows($user) > 0){
    echo "Username Has Already Taken";
    exit;
  }

  $query = "INSERT INTO tbl_user (lastName, fName, mName, presentAdd, provAdd, sex, civilStat, dateOfBirth, placeOfBirth, 
  height, weight, contactNo, religion, username, password) VALUES('$lastName', '$firstName', '$middleName', '$address', '$birthDate', '$contactNum','$username', '$password')";
  mysqli_query($conn, $query);
  echo "Registration Successful";
}

// LOGIN
function login(){
  global $conn;

  $username = $_POST["username"];
  $password = $_POST["password"];

  $user = mysqli_query($conn, "SELECT * FROM tbl_user WHERE username = '$username'");

  if(mysqli_num_rows($user) > 0){

    $row = mysqli_fetch_assoc($user);

    if($password == $row['password']){
      echo "Login Successful";
      $_SESSION["login"] = true;
      $_SESSION["userID"] = $row["userID"];
      
    }
    else{
      echo "Wrong Password";
      exit;
    }
  }
  else{
    echo "User Not Registered";
    exit;
  }
}
?>
