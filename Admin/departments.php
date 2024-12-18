<?php
session_start();

// Include the database connection file
require_once 'connection.php'; // Adjust the path as needed

// Fetch teacher data
$query = "SELECT CONCAT(fname, ' ', mname, ' ', lname) AS fullname, UserID, profile 
          FROM useracc 
          WHERE role = 'Teacher' 
          AND Status = 'Approved' 
          AND dept_ID IS NULL";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Prepare options for the select dropdown
    $teacherOptions = '';
    while ($row = $result->fetch_assoc()) {
        // Get profile picture URL
        $profilePicUrl = !empty($row['profile']) ? '../img/UserProfile/' . $row['profile'] : '../img/defaultProfile.png'; // Use a default image if profile is not set

        // Generate option with profile picture
        $teacherOptions .= '<option value="' . $row['UserID'] . '">' . 
                           '<img src="' . $profilePicUrl . '" alt="Profile" style="width:20px; height:20px; vertical-align: middle;"> ' . 
                           $row['fullname'] . 
                           '</option>';
    }
} else {
    $teacherOptions = '<option value="">No teachers available</option>';
}
?>



<!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
        <link rel="icon" type="image/png" href="../img/Logo/docmap-logo-1.png">
    <title>Departments</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        
    <style>
        .container {
            width: 100%; /* Ensure full width for responsiveness */
            border-radius: 20px;
            margin: 0 auto;
            padding: 0 15px; /* Added horizontal padding for spacing */
            
        }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: -10px;
        margin-bottom: 20px;
    }



    .icon-button {
        background-color: #9b2035;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 90px;
        transition: background-color 0.3s;
        font-size: 24px;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        height:60px;
        width: 60px;
    }

    .icon-button:hover {
        background-color: #861c2e;
    }

    .icon-button i {
        margin: 0; /* Remove any default margins */
    }

    .departments-container {
        margin: 20px 0;
        max-width: 100%;
        
       
        
    }

    .department {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-sizing: border-box; /* Ensures padding and border are included in the element's total width and height */
        position: relative; 
        height: 150px;
    }

    .department h3 {
    margin: 0;
    font-size: 18px; /* Adjust the font size as needed */
    font-weight: bold;
    word-wrap: break-word; /* Allows text to wrap to the next line if necessary */
    word-break: break-word; /* Breaks long words to fit the container */
    white-space: normal; /* Allow the text to break into new lines */
    overflow: hidden; /* Prevents overflow, though not strictly necessary with word-wrap */
    color:#9b2035;
    }


    .department p {
        margin: 0 0 10px; /* Margin to space out the text from the bottom */
        font-size: 13px; /* Text size */
        color: grey; /* Text color */
        white-space: normal; /* Allow wrapping of the text */
        overflow: hidden; /* Hide overflowed text */
        text-overflow: ellipsis; /* Show ellipsis for overflowed text */
        width: 100%; /* Make sure it consumes the full width of the container */
        display: -webkit-box; /* Use a flexible box model */
        -webkit-line-clamp: 2; /* Limit to 2 lines, or more if needed */
        -webkit-box-orient: vertical; /* Orient the box vertically */
        line-height: 1.5em; /* Set line height to make text more readable */
    }




    button {
        padding: 10px 20px;
        background-color: #28a745;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s;
        font-size: 14px; /* Adjust font size */
    }


    /* Updated styles for the edit and delete buttons */
    .btn-icon {
        padding: 10px 15px; /* Set the same padding for both buttons */
        background-color: #28a745; /* Green for edit button */
        color: white;
        border: none;
        border-radius: 5px; /* Rounded corners */
        transition: background-color 0.3s;
        font-size: 14px;
        display: flex; /* Flex display for icon alignment */
        align-items: center;
        justify-content: center;
    }

    

    .btn-small {
        background-color: #dc3545; /* Red color for delete button */
        transition: background-color 0.3s;
    }

    .btn-small:hover {
        background-color: #c82333; /* Darker red color on hover */
    }


    /* Modal Styles */
    .modal {
        display: none; 
        position: fixed; 
        z-index: 1; 
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0,0,0,0.5); 
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        width: 90%;
        max-width: 500px; 
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        position: relative;
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }

    .close {
        position: absolute;
        top: 10px;
        right: 10px;
        color: #aaa;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        transition: color 0.3s;
    }

    .close:hover,
    .close:focus {
        color: #333;
    }

    input[type="text"], textarea {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    textarea {
        resize: vertical;
        height: 100px;
    }

    form button {
        background-color: #28a745;
        border-radius: 5px;
    }

    form button:hover {
        background-color: #218838;
    }

    /* Style for arrow icon link */
    .arrow-link {
        position: absolute;
        bottom: 10px; /* Adjust as needed */
        right: 10px; /* Adjust as needed */
        color: black; /* Change the arrow color to red */
        font-size: 24px; /* Size of the arrow icon */
        text-decoration: none;
        transition: color 0.3s, border 0.3s; /* Transition for both color and border */
        margin-left:50px;

    }
    .arrow-link:hover i {
    color: #9b2035; /* Change the icon color when hovered */
    }

    .dropdown {
        position: absolute;
        top: 10px;   /* Adjust as needed */
        right: 10px; /* Adjust as needed */
        /* Add spacing between the heading and dropdown */

    }

    .dropdown-toggle {
        background: none;
        border: none;
        cursor: pointer;
        background-color: transparent;
        color:black;
        margin-left: 10px;
    }

    .dropdown-menu {
        display: none; /* Hide the dropdown menu by default */
        position: absolute; /* Position dropdown menu absolutely */
        background-color: white; /* Background color for dropdown */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Optional: Add shadow for depth */
        border-radius: 4px; /* Optional: Rounded corners */
        min-width: 120px; /* Set a minimum width for the dropdown menu */
        max-width: 200px; /* Set a maximum width for the dropdown menu */
        width: auto; /* Allows the width to adjust based on content */
    }

    .dropdown.active .dropdown-menu {
        display: block;
    }

    .dropdown-menu button {
        display: block;
        width: 100%;
        text-align: center;
        padding: 10px 15px;
        border: none;
        background: none;
        cursor: pointer;
        color: #333;
        font-size: 14px;
        
    }

    .dropdown-menu button:hover {
        background-color: #f0f0f0;
    }
    .dropdown button:hover {
        background-color: #f0f0f0;
        
    }

    /* Modal styles */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1000; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgba(0, 0, 0, 0.5); /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        background-color: #fefefe; /* White background */
        margin: 15% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888; /* Gray border */
        width: 80%; /* Could be more or less, depending on screen size */
        max-width: 600px; /* Maximum width */
        border-radius: 8px; /* Rounded corners */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect */
    }

    /* Close button */
    .close {
        color: #aaa; /* Gray color */
        float: right; /* Float right */
        font-size: 28px; /* Font size */
        font-weight: bold; /* Bold text */
    }

    .close:hover,
    .close:focus {
        color: black; /* Black on hover */
        text-decoration: none; /* No underline */
        cursor: pointer; /* Pointer cursor */
    }

    /* Form styles */
    form {
        display: flex; /* Use flexbox for alignment */
        flex-direction: column; /* Stack items vertically */
    }

    /* Dropdown styles */
    .form-control {
        padding: 10px; /* Padding for comfort */
        margin: 10px 0; /* Spacing between elements */
        border: 1px solid #ccc; /* Light gray border */
        border-radius: 4px; /* Rounded corners */
        font-size: 16px; /* Font size */
    }

    /* Button styles */
    button {
        padding: 10px; /* Padding for comfort */
        background-color: #007bff; /* Bootstrap primary color */
        color: white; /* White text */
        border: none; /* No border */
        border-radius: 4px; /* Rounded corners */
        font-size: 16px; /* Font size */
        cursor: pointer; /* Pointer cursor */
        transition: background-color 0.3s ease; /* Smooth transition */
    }

    button:hover {
        background-color: #0056b3; /* Darker blue on hover */
    }
    .profile-image1 {
    width: 22px !important;
    height: 22px !important;
    border-radius: 50%;
    object-fit: cover;
    margin-right:5px;
    
    }
    .no-dept-head {
        color: #6c757d; /* Optional: use muted color */
        font-style: italic;
    }
    .depth-name{
        font-size:13px;
        font-weight:bold;
        color:gray;
    }





    </style>
    </head>
    <body>
        <!-- SIDEBAR -->
        <section id="sidebar">
            <?php include 'navbar.php'; ?>
        </section>
        <!-- SIDEBAR -->
        <section id="content">
            <!-- NAVBAR -->
            <?php include 'topbar.php'; ?>
            <!-- NAVBAR -->

            <!-- MAIN -->
            <main>
                <div class="header">
                    <h1 class ="title">Departments</h1>
                    <button id="openModalBtn" class="icon-button">
                        <i class='bx bx-plus'></i>
                    </button>
                </div>
                <div class="container">
                    <div class="departments-container" id="departmentsContainer">
                        <!-- Departments will be loaded here -->
                    </div>
                </div>
            </main>
        
            <!-- Create Department Modal -->
            <div id="createModal" class="modal">
                <div class="modal-content">
                    <span class="close" id="createModalClose">&times;</span>
                    <h2>Create Department</h2>
                    <form id="createDepartmentForm">
                        <label for="departmentName">Department Name:</label><br>
                        <input type="text" id="departmentName" name="departmentName" required><br>
                        <label for="departmentInfo">Department Info:</label><br>
                        <textarea id="departmentInfo" name="departmentInfo" required></textarea><br>
                        <button type="submit">Create</button>
                    </form>
                </div>
            </div>

            <!-- Update Department Modal -->
            <div id="updateModal" class="modal">
                <div class="modal-content">
                    <span class="close" id="updateModalClose">&times;</span>
                    <h2>Update Department</h2>
                    <form id="updateDepartmentForm">
                        <input type="hidden" id="updateDeptID" name="deptID">
                        <label for="updateDepartmentName">Department Name:</label><br>
                        <input type="text" id="updateDepartmentName" name="departmentName" required><br>
                        <label for="updateDepartmentInfo">Department Info:</label><br>
                        <textarea id="updateDepartmentInfo" name="departmentInfo" required></textarea><br>
                        <button type="submit">Update</button>
                    </form>
                </div>
            </div>
            <div id="addDeptHeadModal" class="modal">
            <div class="modal-content">
                <span class="close" id="addDeptHeadModalClose">&times;</span>
                <h2>Add Department Head</h2>
                <form id="addDepartmentHeadForm" onsubmit="submitForm(event)">
                    <input type="hidden" id="deptHeadDeptID" name="deptID"> <!-- Hidden field to hold department ID -->
                    <select class="form-control" id="teacherSelect" name="teacherID" required>
                        <option value="">Select a Teacher</option>
                        <?= $teacherOptions; ?> <!-- Populate options here -->
                    </select>
                    <button type="submit">Add Department Head</button>
                </form>
            </div>
        </div>

        </section>

        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="assets/js/script.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- JavaScript Code -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function submitForm(event) {
    event.preventDefault(); // Prevent the default form submission

    // Get form data
    const formData = new FormData(document.getElementById('addDepartmentHeadForm'));

    // Send AJAX request
    fetch('fetch_teachers.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.text())
    .then(data => {
        // Check for a success message in the response
        if (data.includes('Success!')) {
            // Success message
            Swal.fire({
                title: "Success!",
                text: "Department head updated successfully.",
                icon: "success",
                confirmButtonText: "OK"
            }).then(() => {
                window.location.href = "departments.php"; // Redirect after confirmation
            });
        } else {
            // Error or warning message
            Swal.fire({
                title: "Error!",
                text: data, // Display error message from PHP
                icon: "error",
                confirmButtonText: "OK"
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: "Error!",
            text: "An unexpected error occurred.",
            icon: "error",
            confirmButtonText: "OK"
        });
    });
}
</script>


        <script>
    document.addEventListener('DOMContentLoaded', function () {
        loadDepartments();

        // Create Department
        document.getElementById('createDepartmentForm').addEventListener('submit', function (event) {
            event.preventDefault();
            createDepartment();
        });

        // Update Department
        document.getElementById('updateDepartmentForm').addEventListener('submit', function (event) {
            event.preventDefault();
            updateDepartment();
        });

        // Modal functionality
        var createModal = document.getElementById("createModal");
        var updateModal = document.getElementById("updateModal");
        var addDeptHeadModal = document.getElementById("addDeptHeadModal");
        var createBtn = document.getElementById("openModalBtn");
        var createClose = document.getElementById("createModalClose");
        var updateClose = document.getElementById("updateModalClose");
        var addDeptHeadClose = document.getElementById("addDeptHeadModalClose");

        createBtn.onclick = function () {
            createModal.style.display = "flex";
        }

        createClose.onclick = function () {
            createModal.style.display = "none";
        }

        updateClose.onclick = function () {
            updateModal.style.display = "none";
        }

        addDeptHeadClose.onclick = function () {
            addDeptHeadModal.style.display = "none"; // Hide the modal
        }

        window.onclick = function (event) {
            if (event.target == createModal) {
                createModal.style.display = "none";
            }
            if (event.target == updateModal) {
                updateModal.style.display = "none";
            }
            if (event.target == addDeptHeadModal) {
                addDeptHeadModal.style.display = "none"; // Hide the modal on outside click
            }
        }
    });

    function loadDepartments() {
    fetch('fetch_dept.php?action=read')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('departmentsContainer');
            container.innerHTML = ''; // Clear existing content
            if (data.department && data.department.length) {
                // Add the Bootstrap row class to the container for grid layout
                const row = document.createElement('div');
                row.classList.add('row');
                container.appendChild(row);

                data.department.forEach(department => {
                    const colDiv = document.createElement('div');
                    // Set the column sizes for large, medium, and small screens
                    colDiv.classList.add('col-lg-4', 'col-md-6', 'col-12', 'mb-4'); 
                    colDiv.classList.add('department');
                    
                    let deptHeadInfo = '';
                    if (department.dept_head && department.dept_head.profile_image && department.dept_head.full_name) {
                        // Set profile image path and full name
                        const profileImage = `../img/UserProfile/${department.dept_head.profile_image}`;
                        deptHeadInfo = `
                            <div class="dept-head-info d-flex align-items-center">
                                <img src="${profileImage}" alt="Profile Image" class="profile-image1 me-2">
                                <span class="depth-name">${department.dept_head.full_name}</span>
                            </div>
                        `;
                    } else {
                        // Show message if no department head assigned
                        deptHeadInfo = `<p class="no-dept-head">NO DEPT HEAD ASSIGNED</p>`;
                    }

                    colDiv.innerHTML = `
                        <h3>${department.dept_name}</h3>
                        ${deptHeadInfo}
                        <p>${department.dept_info}</p>
                        <div class="dropdown">
                            <button class="dropdown-toggle"></button>
                            <div class="dropdown-menu">
                                <button class="btn-icon" onclick="showUpdateModal(${department.dept_ID}, '${department.dept_name}', '${department.dept_info}')">
                                    <i class='bx bx-edit-alt'></i> Edit
                                </button>
                                <button class="btn-icon btn-small" onclick="deleteDepartment(${department.dept_ID})">
                                    <i class='bx bx-trash'></i> Delete
                                </button>
                                <button class="btn-icon btn-small" onclick="addDeptHeadModal(${department.dept_ID})">
                                    <i class='bx bx-face'></i> Dept. Head
                                </button>
                            </div>
                        </div>
                        <a href="grades.php?deptID=${department.dept_ID}&deptName=${department.dept_name}" class="arrow-link">
                            <i class='bx bx-right-arrow-alt'></i> <!-- Arrow icon -->
                        </a>
                    `;
                    row.appendChild(colDiv);
                });
            } else {
                container.innerHTML = '<p>No departments found.</p>';
            }
        });
}


    function addDeptHeadModal(deptID) {
        document.getElementById('deptHeadDeptID').value = deptID; // Set the department ID in the hidden input
        document.getElementById('addDeptHeadModal').style.display = 'flex'; // Show the modal
    }

    function createDepartment() {
        const formData = new FormData(document.getElementById('createDepartmentForm'));
        fetch('department_management.php?action=create', {
            method: 'POST',
            body: JSON.stringify({
                name: formData.get('departmentName'),
                info: formData.get('departmentInfo')
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Hide the modal
                document.getElementById('createModal').style.display = 'none';
                // Reset the form
                document.getElementById('createDepartmentForm').reset();
                // Reload the departments to show the new department
                loadDepartments();

                // Success SweetAlert
                Swal.fire({
                    title: 'Success!',
                    text: 'Department created successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            } else {
                // Error SweetAlert
                Swal.fire({
                    title: 'Error!',
                    text: 'Error creating department: ' + (data.message || 'Unknown error'),
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
            }
        })
        .catch(error => {
            // Network or unexpected error SweetAlert
            Swal.fire({
                title: 'Oops!',
                text: 'Something went wrong: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        });
    }


    function deleteDepartment(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Proceed with deletion if confirmed
                fetch(`delete_department.php?action=delete&id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            loadDepartments();
                            Swal.fire(
                                'Deleted!',
                                'Your department has been deleted.',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                'Error deleting department',
                                'error'
                            );
                        }
                    });
            }
        });
    }

    function showUpdateModal(id, name, info) {
        document.getElementById('updateDeptID').value = id;
        document.getElementById('updateDepartmentName').value = name;
        document.getElementById('updateDepartmentInfo').value = info;
        document.getElementById('updateModal').style.display = 'flex';
    }

    function updateDepartment() {
        const formData = new FormData(document.getElementById('updateDepartmentForm'));
        fetch('update_department.php?action=update', {
            method: 'POST',
            body: JSON.stringify({
                id: formData.get('deptID'),
                name: formData.get('departmentName'),
                info: formData.get('departmentInfo')
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                loadDepartments();
                document.getElementById('updateDepartmentForm').reset();
                document.getElementById('updateModal').style.display = 'none';

                // Success SweetAlert
                Swal.fire({
                    title: 'Success!',
                    text: 'Department updated successfully.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            } else {
                // Error SweetAlert
                Swal.fire({
                    title: 'Error!',
                    text: 'Error updating department: ' + (data.message || 'Unknown error'),
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
            }
        })
        .catch(error => {
            // Network or unexpected error SweetAlert
            Swal.fire({
                title: 'Oops!',
                text: 'Something went wrong: ' + error.message,
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        });
    }

</script>


<script>


 document.addEventListener('click', function (event) {
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        // Check if the clicked element is the dropdown toggle
        if (event.target === toggle) {
            dropdown.classList.toggle('active'); // Toggle the active class to show/hide the menu
        } else {
            dropdown.classList.remove('active'); // Close the dropdown if clicking outside
        }
    });
});
</script>


    <script src="assets/js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>