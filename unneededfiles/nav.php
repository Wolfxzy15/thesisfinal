<!DOCTYPE html>
<html lang="en">
  <head>
    <title>nav</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <link rel="stylesheet" href="include/style.css">
  </head>


  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">
    <img src="https://www.clipartmax.com/png/middle/98-987300_seal-of-the-barangay-barangay-logo-png.png" 
    width="90" height="80" class="d-inline-block align-center" alt="">
  </a>
  <h4 style="color: aliceblue;">Barangay</h4>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active pl-4">
        <a class="nav-link" href="index.php"><i class="fa-solid fa-house pr-2"></i>Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item active pl-4">
        <a class="nav-link" href="registerResident.php"><i class="fa-solid fa-user-plus pr-2"></i></i>Add Residents <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item active pl-4">
        <a class="nav-link" href="displayResidents.php"><i class="fa-solid fa-edit pr-2"></i></i>Edit Residents <span class="sr-only">(current)</span></a>
      </li>

    </ul>
    
    <a class="btn btn-dark" href="logout.php" role="button">Logout</a>
  </div>
</nav><br>
  </body>
</html>