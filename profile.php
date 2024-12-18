<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include your database connection file here
include 'connection.php';

// Get user_id from session
$user_id = $_SESSION['user_id'];

// Query to fetch profile picture and user details from useracc table
$sql = "SELECT * FROM useracc WHERE UserID = $user_id";
$result = mysqli_query($conn, $sql);

// Fetch user details
$profile_picture = '';
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $profile_picture = $row['profile'];
    $full_name = $row['fname'] . ' ' . $row['mname'] . '. ' . $row['lname'];
    $ufname = $row['fname'];
    $umname = $row['mname'];
    $ulname = $row['lname'];
    $bday = $row['bday']; // yyyy-mm-dd format
    $sex = $row['sex'];
    $address = $row['address'];
    $email = $row['email'];
    $uname = $row['Username'];
    $password = $row['Password'];
    $mobile = $row['mobile'];

    // Calculate the age based on bday
    $birthDate = new DateTime($bday);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y; // Difference in years
}

// Construct the full path to the profile picture
$profile_picture_path = 'img/UserProfile/' . $profile_picture;

// Close database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
     <link rel="icon" type="image/png" href="img/Logo/docmap-logo-1.png">
    <title>Profile</title>
    <style>


        .container-content {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            padding: 50px;
            border-radius: 8px;
           
           
            background-color: #fff;
            
        }

        .profile-picture {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
    position: relative;
    border: 1px solid lightgray; /* Adds a thin light gray border */
}


        .button-group {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn-custom {
            background-color: #9B2035;
            color: white;
            border: none;
            padding: 5px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 50px;
        }

        .btn-edit {
            background-color: #9B2035;
            border-radius: 50%;
            padding: 5px;
            width: 35px;
            height: 35px;
            color: white;
        }

        .full-name {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
        }
        
        /* Adjusted styling for left-aligned labels */
        .input-container {
            margin-bottom: 15px;
            text-align: left; /* Ensure text alignment for labels */
        }
        
        .input-container label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: left; /* Align label text to the left */
        }
        
        .input-container input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .modal-dialog {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }

        .modal-content {
            margin: auto;
        }
        /* Ensure modal takes up full width on smaller screens */
        .modal-dialog.custom-modal {
        max-width: 900px; /* Adjust as needed */
        margin: 1.75rem auto; /* Adjust vertical margin if needed */
    }

    .modal-body.d-flex {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-illustration {
        flex: 1;
        background: url("assets/images/passw.png") no-repeat center center;
        background-size: cover;
        height: 100%;
        min-height: 500px; /* Ensures a minimum height if the content is smaller */
        /* Optional: Add border or other styling for debugging */
       
    }

    .modal-form {
        flex: 1;
        padding: 20px;
        box-sizing: border-box;
       
    }

    .modal-form img {
        width: 100px;
        margin-bottom: 20px;
    }

    .modal-form h2 {
        margin-bottom: 20px;
        font-size: 28px;
        font-weight: 600;
        color: #333;
    }

    .modal-form .progress-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        position: relative;
        background-color:transparent;
    }

    .modal-form .progress-container {
        display: flex;
        align-items: center;
        width: 100%;
        position: relative;
        background-color:transparent;
    }

    .modal-form .progress-step {
        width: 40px;
        height: 40px;
        background-color: #D3D3D3;
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 600;
        position: relative;
        z-index: 1;
    }

    .modal-form .progress-step.completed {
        background-color: #9B2035;
    }

    .modal-form .progress-line {
        height: 4px;
        flex-grow: 1;
        background-color: #ddd;
        position: relative;
        top: 50%;
        transform: translateY(-50%);
        z-index: 0;
        margin: 0 10px;
    }

    .modal-form .progress-line.completed {
        background-color: #861c2e;
    }

    .modal-form p {
        margin-bottom: 20px;
        font-size: 14px;
        color: #777;
    }

    .modal-form input {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 6px;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        font-size: 16px;
    }

    .modal-form button {
        width: 100%;
        padding: 12px;
        background-color: #9B2035;
        border: none;
        border-radius: 90px;
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s;
        display: flex;
        justify-content: center; /* Center the button text */
    }

    .modal-form button:hover {
        background-color: #861c2e;
    }
    .award-container {
            display: flex;
            align-items: center;
            border-radius: 10px;
            padding: 10px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 10px 0;
            width: 100%;
            position: relative;
            background-color: #f0f0f0;
        }
        .award-icon-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #9b2035;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-right: 15px;
        }
        .award-icon {
            font-size: 24px;
            color: #fff;
        }
        .award-text {
            font-size: 16px;
            font-weight:bold;
        }
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }
    </style>
</head>

