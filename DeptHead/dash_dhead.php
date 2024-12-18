<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$loginSuccess = isset($_SESSION['login_success']) ? $_SESSION['login_success'] : false;


include 'connection.php';
$dept_ID = $_SESSION['dept_ID']; // Get dept_ID from session
$user_id = $_SESSION['user_id'];
// Ensure dept_ID is available in the session
if (!isset($_SESSION['dept_ID'])) {
    echo "Department ID not found in session.";
    exit;
}


// Fetch user information
$sql_user = "SELECT dept_ID, fname FROM useracc WHERE UserID = ?";
if ($stmt_user = $conn->prepare($sql_user)) {
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    $fname = '';
    $dept_id = null; // Initialize dept_ID
    if ($result_user->num_rows > 0) {
        $row = $result_user->fetch_assoc();
        $fname = $row['fname'];
        $dept_id = $row['dept_ID']; // Get dept_ID
    }
    $stmt_user->close();
} else {
    echo "Error preparing user query";
}


// Query to count distinct users (teachers) per feedcontent
$totalUsersQuery = "
    SELECT COUNT(DISTINCT ua.UserID) AS user_count
    FROM feedcontent fc
    LEFT JOIN usercontent uc ON fc.ContentID = uc.ContentID
    LEFT JOIN useracc ua ON uc.UserID = ua.UserID
    WHERE ua.role = 'Teacher' 
      AND ua.Status = 'Approved'
      AND fc.dept_ID = ?
";

// Prepare the statement
if ($stmt_totalUsers = $conn->prepare($totalUsersQuery)) {
    // Bind the dept_ID parameter
    $stmt_totalUsers->bind_param('i', $dept_ID); // Use 'i' for integer
    
    // Execute the query
    $stmt_totalUsers->execute();
    
    // Get the result
    $totalUsersResult = $stmt_totalUsers->get_result();
    
    // Fetch the result and get the user count
    $totalUsers = $totalUsersResult->fetch_assoc()['user_count'];
    
    // Close the prepared statement
    $stmt_totalUsers->close();
    
    // Now you can use the $totalUsers variable for further processing
} else {
    // Handle error if preparing the statement fails
    echo "Error preparing total users query: " . $conn->error;
}



$feedContentQuery = "SELECT COUNT(*) as total FROM feedcontent WHERE dept_ID = ?";
if ($stmt_feedContent = $conn->prepare($feedContentQuery)) {
    $stmt_feedContent->bind_param("i", $dept_id); // Use $dept_id here
    $stmt_feedContent->execute();
    $feedContentResult = $stmt_feedContent->get_result();
    $feedContentCount = mysqli_fetch_assoc($feedContentResult)['total'];
    $stmt_feedContent->close();
} else {
    echo "Error preparing feed content query";
}


// Fetch recent documents
function formatDate($timestamp) {
    $date = new DateTime($timestamp);
    $now = new DateTime();
    $yesterday = (new DateTime())->modify('-1 day');

    if ($date->format('Y-m-d') === $now->format('Y-m-d')) {
        return 'Today ' . $date->format('g:i A'); // 12-hour format with AM/PM
    } elseif ($date->format('Y-m-d') === $yesterday->format('Y-m-d')) {
        return 'Yesterday ' . $date->format('g:i A'); // 12-hour format with AM/PM
    } else {
        return $date->format('F j, Y g:i A'); // 12-hour format with AM/PM
    }
}

// Fetch recent documents
$recentDocumentsQuery = "SELECT * FROM documents ORDER BY timestamp DESC LIMIT 5";
$recentDocumentsResult = mysqli_query($conn, $recentDocumentsQuery);
$recentDocuments = [];
while ($row = mysqli_fetch_assoc($recentDocumentsResult)) {
    // Remove the file extension from the name
    $fileNameWithoutExtension = pathinfo($row['name'], PATHINFO_FILENAME);
    $row['name'] = $fileNameWithoutExtension;
    $row['formatted_timestamp'] = formatDate($row['TimeStamp']);

    // Determine the icon based on document type
    switch ($row['mimeType']) {
        case 'application/pdf':
            $row['icon'] = 'bx bx-file-pdf'; // Icon for PDF files
            break;
        case 'image/jpeg':
        case 'image/jpg':
            $row['icon'] = 'bx bx-image'; // Icon for JPG images
            break;
        case 'image/png':
            $row['icon'] = 'bx bx-image-alt'; // Icon for PNG images
            break;
        case 'application/msword':
        case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            $row['icon'] = 'bx bx-file-doc'; // Icon for DOCX files
            break;
        case 'application/vnd.ms-excel':
        case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            $row['icon'] = 'bx bx-file-excel'; // Icon for XLSX files
            break;
        case 'text/plain':
            $row['icon'] = 'bx bx-file'; // Icon for TXT files
            break;
        default:
            $row['icon'] = 'bx bx-file'; // Default icon for other file types
            break;
    }

    $recentDocuments[] = $row;
}
// Ensure the database connection is established
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// SQL Query to fetch the most recent announcement
$query = "
    SELECT Title, taskContent, DueDate, DueTime
    FROM tasks
    WHERE Type = 'Announcement'
    ORDER BY DueDate DESC, DueTime DESC
    LIMIT 1";  // Fetch only the most recent announcement

