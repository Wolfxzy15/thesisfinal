<?php


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Login Page</title>
  <link rel="icon" type="image/x-icon" href="images/feliB.png">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Electrolize&display=swap" rel="stylesheet">
  <style>
    body{
      background-image: url("images/bglogin.jpg");
      background-repeat: no-repeat;
      background-size: 2000px;
      font-family: "Electrolize", sans-serif;
    }

    .card{
      background-color: #86C232;
    }

    .card-header{
      background-color: #61892F;
    }
    
    a
    {
      color: white;
    }
    .adminTitle{
      font-weight: bold;
      font-size: 15px;
      text-align: right;
    }

    footer{
      padding-top:170px;
      width: 1903px;
    }
    .containerfoot{
      position: fixed;
      width: 2000px;
      display:flex;
      background-color: #86C232;
      height: 175px;
      padding-top: 20px;
      justify-content: space-evenly;
    }

    .adminbutton{
      display:flex;
      flex-wrap: nowrap;
      flex-direction: row-reverse;
      height:35px;
    }

    .navbar-light{
      background-color: #86C232;
      width: 1920px;
    }

  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand">
    <img src="https://scontent.fmnl25-3.fna.fbcdn.net/v/t39.30808-1/269714038_117985617383471_9121652733818886220_n.jpg?stp=dst-jpg_p200x200&_nc_cat=103&ccb=1-7&_nc_sid=5f2048&_nc_eui2=AeFQbJBfCTd5qNPgExeI79D_1trQUu9GAP3W2tBS70YA_TkPGe5SuJKI7pygeS4qtxh7QCbCuH3certud4-CW8Qr&_nc_ohc=-adwALT8KfcAb4sqLXV&_nc_ht=scontent.fmnl25-3.fna&oh=00_AfDU-RmiSWCfO9kr_BXvn-KGcyccZT6dMoHTZI_HQOzxRw&oe=66305058" 
    width="70" height="70" alt="Logo">
      <strong>Barangay</strong>
    </a>
  
  <div class="adminbutton"> 
  <button type="button" class="btn btn-dark">
  <p class="adminTitle"><a href="adminlogin.php">ADMIN</a></p>
  </button>
</div>
</nav>


<br><br><br>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h2>Login</h2>
        </div>  
        <div class="card-body">
        <form autocomplete="off" action="" method="post">
        <input type="hidden" id="action" value="login">
            <div class="form-group">
              <label for="username"><strong>Username</strong></label>
              <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
              <label for="password"><strong>Password</strong></label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="button" onclick="submitData();" class="btn btn-dark">Login</button>
           
          </form>
          <br>
          <p class="mb-0">Don't have an account? <a href="register.php">Register Here</a>.</p><br>
          
          <?php require 'script.php'; ?>
          
        </div>
      </div>
    </div>
  </div>
</div>
<br>
<br>


<footer>
    <div class="containerfoot">
        <div class="row">
            <div class="col-md-4">
                <h5>Contact Us</h5>
                <p>Email: FelimarsPC@gmail.com</p>
                <p>Phone: +1 (123) 69-6900</p>
            </div>
            <div class="col-md-4">
                <h5>Follow Us: </h5>
                <p>Facebook</p>
                <p>Twitter</p>
                <p>Instagram</p>
            </div>
            <div class="col-md-4">
                <h5>Address</h5>
                <p>Brgy, Lopez Jaena St, Beside Acclaim, Jaro, Iloilo City, 5000 Iloilo</p>
            </div>
        </div>
    </div>
</footer>


</body>
</html>

