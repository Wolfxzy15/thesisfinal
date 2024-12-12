<!DOCTYPE html>
<html lang="en">
  <head>
    <title>nav</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
  </head>
  <body>
  <header>
        <div class="menu-toggle" id="menu-toggle">&#9776;</div>
        <a class="navbar-brand ml-2" href="#">
            <img src="images/citylogo.png"
                width="40" height="40" class="d-inline-block align-center" alt="">
        </a>
        <h1 style="margin-left: 0;">Barangay</h1>
        <a class="nav-link ml-auto" style="color:aliceblue" href="logout.php"><i class="fa-solid fa-sign-out pr-2"></i>Logout <span class="sr-only">(current)</span></a>
    </header>

    <aside class="sidebar" id="sidebar">
        <nav>
            <ul><br>
                <li><a class="nav-link" href="user_fam_register.php"><i class="fa-solid fa-user-plus pr-2"></i></i>Add Residents <span class="sr-only">(current)</span></a></l>
                <li><a class="nav-link" href="user_Families.php"><i class="fa-solid fa-people-roof pr-2"></i>Families<span class="sr-only">(current)</span></a></li>
                <li><a class="nav-link" href="user_Residents.php"><i class="fa-solid fa-edit pr-2"></i></i>Residents<span class="sr-only">(current)</span></a></li>
                <li><a class="nav-link" href="user_map.php"><i class="fa-solid fa-building pr-2"></i></i>Evac Map<span class="sr-only">(current)</span></a></li>
            </ul>
        </nav>
    </aside>
  </body>
  <script>
    document.getElementById('menu-toggle').addEventListener('click', function () {
    var sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
    //Map
    var mapControls = document.querySelectorAll('#map .leaflet-control');
    mapControls.forEach(control => {
        control.style.display = sidebar.classList.contains('active') ? 'none' : 'block';
    });
});

  </script>
  
</html>