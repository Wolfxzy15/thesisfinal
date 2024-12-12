<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#updateAddress').on('click', function() {
        var family_id = <?php echo json_encode($family_id); ?>; 
        var latitude = $('#latitude').val();
        var longitude = $('#longitude').val();
        var presentAddress = $('#presentAddress').val(); 

        $.ajax({
            type: 'POST',
            url: 'updateAddress.php', 
            data: {
                family_id: family_id,
                latitude: latitude,
                longitude: longitude,
                presentAddress: presentAddress
            },
            success: function(response) {
               
                if (response.trim() == 'success') {
                    Swal.fire({
                        title: 'Success',
                        text: 'Address updated successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(); 
                        }
                    });
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'An error occurred while updating.', 'error');
            }
        });
    });
});
</script>