// Execute the query
$result = $conn->query($query);

// Check if the query execution was successful
if (!$result) {
    echo "Error executing query: " . $conn->error;  // Display MySQL error if query fails
} else {
    // Check if any rows are returned
    if ($result->num_rows > 0) {
        // Fetch the announcement details
        $announcement = $result->fetch_assoc();
        
        $title = $announcement['Title'];
        $taskContent = $announcement['taskContent'];
        $dueDate = $announcement['DueDate'];
        $dueTime = $announcement['DueTime'];

        // Get the current date and time
        $currentDateTime = new DateTime();

        // Create a DateTime object for the DueDate and DueTime from the database
        $dueDateTime = new DateTime($dueDate . ' ' . $dueTime);

        // Compare if the current date and time is after the DueDate and DueTime
        if ($currentDateTime > $dueDateTime) {
            // If the current date and time is after the DueDate and DueTime, remove the announcement content
            $title = $taskContent = '';  // Clear the announcement content
        }
    } else {
        echo "No announcement available.";  // No announcements found
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="icon" type="image/png" href="../img/Logo/docmap-logo-1.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <title>Dashboard | Home</title>
    <style>
        
        .title{
            padding-bottom:30px;
        }
        .info-data {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;

            margin-bottom: -10px; /* Reduce the margin-bottom */
        }

        .card {
            background-color: #9B2035;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            flex: 1;
            min-width: 200px; /* Minimum width for each card */
            box-sizing: border-box;
            height: 290%; /* Adjust height if needed */
            margin-top: -50px;
            max-height: auto; /* Adjust as necessary */
        }

        .card .head {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card .head h2, .card .head p {
            margin: 0;
        }
        

        .card .head i.icon {
            font-size: 40px;
            color: #9B2035;
        }

        .recent-documents-card h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 25px;
        }

        .recent-documents-container {
            max-height: 400px; /* Set a maximum height for the scrollable area */
            overflow-y: auto; /* Enable vertical scrolling */
            overflow-x: hidden; /* Hide horizontal scrolling */
            background-color:#9b2035;
        }

        .recent-document {
            padding: 20px; /* Increased padding for more space */
            margin-bottom: 10px; /* Increased margin for more space between items */
            
            border: 1px solid #ddd; /* Add border here */
            border-radius: 5px; /* Match border-radius for consistency */
            box-sizing: border-box; /* Include padding and border in element's total width and height */
            display: flex; /* Align items horizontally */
            align-items: center; /* Center items vertically */
            background-color: #f9f9f9; /* Optional: Add a background color */
            overflow: hidden; /* Hide any overflowed content */
        }

        .recent-document .icon {
            font-size: 30px; /* Adjust icon size */
            margin-right: 15px; /* Space between icon and text */
            color: #9B2035; /* Icon color */
        }

        .recent-document h3 {
            margin: 0;
            color: #333;
            white-space: nowrap; /* Prevent text from wrapping to a new line */
            overflow: hidden; /* Hide overflowed text */
            text-overflow: ellipsis; /* Add ellipsis (...) for overflowed text */
            max-width: 300px; /* Optional: Set a maximum width for the text */
            font-size: 20px;
        }

        .recent-document p {
            margin: 5px 0 0;
            color: #666;
        }
        .new-container {
            position: relative;
            height: 150px;
            border-radius: 10px;
            background: linear-gradient(to left, #9b2035, #d0495e );
            
        }

        .new-container::before {
            
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(to left, #9b2035, #d0495e );
            clip-path: path('M0 0 C50 150, 150 0, 200 100, 300 200, 350 0, 400 200, 500 100, 600 300, 700 200, 800 100, 900 0, 1000 200, 1100 100, 1200 0, 1300 200, 1400 100, 1500 300, 1600 200, 1700 100, 1800 0, 1900 200, 2000 100, 2100 0, 2200 200, 2300 100, 2400 300, 2500 200, 2600 100, 2700 0, 2800 200, 2900 100, 3000 0, 3100 200, 3200 100, 3300 300, 3400 200, 3500 100, 3600 0, 3700 200, 3800 100, 3900 0, 4000 200, 4100 100, 4200 300, 4300 200, 4400 100, 4500 0');
            animation: animate 4s linear infinite;
        }

        @keyframes animate {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-100%);
            }
        }

        .new-container h2 {
            color: #fff;
            margin-bottom: 10px;
            margin-top: -10px;
            
        }

        .new-container p {
            color: #fff;
            font-size: 24px;
        }

        .user-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-welcome {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .chart-container{
            flex-direction: column;
        }
        
        
        #chart {
            width: 300%; /* Adjust width as needed */
            max-width: 500px; /* Set a maximum width for the chart */
            height: 300px; /* Adjust height as needed */
            margin: auto; /* Center align the chart container */
        }

        .content-data {
            flex: 1; /* Allow items to grow and shrink */
            min-width: 592px; /* Set a minimum width for responsiveness */
            background: var(--light);
            border-radius: 10px;
            box-shadow: 4px 4px 16px rgba(0, 0, 0, .1);
            margin-top: 35px;
            margin-left: 10px;
            
        }

        .sales-report-container {
            width: 200%; /* Adjust width to fit the container */
            max-width: 800px; /* Set a maximum width if needed */
            height: 500px; /* Adjust height as needed */
            background: var(--light); /* Use a different background if needed */
            border-radius: 10px;
            box-shadow: 4px 4px 16px rgba(0, 0, 0, .1);
            margin-left: 18px;
        }


        .content-data .head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .content-data .head h2 {
            font-size: 20px;
            font-weight: 600;
        }

    
/* Trend container */
.trend-container {
    display: flex;                   /* Arrange trend lines in a row */
    align-items: flex-end;          /* Align lines to the bottom */
    height: auto;                  /* Fixed height for the trend area */
         /* Optional border around the trend area */
    border-radius: 5px;             /* Rounded corners */
    padding: 5px;                   /* Padding inside the trend area */
}

/* Individual trend lines */
.trend-line {
    width: 20px;                    /* Width of each trend line */
    border-radius: 5px;             /* Rounded corners for lines */
}

/* Optional styles for the trend line colors */
.trend-line.submit {
    background-color: #28a745;      /* Green for submitted tasks */
}

.trend-line.assign {
    background-color: #dc3545;      /* Red for assigned tasks */
}

/* Table styling */
.table {
    width: 100%;                      /* Full width */
    border-collapse: collapse;        /* Remove spacing between cells */
    margin: 20px ;                  /* Space around the table */
}

/* Table header styling */
.table th {
    background-color: #9B2035;            /* Red background for header */
    color: white;                     /* White text color */
    padding: 10px;                   /* Padding inside header cells */
    text-align: center;               /* Center text in header cells */
}

/* Table cell styling */
.table td {
    padding: 10px;                    /* Padding inside data cells */
    text-align: center;               /* Center text in data cells */
    border: 1px solid #ccc;           /* Border around cells */
    background-color:#ffff;
    font-weight:bold;
}

/* Optional: Styling for even/odd rows */
.table tr:nth-child(even) {
    background-color: #f2f2f2;        /* Light grey background for even rows */
}

.table tr:nth-child(odd) {
    background-color: #ffffff;        /* White background for odd rows */
}

/* Optional: Hover effect for table rows */
.table tr:hover {
    background-color: #e0e0e0;        /* Slight grey background on hover */
}
/* Table styling */
.table1 {
    width: 100%;                    /* Full width */
    border-collapse: collapse;      /* Remove spacing between cells */
    margin: 20px 0 20px 0;          /* Vertical margin 20px, horizontal margin 0 */
    text-align: left;               /* Align table text to the left */
}


/* Table header styling */
.table1 th {
    background-color: #9B2035;            /* Red background for header */
    color: white;                     /* White text color */
    padding: 15px;                   /* Padding inside header cells */
    text-align: center;               /* Center text in header cells */
}

/* Table cell styling */
.table1 td {
    padding: 10px;                    /* Padding inside data cells */
    text-align: center;               /* Center text in data cells */
    border: 1px solid #ccc;           /* Border around cells */
    background-color:#ffff;
    font-weight:bold;
}

/* Optional: Styling for even/odd rows */
.table1 tr:nth-child(even) {
    background-color: #f2f2f2;        /* Light grey background for even rows */
}

.table1 tr:nth-child(odd) {
    background-color: #ffffff;        /* White background for odd rows */
}

/* Optional: Hover effect for table rows */
.table1 tr:hover {
    background-color: #e0e0e0;        /* Slight grey background on hover */
}
#tasks-container {
    max-height: 400px; /* Adjust the height as needed */
    overflow-y: auto;  /* Enable vertical scrolling */
}

     /* Add styles for the row and hover effect */
.expand-row {
    position: relative; /* Needed for positioning the hover icon */
    cursor: pointer;
    padding: 10px;
    text-align: center;
    transition: background-color 0.3s ease; /* Smooth hover effect */
}

.expand-row:hover {
    background-color: #f0f0f0; /* Highlight row on hover */
}

.expand-row::after {
    content: '\f105'; /* Font Awesome right arrow icon */
    font-family: "Font Awesome 5 Free"; /* Make sure Font Awesome is included */
    font-weight: 900;
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0; /* Hidden by default */
    transition: opacity 0.3s ease; /* Smooth fade-in effect */
}

.expand-row:hover::after {
   
}   
.progress {
    height: 20px; /* Adjust height as needed */
    background-color: lightgray; /* Default background color */
    border-radius: 5px;
}

.progress {
    height: 20px;
    background-color: lightgray;
    border-radius: 5px;
    margin-bottom: 5px; /* Add some spacing between progress bars */
}

.progress-bar {
    height: 100%;
    border-radius: 5px;
    color: white; /* Text color for better visibility */
}

.assigned {
    background-color: yellow;
}

.missing {
    background-color: red;
}

.submitted {
    background-color: green;
}
.fc-today {
    background-color: rgba(255, 0, 0, 0.1); 
    border: 1px solid red; 
}

.fc-day-grid-event {
    color: black;
}

.fc-button-custom {
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    padding: 5px 10px;
    border-radius: 5px;
    margin: 0 5px;
}

.fc-toolbar .fc-right {
    text-align: right;
}

/* Style the prev/next buttons to be just arrows */
.fc-prev-button, .fc-next-button {
    padding: 0; /* Remove default padding */
}

.fc-prev-button span, .fc-next-button span {
    display: inline-block;
    width: 1em;
    height: 1em;
    border: none; /* Remove default border */
}

.fc-prev-button span {
    transform: rotate(180deg); /* Rotate left arrow */
}

.fc-next-button span {
  /* No rotation needed for right arrow */
}
/* Style for the FullCalendar buttons */
.fc-button {
    background-color: #9B2035; /* Maroon background */
    color: white;              /* White text */
    border: none;              /* Remove borders */
    padding: 10px 15px;        /* Add padding */
    font-size: 14px;
               /* Adjust font size *         /* Make text bold */
    border-radius: 4px;        /* Rounded corners */
    
}

/* Hover effect for the button */
.fc-button:hover {
    background-color: #7a1f2a; /* Darker maroon on hover */
    color: white;               /* Keep text white */
    
}

/* Active state for buttons */
.fc-button:active {
    background-color: #650f1d; /* Even darker maroon when pressed */
    color:white;               /* Keep text white */
}

/* Style for the "Today" button specifically */
.fc-button-today {
    background-color: #9b2035 !important;  /* Maroon */
    color: white !important;               /* White text */
}

/* Container for right-alignment */
#filter-container {
    display: flex;
    justify-content: flex-end;   /* Align to the right */
    width: 100%;                 /* Make sure the container spans the full width */
    padding: 10px;               /* Optional: Add some padding around */
}

