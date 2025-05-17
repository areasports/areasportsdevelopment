<?php
require_once 'auth.php';
requireLogin();

$data = $_SESSION['data'];
$routine_data = $_SESSION['routine_data'];
$exercises = $_SESSION['exercises'];

usort($data, function($a, $b) {
    // Push empty strings to the end
    if ($a[5] === '') return 1;
    if ($b[5] === '') return -1;

    // Otherwise, compare numerically
    return intval($a[5]) - intval($b[5]);
});

$day = date('l');
$date = date('d/m/Y');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Area Sports Development</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        /* Simple Loader */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #1e1e1e;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease-out;
        }

        .loader-content {
            text-align: center;
        }

        .loader-title {
            color: #FFA500;
            font-size: 32px;
            font-weight: 900;
            letter-spacing: 3px;
            margin-bottom: 40px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            text-transform: uppercase;
            line-height: 1.2;
            max-width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .loader {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .dot {
            width: 12px;
            height: 12px;
            background: #FFA500;
            border-radius: 50%;
            animation: bounce 0.5s alternate infinite;
        }

        .dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes bounce {
            to {
                transform: translateY(-12px);
            }
        }

        @media (max-width: 768px) {
            .loader-title {
                font-size: 24px;
                margin-bottom: 30px;
                letter-spacing: 2px;
            }
            .dot {
                width: 10px;
                height: 10px;
            }
        }

        body {
            min-height: 100vh;
            background-color: #1e1e1e;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #f0f0f0;
            padding: 40px;
            position: relative;
            overflow-y: auto;
        }

        /* Add a semi-transparent overlay to improve content readability */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 0;
        }

        /* Ensure all content stays above the overlay */
        .header, .user-info-bar, .coach-note, .section-title, form {
            position: relative;
            z-index: 1;
        }

        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 40px;
        }

        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 48px;
            font-weight: 700;
            color: #FFA500;
            text-align: center;
        }

        .menu {
            margin-left: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #FFA500;
        }

        .section-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #FFA500;
        }

        .exercise {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #333;
            padding: 20px 0;
        }

        .exercise-name {
            flex: 1;
        }

        .exercise-name p {
            margin: 4px 0;
            color: #ccc;
        }

        .input-field {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .input-field input {
            width: 70px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #FFA500;
            background-color: #1e1e1e;
            color: #f0f0f0;
            border-radius: 8px;
            text-align: center;
        }

        .input-field span {
            font-size: 16px;
        }

        .view-icon {
            font-size: 1.5rem !important;
            margin-left: 12px;
            cursor: pointer;
            color: #FFA500;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #1e1e1e;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 700px;
            position: relative;
        }

        .modal-content iframe {
            width: 100%;
            height: 400px;
            border: none;
        }

        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            color: #FFA500;
        }

        @media (max-width: 768px) {
            body {
                padding: 20px;
            }

            .header h1 {
                font-size: 32px;
            }

            .section-title {
                font-size: 24px;
            }

            .input-field input {
                width: 60px;
            }
        }
        .btn-submit {
            background-color: #f06c1f;
            color: #f0f0f0;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            width: 100%;
        }
        .btn-submit:hover {
            background-color: #FFA500;
            color: #f0f0f0;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            width: 100%;
        }

        .user-info-bar {
            background: #1e1e1e;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info-item i {
            color: #FFA500;
            font-size: 20px;
        }

        .coach-note {
            background: #1e1e1e;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #333;
        }

        .coach-note-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .coach-note-header i {
            color: #FFA500;
            font-size: 20px;
        }

        .coach-note-content {
            color: #ccc;
            font-style: italic;
            line-height: 1.5;
        }

        .no-exercises-message {
            text-align: center;
            padding: 40px 20px;
            font-size: 24px;
            color: #FFA500;
            background: rgba(30, 30, 30, 0.7);
            border-radius: 10px;
            margin: 20px 0;
        }

        @media (max-width: 768px) {
            .no-exercises-message {
                font-size: 20px;
                padding: 30px 15px;
            }
        }

        .logout-container {
            margin-top: 40px;
            text-align: center;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .btn-logout {
            background-color: #dc3545;
            color: #f0f0f0;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-logout:hover {
            background-color: #c82333;
        }

        .btn-logout i {
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .user-info-bar {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .user-info-item:last-child {
                width: 100%;
            }

            .btn-logout {
                width: 100%;
                justify-content: center;
                padding: 10px 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Page Loader -->
    <div class="page-loader">
        <div class="loader-content">
            <div class="loader-title">AREA SPORTS DEVELOPMENT</div>
            <div class="loader">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>
    </div>

    <div class="header">
        <img src="./user-assets/assets/images/company-logo.png" alt="Gym Logo" class="logo">
    </div>
    
    <div class="user-info-bar">
        <div class="user-info-item">
            <i class="fas fa-user"></i>
            <span><?php echo $_SESSION['user_name']; ?></span>
        </div>
        <div class="user-info-item">
            <i class="fas fa-calendar-day"></i>
            <span><?php echo $day; ?></span>
        </div>
        <div class="user-info-item">
            <button onclick="logout()" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
    </div>

    <div class="coach-note">
        <div class="coach-note-header">
            <i class="fas fa-clipboard-list"></i>
            <h3 style="margin: 0; color: #FFA500;">Coach's Note</h3>
        </div>
        <div class="coach-note-content">
            <?php
                $coach_note = '';
                foreach($data as $item) {
                    if($item[4] == "note" && in_array($day, explode(',', $item[6]))) {
                        $coach_note = $item[1];
                        break;
                    }
                }
                echo $coach_note ? $coach_note : 'No notes for today.';
            ?>
        </div>
    </div>

    <div class="section-title">Exercises</div>
    <?php
    $exercisesFound = false;
    $count = 0;
    ?>
    <form method="post" id="exerciseForm">
        <input type="hidden" name="form_name" value="weight">
        <input type="hidden" name="date" value="<?php echo $date; ?>">
        <input type="hidden" name="name" value="<?php echo $_SESSION['user_name']; ?>">
        <?php
            foreach($data as $index => $item) { 
                $day_arr = explode(',', $item[6]);
                if($item[4] == "exercise" && in_array($day, $day_arr)) { 
                    $exercisesFound = true;
                    $count++;
        ?>
        <div class="row exercise">
            <div class="col-6">
                <div class="exercise-name">
                    <strong><?php echo $item[1]; ?><input type="hidden" name="excercise_<?php echo $index; ?>" value="<?php echo $item[1]; ?>"></strong>
                    <p><?php echo $item[2]; ?> sets X <?php echo $item[3]; ?> reps<input type="hidden" name="set_<?php echo $index; ?>" value="<?php echo $item[2]; ?>"><input type="hidden" name="rep_<?php echo $index; ?>" value="<?php echo $item[3]; ?>"></p>
                </div>
            </div>
            <div class="col-6">
                <div class="input-field">
                    <?php
                    for($i = 0; $i < $item[2]; $i++) { ?>
                        <div><input class="required" type="text" placeholder="kg" name="w_<?php echo $index; ?>"></div>
                    <?php } 
                        foreach($exercises as $exercise) {
                            if($exercise[0] == $item[1] && $exercise[1] !== "") {
                                $videoUrl = $exercise[1];
                    ?>
                    <span class="view-icon" onclick="openModal('<?php echo $videoUrl; ?>')">👁</span>
                    <?php
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
        <?php 
                }   
            }

            if (!$exercisesFound) {
        ?>
            <div class="no-exercises-message">
                <strong>No exercises scheduled for today!</strong>
            </div>
        <?php
            }
        ?>
        <?php if ($exercisesFound) { ?>
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-submit">Submit</button>
                    <input type="hidden" name="count" value="<?php echo $count; ?>">
                </div>
            </div>
        <?php } ?>
    </form>

    <div class="modal" id="videoModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">✖</span>
            <iframe id="videoFrame" src="" allowfullscreen></iframe>
        </div>
    </div>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="./user-assets/assets/js/script.js"></script>
    <script>
        // Hide loader after 3 seconds
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.querySelector('.page-loader').style.opacity = '0';
                setTimeout(function() {
                    document.querySelector('.page-loader').style.display = 'none';
                }, 500);
            }, 3000);
        });

        function openModal(videoUrl) {
            document.getElementById('videoModal').style.display = 'flex';
            document.getElementById('videoFrame').src = videoUrl;
        }

        function closeModal() {
            document.getElementById('videoModal').style.display = 'none';
            document.getElementById('videoFrame').src = '';
        }

        function logout() {
            $.ajax({
                url: 'auth.php',
                type: 'POST',
                data: {
                    action: 'logout'
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        window.location.href = 'index.php';
                    }
                }
            });
        }

        $(document).on('click', '.btn-submit', function() {
            
            if (!validate('user-submits')) {
                return false;
            }

            var form = $("#exerciseForm");
            $('.btn-submit').attr('disabled', 'disabled');
            $('.btn-submit').text('Please wait...');
            var url_gsheet = "https://script.google.com/macros/s/AKfycbwsG_udq5z9GwE1EcDvAN28280UN8aQ-EdSoIwJVglpW5VbCESV6OXuph5ayFTwQt_DGw/exec";

            // Convert form data to object
            var formData = {};
            var serializedData = form.serializeArray();
            $.each(serializedData, function() {
                formData[this.name] = this.value;
            });

            $.post(url_gsheet, formData, function(response){
                if (response.result == "success") {
                    if (response.result == "success") {
                        swal({
                            title: "Good Job!",
                            text: "Thank you for submitting",
                            icon: "success",
                        })
                        .then(function () {
                            window.location = window.location.href;
                        });
                        setTimeout(function () {
                            window.location = window.location.href;
                        }, 3000);
                    } else {
                        swal({
                            title: "Error!",
                            text: response.error,
                            icon: "error",
                        }).then(function () {
                            $('.btn-submit').removeAttr('disabled');
                            $('.btn-submit').text('Submit Again');
                        });
                    }
                }
                else {
                    swal({
                        title: "Error!",
                        text: "Something went wrong",
                        icon: "error",
                    }).then(function () {
                        $('.btn-submit').removeAttr('disabled');
                        $('.btn-submit').text('Submit Again');
                    });
                }
            });
        });
    </script>
</body>

</html>