<?php

session_start();

include 'connection.php';

// Fetch chairperson data
$sql = "
    SELECT 
        Chairperson_ID,
        CONCAT(useracc.fname, ' ', useracc.mname, ' ', useracc.lname) AS FullName,
        grade.Grade_Level
    FROM 
        chairperson
    INNER JOIN 
        useracc ON chairperson.UserID = useracc.UserID
    INNER JOIN 
        grade ON chairperson.Grade_ID = grade.Grade_ID
";
$result = $conn->query($sql);

// Initialize an array to hold data
$chairpersonData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $chairpersonData[] = $row;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty</title>
        <link rel="icon" type="image/png" href="../img/Logo/docmap-logo-1.png">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* CSS for the faculty members list */
        

        .faculty-list-container {
            margin: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .faculty-list-title {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .faculty-actions {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .action-buttons {
            display: flex;
            gap: 10px; /* Space between buttons */
        }

        .faculty-actions button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            
        }

        .btn-update {
            background-color:blue;
            color: white;   
        }

        .btn-resign {
            background-color: red;
            color: white;
        }

        .search-input {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            width: 40%;
            transition: border-color 0.3s ease;

        }

        .search-input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        .faculty-table {
            width: 100%;
            border-collapse: collapse;
        }

        .faculty-table th, .faculty-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .faculty-table th {
            background-color: #9b2035;
            color: white;
        }

        .faculty-table tr:hover {
            background-color: #f1f1f1;
        }

        .faculty-table input[type="checkbox"] {
            cursor: pointer;
        }

        /* Styling the form container for each user */
        .user-form-container {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .user-form-container label {
            display: block;
            width: 150px;  /* Adjust the width as needed */
            text-align: left;  /* Ensures the label text is aligned to the left */
        }

        .user-form-container .swal2-input {
            flex: 1;
            padding: 8px;
            margin-top: 0px;
            margin-bottom: 12px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .swal2-input:focus {
            border-color: #3498db;
            outline: none;
        }

        .user-form-container select {
            width: calc(100% - 20px);
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fff;
            margin-bottom: 12px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .user-form-container select:focus {
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }



        /* Make the form responsive */
        @media (max-width: 768px) {
            .user-form-container {
                margin: 10px;
                padding: 12px;
            }

            .user-form-container .swal2-input {
                padding: 6px;
            }
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .pagination a {
            padding: 10px 15px;
            margin: 0 5px;
            text-decoration: none;
            background-color: transparent; /* Make the background of the page numbers transparent */
            color:grey;
        
            border-radius: 50%; /* Make buttons circular */
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .pagination a.active {
            background-color: transparent;
            color: #9b2035;
            font-weight:bold;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination a.prev-button, .pagination a.next-button {
            background-color: #a53649; /* Lighter shade for Previous and Next */
            border-color: transparent; 
            color: white;/* Lighter border color */
        }

        .pagination a.prev-button:hover, .pagination a.next-button:hover {
            background-color: #a43150; /* Slightly darker shade when hovered */
        }

        .pagination a.first-button, .pagination a.last-button {
            background-color: #9b2035; /* Dark background color for First and Last */
            color: white;
        }

        .pagination a.first-button:hover, .pagination a.last-button:hover {
            background-color: #a43150; /* Lighter shade on hover for First and Last */
        }

        /*---------- Chairperson Container ----------*/
        .chairperson-container {
            margin: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        /* Button container (flex row) */
        .button-container {
            display: flex;
            justify-content: flex-end; /* Aligns the buttons to the right */
            gap: 10px; /* Space between buttons */
            margin-bottom: 20px;
            width: 100%; /* Make sure buttons take up the full width */
        }

        /* Style for the Assign Chairperson Button */
        #assignChairpersonBtn {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        #assignChairpersonBtn:hover {
            background-color: #2980b9;
        }

        /* Style for the Delete Chairperson Button */
        #deleteChairpersonBtn {
            padding: 10px 20px;
            background-color: #f44336; /* Red */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        #deleteChairpersonBtn:hover {
            background-color: #d32f2f;
        }

        /*---------- Chairperson Table ----------*/
        .chairperson-table, .faculty-table {
            width: 100%;
            border-collapse: collapse;
        }

        .chairperson-table th, .chairperson-table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .chairperson-table th {
            background-color: #9b2035;
            color: white;
        }

        .chairperson-table tr:hover, .faculty-table tr:hover {
            background-color: #f1f1f1;
        }

        .chairperson-table input[type="checkbox"] {
            cursor: pointer;
        }

        /*---------- Assign Chairperson Modal ----------*/
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            padding: 10px;
            box-sizing: border-box;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 600px;  /* Increased width for a larger modal */
            max-width: 90%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
        }

        .modal-content h2 {
            margin-top: 0;
            font-size: 1.5rem;
            text-align: center;
        }

        .modal-content form label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            font-size: 1rem;
        }

        .modal-content form select,
        .modal-content form button {
            width: 100%;
            margin-top: 5px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        .btn-assign {
            margin-top: 15px;
            margin-bottom: 15px;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-assign:hover {
            background-color: #2980b9;
        }

        .btn-submit {
            background-color: #27ae60;
            color: white;
            border: none;
            margin-top: 20px;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background-color: #229954;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .modal-content {
                width: 90%;
                padding: 15px;
            }

            .modal-content h2 {
                font-size: 1.2rem;
            }

            .modal-content form label,
            .modal-content form select,
            .modal-content form button {
                font-size: 0.9rem;
            }

            .btn-assign {
                font-size: 0.9rem;
                padding: 8px 16px;
            }

            .btn-submit {
                font-size: 0.9rem;
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .modal-content {
                padding: 10px;
            }

            .modal-content h2 {
                font-size: 1rem;
            }

            .modal-content form label,
            .modal-content form select,
            .modal-content form button {
                font-size: 0.8rem;
            }

            .btn-assign {
                font-size: 0.8rem;
                padding: 6px 12px;
            }

            .btn-submit {
                font-size: 0.8rem;
                padding: 8px;
            }
        }

        /*---------- User Table inside Modal ----------*/
        .user-table-container {
            margin-top: 20px;
            max-height: 300px;  /* Adjusted max-height for better fit */
            overflow-y: auto;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .user-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .user-info-table th, 
        .user-info-table td {
            text-align: left;
            padding: 10px;  /* Increased padding for better readability */
            border: 1px solid #ddd;
        }

        .user-info-table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        .user-info-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .user-info-table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .user-info-table input[type="radio"] {
            margin-left: auto;
            margin-right: auto;
        }

        .info-message {
            display: flex;
            align-items: center;
            margin-top: 5px; /* Space between title and message */
            
        }
        
        .info-message p {
            font-size: 16px; /* Font size for the message */
            color: #555; /* Color for the message text */
            margin: 0; /* Remove default margin */
        }


    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <?php include 'navbar.php'; ?>
    </section>
    <!-- SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <?php include 'topbar.php'; ?>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <h1 class="faculty-list-title">Faculty Members</h1>
            <!-- Information Icon and Message -->
                <div class="info-message" style="display: flex; align-items: center; margin-top: 10px;">
                    <i class='bx bx-info-circle'style="font-size: 24px; margin-right: 10px; color:#9B2035; "></i>
                    <p style="font-size: 14px; color: #555; margin: 0;"> <!-- Remove margin for better alignment -->
                        You cannot undo once you removed a user.
                </div>

            <div class="faculty-list-container">
                
                <!-- Action Buttons -->
                <div class="faculty-actions">
                    <div class="action-buttons">
                        <button id="updateSelected" class="btn-update">
                            <i class="fas fa-edit"></i> Update
                        </button>
                        <button id="resignSelected" class="btn-resign">
                            <i class="fas fa-trash-alt"></i> Resign
                        </button>
                    </div>
                    <input
                        type="text"
                        id="searchInput"
                        class="search-input"
                        placeholder="Search Faculty..."
                    />
                </div>

                <!-- Faculty Table -->
                <table class="faculty-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Full Name</th>
                            <th>Position</th>
                            <th>Address</th>
                            <th>Mobile</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
                <!-- Pagination controls -->
                <div class="pagination-container">
                    <div class="pagination">
                        <!-- Pagination buttons will be added here -->
                    </div>
                </div>
            </div>


            <!------------------------------------ Chairperson Container ------------------------------->
            <div class="chairperson-container">
                <h2>Chairpersons</h2>
                
                <!-- Buttons for Deletion and Assign -->
                <div class="button-container">
                    <button id="assignChairpersonBtn" class="btn-update">Assign Chairperson</button>
                    <button id="deleteChairpersonBtn" class="btn-delete">Delete Chairperson</button>
                </div>
                
                <table class="chairperson-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll-chairperson"></th>
                            <th>Full Name</th>
                            <th>Grade Level</th>
                        </tr>   
                    </thead>
                    <tbody>
                        <?php if (!empty($chairpersonData)) : ?>
                            <?php foreach ($chairpersonData as $chairperson) : ?>
                                <tr>
                                    <!-- Checkbox for selecting chairpersons -->
                                    <td><input type="checkbox" name="chairpersonID[]" value="<?= $chairperson['Chairperson_ID']; ?>"></td>
                                    <td><?= htmlspecialchars($chairperson['FullName']); ?></td>
                                    <td><?= htmlspecialchars($chairperson['Grade_Level']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3" style="text-align: center;">No chairperson data available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Modal for Assigning Chairperson -->
            <div id="assignChairpersonModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" id="closeModal">&times;</span>
                    <h2>Assign Chairperson</h2>

                    <form id="assignChairpersonForm" method="POST" action="assign_chairperson.php">
                        <div class="user-table-container">
                            <table class="user-info-table">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Full Name</th>
                                        <th>Rank</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Database connection
                                    include 'connection.php';

                                    // Fetch users eligible to be chairpersons
                                    $usersQuery = "SELECT UserID, CONCAT(fname, ' ', mname, ' ', lname) AS FullName, Rank 
                                                FROM useracc 
                                                WHERE role IN ('Department Head', 'Teacher')";
                                    $usersResult = $conn->query($usersQuery);

                                    if ($usersResult->num_rows > 0) {
                                        while ($user = $usersResult->fetch_assoc()) {
                                            echo "
                                            <tr>
                                                <td><input type='radio' name='userID' value='{$user['UserID']}' required></td>
                                                <td>" . htmlspecialchars($user['FullName']) . "</td>
                                                <td>" . htmlspecialchars($user['Rank'] ?? 'N/A') . "</td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "
                                        <tr>
                                            <td colspan='3'>No eligible users found.</td>
                                        </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <label for="gradeID">Grade Level:</label>
                        <select name="gradeID" id="gradeID" required>
                            <option>Select Grade Level</option>
                            <?php
                            // Fetch grade levels
                            $gradesQuery = "SELECT Grade_ID, Grade_Level FROM Grade";
                            $gradesResult = $conn->query($gradesQuery);

                            if ($gradesResult->num_rows > 0) {
                                while ($grade = $gradesResult->fetch_assoc()) {
                                    echo "<option value='{$grade['Grade_ID']}'>" . htmlspecialchars($grade['Grade_Level']) . "</option>";
                                }
                            }

                            $conn->close();
                            ?>
                        </select>

                        <button type="submit" class="btn-submit">Assign</button>
                    </form>
                </div>
            </div>




        </main>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="assets/js/script.js"></script>

    <script>
            // Handle Select All checkbox
            document.getElementById('selectAll').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.select-user');
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            });

            // Handle Update Selected Button Click
            document.getElementById('updateSelected').addEventListener('click', function() {
                const selectedUsers = getSelectedUsers();
                if (selectedUsers.length > 0) {
                    Swal.fire({
                        title: 'Update Selected',
                        text: `You are about to update ${selectedUsers.length} selected users.`,
                        icon: 'info',
                        confirmButtonText: 'Proceed',
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formsHtml = selectedUsers.map(user => {
                                const [firstName, middleName, lastName] = user.fullname.split(' ');
                                return `
                                    <div class="user-form-container">
                                        <label>First Name:</label>
                                        <input type="text" class="swal2-input user-firstname" value="${firstName || ''}">
                                        <label>Middle Name:</label>
                                        <input type="text" class="swal2-input user-middlename" value="${middleName || ''}">
                                        <label>Last Name:</label>
                                        <input type="text" class="swal2-input user-lastname" value="${lastName || ''}">
                                        <label>Rank:</label>
                                        <input type="text" class="swal2-input user-rank" value="${user.rank || ''}">
                                        <label>Address:</label>
                                        <input type="text" class="swal2-input user-address" value="${user.address || ''}">
                                        <label>Mobile:</label>
                                        <input type="text" class="swal2-input user-mobile" value="${user.mobile || ''}">
                                        <label>Email:</label>
                                        <input type="email" class="swal2-input user-email" value="${user.email || ''}">
                                    </div>
                                `;
                            }).join('');

                            Swal.fire({
                                title: 'Update User\'s Information',
                                html: `<form id="updateForm">${formsHtml}</form>`,
                                focusConfirm: false,
                                showCancelButton: true,
                                preConfirm: () => {
                                    const updatedUsers = selectedUsers.map((user, index) => ({
                                        UserID: user.UserID,
                                        firstName: document.querySelectorAll('.user-firstname')[index].value,
                                        middleName: document.querySelectorAll('.user-middlename')[index].value,
                                        lastName: document.querySelectorAll('.user-lastname')[index].value,
                                        rank: document.querySelectorAll('.user-rank')[index].value,
                                        address: document.querySelectorAll('.user-address')[index].value,
                                        mobile: document.querySelectorAll('.user-mobile')[index].value,
                                        email: document.querySelectorAll('.user-email')[index].value,
                                    }));
                                    return updatedUsers;
                                }
                            }).then((updateResult) => {
                                if (updateResult.isConfirmed) {
                                    updateUsersOnServer(updateResult.value);
                                }
                            });
                        }
                    });
                } else {
                    Swal.fire('No Selection', 'Please select users to update.', 'warning');
                }
            });

            // Function to get selected user data
            function getSelectedUsers() {
                const selectedCheckboxes = document.querySelectorAll('.select-user:checked');
                return Array.from(selectedCheckboxes).map(checkbox => ({
                    UserID: checkbox.value,
                    fullname: checkbox.getAttribute('data-fullname'),
                    rank: checkbox.getAttribute('data-rank'),
                    address: checkbox.getAttribute('data-address'),
                    mobile: checkbox.getAttribute('data-mobile'),
                    email: checkbox.getAttribute('data-email'),
                }));
            }
            // Function to send updated users to the server
            function updateUsersOnServer(updatedUsers) {
                // Filter out unchanged users
                const changedUsers = updatedUsers.filter(user => {
                    const original = document.querySelector(`.select-user[value="${user.UserID}"]`);
                    return (
                        user.firstName !== original.getAttribute('data-fullname').split(' ')[0] ||
                        user.middleName !== (original.getAttribute('data-fullname').split(' ')[1] || '') ||
                        user.lastName !== (original.getAttribute('data-fullname').split(' ')[2] || '') ||
                        user.rank !== original.getAttribute('data-rank') ||
                        user.address !== original.getAttribute('data-address') ||
                        user.mobile !== original.getAttribute('data-mobile') ||
                        user.email !== original.getAttribute('data-email')
                    );
                });

                if (changedUsers.length === 0) {
                    Swal.fire('No Changes', 'No data was changed. Update not required.', 'info');
                    return;
                }

                // Send changes to the server
                fetch('update_users.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ users: changedUsers })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Success alert
                            Swal.fire({
                                title: 'Success!',
                                text: 'Users have been successfully updated.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => location.reload()); // Optionally reload to reflect changes
                        } else {
                            // Error alert with a specific message from the server
                            let errorMessage = data.error || 'There was an error updating the users. Please try again.';
                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        // Unexpected error alert
                        console.error('Error updating users:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An unexpected error occurred. Please try again later.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
            }

            // Function to load faculty members and update the table
            function loadFacultyMembers(page = 1) {
                fetch(`fetch_users.php?page=${page}`)
                    .then(response => response.json())
                    .then(data => {
                        const { users, total_pages, current_page } = data;
            
                        // Update the table with the fetched users
                        const tableBody = document.querySelector('.faculty-table tbody');
                        tableBody.innerHTML = '';
            
                        users.forEach(user => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td><input type="checkbox" class="select-user" 
                                        data-fullname="${user.fullname}" 
                                        data-rank="${user.Rank}" 
                                        data-address="${user.address}" 
                                        data-mobile="${user.mobile}" 
                                        data-email="${user.email}" 
                                        value="${user.UserID}"></td>
                                <td>${user.fullname}</td>
                                <td>${user.Rank}</td>
                                <td>${user.address}</td>
                                <td>${user.mobile}</td>
                                <td>${user.email}</td>
                            `;
                            tableBody.appendChild(row);
                        });
            
                        // Update pagination controls
                        updatePagination(total_pages, current_page);
                    })
                    .catch(error => console.error('Error loading faculty members:', error));
            }

            // Function to update pagination controls
            function updatePagination(total_pages, current_page) {
                const paginationContainer = document.querySelector('.pagination');
                paginationContainer.innerHTML = '';
            
                // First button
                if (current_page > 1) {
                    const firstButton = document.createElement('a');
                    firstButton.href = `?page=1`;
                    firstButton.classList.add('first-button');
                    firstButton.title = 'back to first';
                    firstButton.innerHTML = '<i class="bx bx-chevrons-left"></i>';
                    paginationContainer.appendChild(firstButton);
                }
            
                // Previous button
                if (current_page > 1) {
                    const prevButton = document.createElement('a');
                    prevButton.href = `?page=${current_page - 1}`;
                    prevButton.classList.add('prev-button');
                    prevButton.innerHTML = '<i class="bx bx-chevron-left"></i>';
                    paginationContainer.appendChild(prevButton);
                }
            
                // Page numbers
                const start_page = Math.max(1, current_page - 2); // Ensure we don't go below 1
                const end_page = Math.min(total_pages, current_page + 2); // Ensure we don't go above the last page
            
                for (let i = start_page; i <= end_page; i++) {
                    const pageButton = document.createElement('a');
                    pageButton.href = `?page=${i}`;
                    pageButton.classList.add(i === current_page ? 'active' : '');
                    pageButton.innerText = i;
                    paginationContainer.appendChild(pageButton);
                }
            
                // Next button
                if (current_page < total_pages) {
                    const nextButton = document.createElement('a');
                    nextButton.href = `?page=${current_page + 1}`;
                    nextButton.classList.add('next-button');
                    nextButton.innerHTML = '<i class="bx bx-chevron-right"></i>';
                    paginationContainer.appendChild(nextButton);
                }
            
                // Last button
                if (current_page < total_pages) {
                    const lastButton = document.createElement('a');
                    lastButton.href = `?page=${total_pages}`;
                    lastButton.classList.add('last-button');
                    lastButton.innerHTML = '<i class="bx bx-chevrons-right"></i>';
                    paginationContainer.appendChild(lastButton);
                }
            }

            // Initial load of faculty members for the first page
            loadFacultyMembers(1);

            

            
            // Handle Delete Selected Button Click
            document.getElementById('resignSelected').addEventListener('click', function () {
                const selectedUsers = getSelectedUsers();
                if (selectedUsers.length > 0) {
                    Swal.fire({
                        title: 'Resign Selected',
                        text: `Are you sure you want to delete ${selectedUsers.length} selected users? This action cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete',
                        cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteUsersFromServer(selectedUsers);
                        }
                    });
                } else {
                    Swal.fire('No Selection', 'Please select users to delete.', 'warning');
                }
            });

            // Function to delete selected users from the server
            function deleteUsersFromServer(selectedUsers) {
                const userIDs = selectedUsers.map(user => user.UserID);

                fetch('delete_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ userIDs })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Selected users have been successfully deleted.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => location.reload()); // Reload to reflect changes
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'There was an error deleting the users. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error deleting users:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An unexpected error occurred. Please try again later.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            }


            // Filter faculty members by search input
            document.getElementById('searchInput').addEventListener('input', function () {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('.faculty-table tbody tr');

                rows.forEach(row => {
                    const fullName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const position = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    const address = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                    const mobile = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                    const email = row.querySelector('td:nth-child(6)').textContent.toLowerCase();

                    // Check if the row matches the search term in any column
                    if (
                        fullName.includes(filter) ||
                        position.includes(filter) ||
                        address.includes(filter) ||
                        mobile.includes(filter) ||
                        email.includes(filter)
                    ) {
                        row.style.display = ''; // Show row
                    } else {
                        row.style.display = 'none'; // Hide row
                    }
                });
            });
        </script>

        
        <script>
            document.getElementById('assignChairpersonBtn').addEventListener('click', function() {
                document.getElementById('assignChairpersonModal').style.display = 'flex';
            });

            document.getElementById('closeModal').addEventListener('click', function() {
                document.getElementById('assignChairpersonModal').style.display = 'none';
            });


            $(document).ready(function() {
                $('#assignChairpersonForm').submit(function(event) {
                    event.preventDefault();  // Prevent default form submission

                    var gradeID = $('#gradeID').val();  // Get the selected grade level

                    // Check if a grade level is selected
                    if (!gradeID || gradeID === 'Select Grade Level') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Grade Level Required',
                            text: 'Please select a grade level before assigning the chairperson.',
                            confirmButtonText: 'OK'
                        });
                        return;  // Prevent form submission if no grade level is selected
                    }

                    var formData = $(this).serialize();  // Get form data

                    $.ajax({
                        type: 'POST',
                        url: 'assign_chairperson.php',  // PHP file that handles form submission
                        data: formData,
                        dataType: 'json',  // Expecting JSON response
                        success: function(response) {
                            // Check if the response status is 'success' or 'error'
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Chairperson Assigned!',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                }).then(() => location.reload()); // Reload to reflect changes
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while assigning the chairperson. Please try again.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });
            });


            $(document).ready(function() {
                // Delete Chairpersons
                $('#deleteChairpersonBtn').click(function(e) {
                    e.preventDefault();  // Prevent the default button behavior
                    
                    var selectedChairpersons = $('input[name="chairpersonID[]"]:checked').map(function() {
                        return this.value;
                    }).get();

                    if (selectedChairpersons.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Chairperson Selected',
                            text: 'Please select at least one chairperson to delete.',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    $.ajax({
                        type: 'POST',
                        url: 'delete_chairperson.php',  // PHP file for deletion
                        data: { chairpersonID: selectedChairpersons },  // Correct data format
                        dataType: 'json',  // Specify that the response should be JSON
                        success: function(response) {
                            // Check the response status to display appropriate message
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Chairperson Deleted',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                }).then(() => location.reload());
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Deletion Failed',
                                    text: response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleting the chairperson. Please try again.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                });
            });




            // JavaScript to select/deselect all checkboxes
            document.getElementById("selectAll-chairperson").addEventListener("click", function() {
                const checkboxes = document.querySelectorAll("input[name='chairpersonID[]']");
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = document.getElementById("selectAll-chairperson").checked;
                });
            });



        </script>
     <script src="assets/js/script.js"></script>

        
</body>
</html>