/* Style for the year filter dropdown */
#year-filter {
    font-size: 12px;             /* Smaller font size */
    padding: 5px;                /* Adjust padding */
    width: 120px;                /* Set the width to make it more compact */
    height: 30px;                /* Adjust height */
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
        <main style="max-height: 100%;">
    <h1 class="title" style="margin-bottom: 23px;">Dashboard</h1>
    <div class="info-data">
        <div class="row">

            <!-- Main Section -->
            <div class="col-md-8">
                <div class="row">
                    <!-- Welcome Card -->
                    <div class="col-md-12">
                        <div class="card" style="height: 150px; padding: 10px; border-radius: 10px; box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.05); position: relative;">
                            <div style="padding: 20px;">
                                <h1 style="font-weight: bold; color: #9b2035; margin-top: 20px;">
                                    Hello, <?php echo htmlspecialchars($fname); ?>!
                                    <img src="img/EYYY.gif" alt="Animated GIF" style="height: 40px; vertical-align: middle;">
                                </h1>
                            </div>
                            <img src="img/card.png" alt="Welcome Image" style="position: absolute; right: 0; top: -63%; height: 320px; max-width: none;">
                        </div>
                    </div>
                        <!-- Task Progress Table -->
                                            <div class="col-md-12" style="margin-top: 70px;">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="card" style="max-height: 300px; padding: 15px; border-radius: 10px; box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.05); position: relative;">
                                                            <h5 id="department-name" style="font-weight: bold; color: #9b2035;">Department Name</h5>
                                                            <select id="year-filter">                    
                                                            </select>
                                                            <table class="table1 table-responsive">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Name</th>
                                                                        <th>Progress</th>
                                                                        <th>Performance</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="teacher-table-body">
                                                                    <!-- JavaScript will populate this -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                        <div class="card" style="height: 300px; padding: 20px; border-radius: 10px; box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.05);">
                                            <h5 style="font-weight: bold; color: #9b2035;">Recent Submitted Tasks</h5>
                                            <div style="margin-top: 20px;">
                                                <?php
                                                // Include the file to fetch and display recent tasks
                                                include 'fetch_recent_docs.php'; 
                                                ?>
                                            </div>
                                        </div>
                                    </div>



                                                </div>
                                            </div>
                    <!-- Stats Section -->
                    <div class="col-md-12" style="margin-top: 50px;">
                        <div class="row" style="margin-top: 20px; margin-bottom: 25px;">
                            <!-- Cards -->
                            <div class="col-md-4">
                                <div class="card">
                                <div class="head">
                                        <h2><?php echo $totalUsers; ?></h2>
                                        <p style="font-weight: bold;">Total Teachers</p>
                                        <i class='bx bx-group icon'></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="head">
                                        <h2><?php echo $feedContentCount; ?></h2>
                                        <p style="font-weight: bold;">Sections Handled</p>
                                        <i class='bx bx-grid-alt icon'></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                <div class="head" style="font-weight: bold;">
                                <h2 id="overall-performance"></h2><p> Overall Performance</p>
                                        <i class='bx bx-bar-chart' style=" font-size: 40px; color: #9B2035;"></i>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                   
                </div>
            </div>

            <!-- Calendar Section -->
            <div class="col-md-4">
                <div class="card" style="height: 600px; padding: 20px; border-radius: 10px; box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.05); display: flex; flex-direction: column; justify-content: space-between;">
                    <div id="calendar" style="height: 250px; margin-bottom: 20px;"></div>
                    <div id="tasks-container" style="flex-grow: 1; margin-top: 130px;">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr style="font-size: 15px;">
                                    <th>Department</th>
                                    <th>Tasks Progress</th>
                                </tr>
                            </thead>
                            <tbody id="department-table-body">
                                <!-- Data will be inserted via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>


        <!-- MAIN -->
    </section>
    <!-- NAVBAR -->

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
	<script src="assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.41/moment-timezone-with-data.min.js"></script>
    <script>
    // Function to load the latest tasks from the server
    function loadRecentTasks() {
        // Fetch the recent tasks from the server
        fetch('fetch_recent_docs.php')
            .then(response => response.text())
            .then(data => {
                // Replace the content inside the container with the new data
                document.getElementById('recent-tasks-container').innerHTML = data;
            })
            .catch(error => {
                console.error('Error fetching recent tasks:', error);
            });
    }

    // Initial load
    loadRecentTasks();

    // Reload the tasks every 5 seconds (5000 milliseconds)
    setInterval(loadRecentTasks, 5000);