<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <?php include 'navbar.php'; ?>
    </section>
    <!-- SIDEBAR -->

    <!-- NAVBAR -->
    <section id="content">
        <!-- NAVBAR -->
        <?php include 'topbar.php'; ?>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <h5 class ="title">Profile</h5>
                <div class="container">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-5 mt-5">
                            <div class="container-content">
                                <?php if ($profile_picture): ?>
                                    <div class="profile-container text-center">
                                        <img src="<?php echo $profile_picture_path; ?>" alt="Profile Picture" class="profile-picture" id="profile-picture">
                                    </div>
                                <?php else: ?>
                                    <p class="text-center">No profile picture available.</p>
                                <?php endif; ?>

                                <div class="button-group text-center mt-3">
                                    <a href="#" class="btn-edit" data-toggle="modal" data-target="#uploadModal" id="btnedit">
                                        <i class='bx bx-image-add'style="font-size:20px;" ></i>
                                    </a>
                                    <a href="#" class="btn-custom" data-toggle="modal" data-target="#changePasswordModal">
                                        Change Credentials
                                    </a>
                                </div>

                                <div class="text-center mt-3 full-name"><?php echo $full_name; ?></div>
                                <p class="text-center mt-3 font-weight-bold">Your Awards</p>

                                <!-- Award Containers -->
                                <div class="award-container d-flex align-items-center mb-2">
                                    <div class="award-icon-container">
                                        <i class='bx bx-award award-icon'></i>
                                    </div>
                                    <span class="award-text ml-2">Exemplary Contributor</span>
                                </div>
                                <div class="award-container d-flex align-items-center mb-2">
                                    <div class="award-icon-container">
                                        <i class='bx bx-upload award-icon'></i>
                                    </div>
                                    <span class="award-text ml-2">Top Uploader</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-7 mt-5">
                            <div class="container-content position-relative">
                                <h4 class="d-flex justify-content-between align-items-center">
                                    User Details
                                <button class="btn-custom" id="editButton" data-toggle="modal" data-target="#editModal" style="width:60px;">Edit</button>
                                </h4>
                                <form>
                                    <div class="row">
                                        <div class="col-md-6 input-container">
                                            <label for="uname">Username</label>
                                            <input type="text" id="uname" name="uname" value="<?php echo htmlspecialchars($uname); ?>" readonly>
                                        </div>
                                        <div class="col-md-6 input-container">
                                            <label for="password">Password</label>
                                            <input type="password" id="password" name="password" value="*************" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 input-container">
                                            <label for="email">Email</label>
                                            <input type="text" id="email" name="email" value="<?php echo $email; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-7 input-container">
                                            <label for="fname">Full Name</label>
                                            <input type="text" id="fname" name="fname" value="<?php echo $full_name; ?>" readonly>
                                        </div>
                                        <div class="col-md-5 input-container">
                                            <label for="mobile">Mobile No.</label>
                                            <input type="text" id="mobile" name="mobile" value="<?php echo $mobile; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 input-container">
                                            <label for="bday">Birthday</label>
                                            <input type="text" id="bday" name="bday" value="<?php echo $bday; ?>" readonly>
                                        </div>
                                        <div class="col-md-4 input-container">
                                            <label for="age">Age</label>
                                            <input type="text" id="age" name="age" value="<?php echo $age; ?>" readonly>
                                        </div>
                                        <div class="col-md-4 input-container">
                                            <label for="sex">Sex</label>
                                            <input type="text" id="sex" name="sex" value="<?php echo $sex; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 input-container">
                                            <label for="address">Address</label>
                                            <input type="text" id="address" name="address" value="<?php echo $address; ?>" readonly>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>

                    <div class="modal" id="editModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit User Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editForm">
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <label for="editFname" class="form-label">First Name</label>
                                                <input type="text" class="form-control" id="editFname" />
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="editMname" class="form-label">Middle Name</label>
                                                <input type="text" class="form-control" id="editMname" />
                                            </div>
                                            <div class="col-sm-4">
                                                <label for="editLname" class="form-label">Last Name</label>
                                                <input type="text" class="form-control" id="editLname" />
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            
                                            <div class="col-sm-12">
                                                <label for="editEmail" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="editEmail" />
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-6">
                                                <label for="editMobile" class="form-label">Mobile</label>
                                                <input type="text" class="form-control" id="editMobile" />
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="editBday" class="form-label">Birthdate</label>
                                                <input type="date" class="form-control" id="editBday" />
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-6">
                                                <label for="editSex" class="form-label">Sex</label>
                                                <select class="form-control" id="editSex">
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="editAddress" class="form-label">Address</label>
                                                <input type="text" class="form-control" id="editAddress" />
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" id="saveChanges" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>




                    <!-- Modals -->
                    <!-- Upload Modal -->
                    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="uploadModalLabel">Upload Profile Picture</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="uploadForm" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="file">Choose file</label>
                                            <input type="file" class="form-control-file" id="file" name="file">
                                        </div>
                                        <button type="button" class="btn btn-primary" id="uploadBtn">Upload</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password Modal -->
                    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content"style ="width:900px;">
                                <div class="modal-header">
                                    
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" >
                                    <div class="row">
                                        <!-- Illustration Section -->
                                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                                            <div class="modal-illustration">
                                                <!-- Placeholder for illustration -->
                                            </div>
                                        </div>

                                        <!-- Form Section -->
                                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                                            <div class="modal-form text-center">
                                                <img src="img/Logo/docmap.png" alt="Logo" class="img-fluid mb-3">
                                                <h2>Change Credentials</h2>
                                                <div class="progress-bar mb-3">
                                                    <div class="progress-container">
                                                        <div class="progress-step completed">1</div>
                                                        <div class="progress-line"></div>
                                                        <div class="progress-step">2</div>
                                                        <div class="progress-line"></div>
                                                        <div class="progress-step">3</div>
                                                    </div>
                                                </div>
                                                <p>Enter your email address to receive an OTP.</p>
                                                <form action="changePasswordForm" method="POST">
                                                    <input type="hidden" id="userId" value="<?php echo $_SESSION['user_id']; ?>">
                                                    <div class="form-group">
                                                        <input type="email" name="email" placeholder="Your email address" class="form-control" required>
                                                    </div>
                                                    <button type="submit" id="changePasswordBtn" class="btn btn-primary btn-block">Send OTP</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </main>

        <!-- MAIN -->
    </section>
    <!-- NAVBAR -->
    <script src="assets/js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>
