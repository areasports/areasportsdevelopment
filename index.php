<?php
require_once 'auth.php';
isLogin();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Sports Development</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="user-assets/assets/css/style.css">
</head>

<body>
    <div class="container">
        <div class="login-section" id="loginSection">
            <form class="login-form" id="loginForm">
                <div class="logo-section">
                    <img src="./user-assets/assets/images/company-logo.png" alt="" style="width: 80%; height: auto;">
                </div>

                <div class="form-group">
                    <input type="text" id="loginUsername" class="required user-name" name="name">
                    <label for="loginUsername">Name</label>
                </div>

                <div class="form-group">
                    <input type="password" id="loginPassword" class="password required" name="password">
                    <i class="fas fa-eye-slash toggle-password"></i>
                    <label for="loginPassword">Password</label>
                </div>

                <button type="button" class="btn btn-main w-100 login-btn">
                    Login
                </button>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="user-assets/assets/js/script.js"></script>
    <script>
        $('.toggle-password').click(function () {
            const input = $(this).parent().find('input');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                input.attr('type', 'password');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });

        $('.login-btn').on('click', function (e) {
        
            if(validate('login-submits')){
                $('.login-btn').text('Checking Password...');
                $.post('https://script.google.com/macros/s/AKfycbwsG_udq5z9GwE1EcDvAN28280UN8aQ-EdSoIwJVglpW5VbCESV6OXuph5ayFTwQt_DGw/exec', {
                    name: $('#loginUsername').val(),
                    password: $('#loginPassword').val(),
                    check_user: 'yes',
                    type: 'client'
                }, function(response){
                    if(response.result == 'success'){
                        // Store user data in session via AJAX
                        $.ajax({
                            url: 'auth.php',
                            type: 'POST',
                            data: {
                                action: 'login',
                                data: response.data || {},
                                routine_data: response.routine_data || {},
                                exercises: response.exercises || [],
                                user_name: $('#loginUsername').val()
                            },
                            dataType: 'json',
                            contentType: 'application/x-www-form-urlencoded',
                            success: function(sessionResponse) {
                                console.log('Auth Success Response:', sessionResponse); // Debug log
                                if(sessionResponse.success) {
                                    window.location.href = 'user.php';
                                } else {
                                    swal("Error!", "Failed to create session", "error");
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log('Auth Error Response:', {
                                    status: status,
                                    error: error,
                                    responseText: xhr.responseText
                                }); // Debug log
                                swal("Error!", "Session creation failed: " + error, "error");
                            }
                        });
                    } 
                    else if(response.result == 'not match'){
                        swal("Error!", "Invalid username or password", "error");
                        $('.login-btn').text('Login');  
                    }
                    else {
                        swal("Error!", "Invalid username or password", "error");
                        $('.login-btn').text('Login');
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.log('API Error:', {
                        status: textStatus,
                        error: errorThrown,
                        response: jqXHR.responseText
                    });
                    swal("Error!", "Failed to connect to authentication service", "error");
                });
            }
        });
    </script>
</body>

</html>