</script>
    <script>
 $(document).ready(function () {
    let teacherData = []; // Store the fetched teacher data for filtering
    let yearFilter = new Date().getFullYear(); // Default to current year

    // Function to render the table
    function renderTable(data) {
        const tbody = $('#teacher-table-body');
        tbody.empty();

        if (data.length === 0) {
            tbody.append('<tr><td colspan="4" class="text-center">No data available for the selected year</td></tr>');
            return;
        }

        let totalOnTimePercentage = 0; // To track the sum of on-time percentages
        const totalTeachers = data.length;

        data.forEach(teacher => {
            const totalTasks = teacher.total;
            const assignedPercentage = (teacher.assigned / totalTasks) * 100 || 0;
            const missingPercentage = (teacher.missing / totalTasks) * 100 || 0;
            const submittedPercentage = (teacher.submitted / totalTasks) * 100 || 0;

            // Colors for progress bars
            const colors = {
                assigned: assignedPercentage > 0 ? 'gray' : 'lightgray',
                missing: missingPercentage > 0 ? 'red' : 'lightgray',
                submitted: submittedPercentage > 0 ? 'green' : 'lightgray',
            };

            // Calculate the "on-time submission" performance
            const onTimePercentage = Math.max(0, submittedPercentage - (assignedPercentage + missingPercentage));

            // Add the on-time percentage to the total
            totalOnTimePercentage += onTimePercentage;

            const row = `
                <tr>
                    <td>${teacher.name}</td>
                    <td>
                        <div class="progress" 
                             title="Submitted: ${submittedPercentage.toFixed(2)}% (${teacher.submitted})
                                    Missing: ${missingPercentage.toFixed(2)}% (${teacher.missing}) 
                                    Assigned: ${assignedPercentage.toFixed(2)}% (${teacher.assigned})">
                            <div class="progress-bar" 
                                 style="width: 100%; 
                                        background: linear-gradient(to right, 
                                        ${colors.submitted} ${submittedPercentage}%, 
                                        ${colors.missing} ${submittedPercentage + missingPercentage}%, 
                                        ${colors.assigned} ${submittedPercentage + missingPercentage}%)">
                            </div>
                        </div>
                    </td>
                    <td>${onTimePercentage.toFixed(0)}%</td>
                </tr>
            `;

            tbody.append(row);
        });

        // Calculate and display overall performance
        const overallPerformance = totalTeachers > 0 ? (totalOnTimePercentage / totalTeachers).toFixed(2) : 0;
        $('#overall-performance').text(overallPerformance + '%');
    }

    // Dynamically generate the year dropdown options (recent year at the top)
    function generateYearOptions() {
        const currentYear = new Date().getFullYear();
        const yearDropdown = $('#year-filter');
        yearDropdown.empty(); // Clear the existing options

        // Fetch the available year ranges and dynamically create the dropdown options
        $.ajax({
            url: 'fetch_schoolyear.php', // Assuming this PHP file returns an array of year ranges (e.g., 2023-2024)
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                const yearRanges = response.yearRanges || [];
                
                // Loop through the year ranges and append them to the dropdown
                yearRanges.forEach(function(yearRange) {
                    const yearOption = new Option(yearRange, yearRange, yearRange === `${currentYear}-${currentYear+1}`, yearRange === `${currentYear}-${currentYear+1}`);
                    yearDropdown.append(yearOption);
                });
            },
            error: function() {
                alert('Error fetching year ranges.');
            }
        });
    }

    // Fetch teacher data with the selected year or year range
    function fetchTeacherData(year) {
        $.ajax({
            url: 'fetch_teacher_progress.php',
            type: 'GET',
            dataType: 'json',
            data: { year: year }, // Pass the year in the request
            success: function (response) {
                if (response.error) {
                    alert(response.error);
                    return;
                }

                $('#department-name').text('Teachers in ' + response.departmentName);
                teacherData = response.teachers; // Store the data

                // If no data is found for the selected year, use the previously fetched data
                if (teacherData.length === 0) {
                    alert('No data available for the selected year. Displaying previous data.');
                } else {
                    renderTable(teacherData); // Render the new data
                }
            },
            error: function () {
                alert('Error fetching teacher data.');
            }
        });
    }

    // Load data for the current year by default
    generateYearOptions(); // Generate year options dynamically
    fetchTeacherData(yearFilter);

    // Add event listener for year filter change
    $('#year-filter').on('change', function () {
        yearFilter = $(this).val(); // Get the selected year or range
        fetchTeacherData(yearFilter); // Fetch data for the selected year or range
    });
});










