<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js">
</script>
<script type="text/javascript">
    function submitData() {
        $(document).ready(function() {
            var data = $("#addFamilyMemberForm").serialize();

            var isFormIncomplete = false;
            $(".form-control").each(function() {
                if ($(this).val() === "") {
                    isFormIncomplete = true;
                    return false; 
                }
            });

            if (isFormIncomplete) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Form Incomplete',
                    text: 'Please fill up the form completely!',
                    confirmButtonText: 'OK',
                });
                return;
            }

            $.ajax({
                url: 'residentFunction.php',
                type: 'post',
                data: data,
                success: function(response) {
                    if (response.trim() == "Resident Added Successfully!") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response,
                            confirmButtonText: 'OK',
                        }).then(function() {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            confirmButtonText: 'OK',

                        });
                    }
                }
            });
        });
    }
</script>