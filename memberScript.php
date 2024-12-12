<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
  function submitData() {
    var data = $("#addFamilyMemberForm").serialize();

    $.ajax({
      url: 'addFmemberFunction.php',
      type: 'POST',
      data: data,
      success: function(response) {
        if (response.trim() === "success") {
          Swal.fire({
            icon: 'success',
            title: 'Family member added successfully',
            showConfirmButton: false,
            timer: 1000,
          }).then(() => {
            window.location.reload();
          });
        } else if (response.trim() === "incomplete") {
          Swal.fire({
            icon: 'warning',
            title: 'Form incomplete',
            showConfirmButton: false,
            timer: 1000,
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: response.trim(),
            ConfirmButton: "OK",
          });
        }
      }
    });
  }
</script>