$(document).ready(function() {
    $('#calendar').fullCalendar({
        header: {
            center: 'title',  // Empty center
            left: 'prev,next', 
            right: ''  // Add 'today' button to the right
        },
        defaultDate: moment().format('YYYY-MM-DD'),
        timeZone: 'Asia/Manila',
        navLinks: true,
        editable: true,
        height: 400,
        buttonIcons: false, 
        themeSystem: 'standard',
        firstDay: 0, 
        dayCellClass: function(date, cell) {
            return 'fc-day-grid-event';
        },
        dayRender: function(date, cell) {
            let today = moment().tz('Asia/Manila').startOf('day');
            let calendarDate = date.startOf('day');

            if (calendarDate.isSame(today)) {
                cell.addClass('fc-today'); 
            }
        },
        viewRender: function(view, element) {
            // Customize the header (arrows only)
            // Customize title format (Month YYYY)
            $('.fc-toolbar .fc-center h2').html(view.title.replace(/(\w+) (\d+)/, '$1 $2')); // Removes the day
        },
        footer: { 
            right: 'today,month,agendaWeek,agendaDay',

        },
        // Customize button behavior
        customButtons: {
            today: {
                text: 'Today',  // Button text
                click: function() {
                    // Navigate to today's date
                    $('#calendar').fullCalendar('gotoDate', moment());
                }
            }
        }
    });
});