document.getElementById('editButton').addEventListener('click', function () {
    // Populate the modal fields with existing data
    const fname = "<?php echo isset($ufname) ? htmlspecialchars($ufname, ENT_QUOTES, 'UTF-8') : ''; ?>";
    const mname = "<?php echo isset($umname) ? htmlspecialchars($umname, ENT_QUOTES, 'UTF-8') : ''; ?>";
    const lname = "<?php echo isset($ulname) ? htmlspecialchars($ulname, ENT_QUOTES, 'UTF-8') : ''; ?>";
    const email = "<?php echo isset($email) ? htmlspecialchars($email, ENT_QUOTES, 'UTF-8') : ''; ?>";
    const mobile = "<?php echo isset($mobile) ? htmlspecialchars($mobile, ENT_QUOTES, 'UTF-8') : ''; ?>";
    const bday = "<?php echo isset($bday) ? htmlspecialchars($bday, ENT_QUOTES, 'UTF-8') : ''; ?>";
    const sex = "<?php echo isset($sex) ? htmlspecialchars($sex, ENT_QUOTES, 'UTF-8') : ''; ?>";
    const address = "<?php echo isset($address) ? htmlspecialchars($address, ENT_QUOTES, 'UTF-8') : ''; ?>";

    document.getElementById('editFname').value = fname;
    document.getElementById('editMname').value = mname;
    document.getElementById('editLname').value = lname;
    document.getElementById('editEmail').value = email;
    document.getElementById('editMobile').value = mobile;
    document.getElementById('editBday').value = bday;
    document.getElementById('editSex').value = sex;
    document.getElementById('editAddress').value = address;

    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
});

document.addEventListener('DOMContentLoaded', (event) => {
    // Your JavaScript code here
    document.getElementById('saveChanges').addEventListener('click', function () {
        // Gather data from the modal fields
        const formData = {
            action: 'update',
            fname: document.getElementById('editFname').value,
            mname: document.getElementById('editMname').value,
            lname: document.getElementById('editLname').value,
            email: document.getElementById('editEmail').value,
            mobile: document.getElementById('editMobile').value,
            bday: document.getElementById('editBday').value,
            sex: document.getElementById('editSex').value,
            address: document.getElementById('editAddress').value
        };

        // Send data to the backend using AJAX
        fetch('edit_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message
                }).then(() => { // After SweetAlert is closed
                    // Refresh the page
                    location.reload(); 
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while updating user details.'
            });
        });
    });
});

</script>

<script>
$(document).ready(function () {
    // Upload Button Click Event
    $('#uploadBtn').click(function (e) {
        e.preventDefault();

        var fileInput = $('#file')[0];
        if (!fileInput.files.length) {
            Swal.fire({
                icon: 'error',
                title: 'No file selected',
                text: 'Please choose a file to upload.'
            });
            return;
        }

        var formData = new FormData($('#uploadForm')[0]);

        $.ajax({
            url: 'picupload.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                try {
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        $('#profile-picture').attr('src', 'img/UserProfile/' + data.filename);
                        $('#uploadModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Profile picture updated successfully.'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Invalid response from server.'
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload failed',
                    text: textStatus
                });
            }
        });
    });

    // Change Password Button Click Event
    $('#changePasswordBtn').click(function (e) {
        e.preventDefault();
        var email = $('#email').val();
        var userId = $('#userId').val(); // Assuming you have a hidden input for userId

        $.ajax({
            url: 'changepassword.php',
            type: 'POST',
            data: { email: email, userId: userId },
            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'OTP Sent',
                        text: response.message,
                        icon: 'success'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'verify_otp2.php?email=' + response.email + '&userId=' + userId;
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                Swal.fire({
                    icon: 'error',
                    title: 'Request failed',
                    text: textStatus
                });
            }
        });
    });
});
</script>


</body>

</html>

