<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Landing Page</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Electrolize&display=swap" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="images/feliB.png">
  <style>
   .center-image {
      display: flex;
      justify-content: center;
    }

    
    .navbar-brand {
      padding: 0;
    }
    .card-body {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    .btn-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px; 
    }
  </style>
</head>
<body style="background-image: url('https://dynamic-media-cdn.tripadvisor.com/media/photo-o/06/e0/4c/2d/iloilo-city-hall.jpg?w=1200&h=-1&s=1');
  background-repeat: no-repeat;
  background-size: cover;">
 <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">
    <img src="https://www.clipartmax.com/png/middle/98-987300_seal-of-the-barangay-barangay-logo-png.png" 
    width="85" height="70" class="d-inline-block align-center" alt="">
  </a>
  <h4 style="color: aliceblue;">Barangay</h4>
    <a class="btn btn-dark ml-auto" href="adminLogin.php" role="button">ADMIN</a>
  </div>
</nav><br>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <div class="center-image">
          <img src="images/admin.png" 
    width="90" height="90" alt="">

          </div>
          <h2 style="text-align: center;">RESIDENT REGISTRATION</h2>
        </div>  
        <div class="card-body">
        <h3>Don't have an account yet?</h3>
        <input type="hidden" id="action" value="">
            <button type="button" class="btn btn-success"><a href="registrationPage.php">Register here</a></button>
          </form>
          <br>
        </div>
      </div>
    </div>
  </div>
</div>

<script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js">
</script>


</body>
</html>