</script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <script>
$(document).ready(function() {
    // Fetch initial department data
    $.ajax({
        url: 'fetch_tasks.php',  // Points to your PHP script
        type: 'GET',
        dataType: 'json',        // Expecting a JSON response
        success: function(data) {
            if (data.error) {
                alert(data.error);
            } else {
                var tbody = $('#department-table-body');
                tbody.empty(); // Clear existing rows

                // Loop through departments and create rows dynamically
                data.forEach(function(department) {
                    var row = '<tr>';
                    row += '<td>' + department.dept_name + '</td>';
                    row += `
                        <td class="expand-row" data-department-id="${department.dept_ID}">
                             ${department.totalSubmit}/${department.totalAssigned}<br> <!-- Line break -->
                            <span class="expand-text"> (Click to breakdown)</span>
                            <span id="toggle-arrow-${department.dept_ID}" class="toggle-arrow" >
                                <i class="fas fa-chevron-down"></i> <!-- Down arrow icon -->
                            </span>
                        </td>
                    `;
                    row += '</tr>';

                    // Add an empty row for task breakdown (hidden initially)
                    row += '<tr class="task-details-row" id="details-row-' + department.dept_ID + '" style="display: none;">' +
                        '<td colspan="3">' +
                        '<table class="table table-bordered">' +
                        '<thead>' +
                        '<tr>' +
                        '<th style="font-size: 12px;">Task Title</th>' +
                        '<th style="font-size: 12px;">Task Progress</th>' +
                        '</tr>' +
                        '</thead>' +
                        '<tbody id="tasks-for-department-' + department.dept_ID + '">' +
                        '</tbody>' +
                        '</table>' +
                        '</td>' +
                        '</tr>';

                    tbody.append(row); // Append the new row
                });

                // Use event delegation for dynamically added "expand-row"
                $(document).on('click', '.expand-row', function() {
                    var deptID = $(this).data('department-id');
                    var detailsRow = $('#details-row-' + deptID);
                    var tasksTableBody = $('#tasks-for-department-' + deptID);
                    var arrowIcon = $('#toggle-arrow-' + deptID).find('i');

                    // Toggle task details visibility
                    detailsRow.toggle();

                    // Change the arrow icon based on visibility
                    if (detailsRow.is(':visible')) {
                        arrowIcon.removeClass('fa-chevron-down').addClass('fa-chevron-up'); // Change to up arrow
                    } else {
                        arrowIcon.removeClass('fa-chevron-up').addClass('fa-chevron-down'); // Change to down arrow
                    }

                    // Load task data only if not already loaded
                    if (detailsRow.is(':visible') && tasksTableBody.children().length === 0) {
                        $.ajax({
                            url: 'fetch_tasks_dash.php', // PHP script URL
                            type: 'GET',
                            data: { dept_id: deptID }, // Send department ID
                            dataType: 'json',
                            success: function(response) {
                                if (response.error) {
                                    alert(response.error);
                                } else {
                                    tasksTableBody.empty(); // Clear previous tasks

                                    // Populate task details
                                    response.timestamps.forEach(function(timestamp) {
                                        let taskRow = `
                                            <tr>
                                                <td><strong>${timestamp.TaskTitle}</strong></td>
                                                <td>${timestamp.totalSubmit} / ${timestamp.totalAssigned}</td>
                                            </tr>
                                        `;
                                        tasksTableBody.append(taskRow);
                                    });

                                    // Optionally, update department-level stats if needed
                                    $('#deptStats').html(
                                        'Submitted: ' + response.department.totalSubmit + ' / Assigned: ' + response.department.totalAssigned
                                    );
                                }
                            },
                            error: function() {
                                alert('Error loading task details.');
                            }
                        });
                    }
                });

                // Add hover effect to indicate interactivity
                $(document).on('mouseenter', '.expand-row', function() {
                    $(this).css('cursor', 'pointer'); // Change cursor to pointer on hover
                });
            }
        },
        error: function() {
            alert('Error loading department data.');
        }
    });
});

