

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js">
</script>
<script type="text/javascript">
  function submitData() {
    $(document).ready(function() {
      var data = {
        firstName: $("#firstName").val(),
        lastName: $("#lastName").val(),
        username: $("#username").val(),
        password: $("#password").val(),
        cpassword: $("#cpassword").val(),
        emailAdd: $("#emailAdd").val(),
        userType: $("#userType option:selected").val(),
        action: $("#action").val(),
      };

      $.ajax({
        url: 'loginFunction.php',
        type: 'post',
        data: data,
        success: function(response) {
          if (response.trim() == "Admin Login Successful") {
            Swal.fire({
              icon: 'success',
              title: 'Admin Login Success',
              showConfirmButton: false,
              timer: 1000,
            }).then(() => {
              window.location.href = 'evacMap.php';
            });
          }
          else if (response.trim() == "User Login Successful") {
            Swal.fire({
              icon: 'success',
              title: 'User Login Success',
              showConfirmButton: false,
              timer: 1000,
            }).then(() => {
              window.location.href = 'evacMap.php';
            });
          }else if (response.trim() == "Registration Successful") {  //ADMIN REGISTRATION PART
            Swal.fire({
              icon: 'success',
              title: 'Registered Successfully',
              ConfirmButton: "OK",
            }).then(() => {
              window.location.reload();
            });
          }else if (response.trim() == "Username Has Already Taken") {
            Swal.fire({
              icon: 'warning',
              title: 'Username Has Already Taken',
              ConfirmButton: "OK",
            });
          }else if (response.trim() == "Wrong Password") {
            Swal.fire({
              icon: 'warning',
              title: 'Wrong Password',
              showConfirmButton: false,
              timer: 700,
            });
          }
          else if (response.trim() == "Passwords do not match!") {
            Swal.fire({
              icon: 'warning',
              title: 'Passwords do not match!',
              showConfirmButton: false,
              timer: 1500,
            });
          }else if (response.trim() == "Admin Not Registered") {
            Swal.fire({
              icon: 'warning',
              title: 'Admin Not Registered',
              ConfirmButton: "OK",
            });
          }
          else {
            Swal.fire({ 
              icon: 'error',
              title: 'Error',
              text: response.trim(),
              showConfirmButton: false,
              timer: 2500
            });
          }
        }
      });
    });
  }
</script>