// Helper function to escape HTML
function htmlspecialchars(str) {
    return str.replace(/[&<>"']/g, function(match) {
        var escape = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return escape[match];
    });
}

// Helper function to format date
function formatDate(date) {
    var options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(date).toLocaleDateString('en-US', options);
}

// Helper function to format time
function formatTime(time) {
    var options = { hour: '2-digit', minute: '2-digit', hour12: true };
    return new Date('1970-01-01T' + time + 'Z').toLocaleTimeString('en-US', options);
}

    // Function to populate year options
    function populateYearSelect() {
        const yearSelect = document.getElementById('yearSelect');
        const currentYear = new Date().getFullYear();
        const startYear = currentYear - 10; // 5 years before
        const endYear = currentYear + 0; // 5 years after
        
        // Clear existing options
        yearSelect.innerHTML = '';

        // Create the "All Years" option
        const allYearsOption = document.createElement('option');
        allYearsOption.value = '';
        allYearsOption.textContent = 'All Years';
        yearSelect.appendChild(allYearsOption);

        // Populate year options
        for (let year = startYear; year <= endYear; year++) {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            yearSelect.appendChild(option);
        }
    }

    // Chart options
    var options = {
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: true
            }
        },
        series: [{
            name: 'Documents Uploaded',
            data: [] // Start with empty data
        }],
        xaxis: {
            categories: [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ],
            title: {
                text: 'Months'
            }
        },
        yaxis: {
            title: {
                text: 'Number of Uploaded Documents'
            },
            min: 0
        },
        tooltip: {
            shared: true,
            intersect: false
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#chart1"), options);
    chart.render();

    // Event listener for year selection
    document.getElementById('yearSelect').addEventListener('change', updateChart);

    // Fetch data from server
    async function fetchData(year) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", `fetch_data.php?year=${year}`, true);
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve(JSON.parse(xhr.responseText));
                } else {
                    reject('Failed to fetch data');
                }
            };
            xhr.onerror = function () {
                reject('Request failed');
            };
            xhr.send();
        });
    }

    // Update chart data
    async function updateChart() {
        var selectedYear = document.getElementById('yearSelect').value;

        try {
            var filteredData = await fetchData(selectedYear);
            chart.updateSeries([{
                name: 'Documents Uploaded',
                data: filteredData
            }]);
        } catch (error) {
            console.error(error);
        }
    }

    // Initialize the year select and chart on page load
    window.onload = function() {
        populateYearSelect();
        updateChart();
    };
    
    
</script>


     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     
      <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($loginSuccess === true): ?>
        Swal.fire({
            title: 'Login Successful!',
            text: 'Welcome back, <?php echo htmlspecialchars($fname); ?>!',
            icon: 'success',
            confirmButtonText: 'OK'
        });
        <?php endif; ?>
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Prepare data for the chart
        var labels = [];
        var assignedData = [];
        var missingData = [];
        var submittedData = [];

        // Loop through teachersData and extract values
        teachersData.forEach(function(teacher) {
            labels.push(teacher.name);  // Teacher names as labels
            assignedData.push(teacher.assigned);  // Assigned task count
            missingData.push(teacher.missing);  // Missing task count
            submittedData.push(teacher.submitted);  // Submitted task count
        });

        // Create the bar chart
        var ctx = document.getElementById('workloadChart').getContext('2d');
        var workloadChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,  // Teacher names as labels
                datasets: [{
                    label: 'Assigned Tasks',
                    data: assignedData,
                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }, {
                    label: 'Missing Tasks',
                    data: missingData,
                    backgroundColor: 'rgba(220, 53, 69, 0.5)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }, {
                    label: 'Submitted Tasks',
                    data: submittedData,
                    backgroundColor: 'rgba(40, 167, 69, 0.5)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

</body>
</html>
