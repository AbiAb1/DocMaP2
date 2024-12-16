<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not authenticated
    header("Location: ../index.php");
    exit();
}


include 'connection.php';

$user_id = $_SESSION['user_id'];

// Fetch user information
$sql_user = "SELECT * FROM useracc WHERE UserID = ?";
if ($stmt_user = $conn->prepare($sql_user)) {
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    $fname = '';
    if ($result_user->num_rows > 0) {
        $row = $result_user->fetch_assoc();
        $fname = $row['fname'];
    }
    $stmt_user->close();
} else {
    echo "Error preparing user query";
}


// Fetch distinct School Years and Grades
$schoolYears = $conn->query("SELECT DISTINCT Year_Range FROM schoolyear");
$grades = $conn->query("SELECT DISTINCT Grade_Level FROM grade");

// Default filter values
$selectedYear = $_GET['school_year'] ?? null;
$selectedGrade = $_GET['grade_level'] ?? null;

// Base SQL query with optional filters
$sql = "
    SELECT g.Grade_Level, 
           SUM(e.Enroll_Gross) AS total_enrollment, 
           AVG(d.Dropout_Rate) AS avg_dropout_rate, 
           AVG(p.Promotion_Rate) AS avg_promotion_rate, 
           AVG(c.Cohort_Rate) AS avg_cohort_rate, 
           AVG(r.Repeaters_Rate) AS avg_repetition_rate, 
           AVG(t.Transition_Rate) AS avg_transition_rate
    FROM performance_indicator pi
    JOIN grade g ON pi.Grade_ID = g.Grade_ID
    JOIN enroll e ON pi.Enroll_ID = e.Enroll_ID
    JOIN dropout d ON pi.Dropout_ID = d.Dropout_ID
    JOIN promotion p ON pi.Promotion_ID = p.Promotion_ID
    JOIN cohort_survival c ON pi.Cohort_ID = c.Cohort_ID
    JOIN repetition r ON pi.Repetition_ID = r.Repetition_ID
    JOIN transition t ON pi.Transition_ID = t.Transition_ID
    JOIN schoolyear sy ON pi.School_Year_ID = sy.School_Year_ID
    WHERE 1=1";

// Add filters
if ($selectedYear) {
    $sql .= " AND sy.Year_Range = '$selectedYear'";
}
if ($selectedGrade) {
    $sql .= " AND g.Grade_Level = '$selectedGrade'";
}

$sql .= " GROUP BY g.Grade_Level";

$result = $conn->query($sql);

// Fetch the data
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
date_default_timezone_set('Asia/Manila'); // Set timezone to Manila
$hour = date('H');

if ($hour >= 5 && $hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour >= 12 && $hour < 18) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

$totalDepartmentQuery = "SELECT COUNT(*) as total FROM department";
$totalDepartmentResult = mysqli_query($conn, $totalDepartmentQuery);
$totalDepartment = mysqli_fetch_assoc($totalDepartmentResult)['total'];

// Fetch total users, department heads, and teachers
$totalUsersQuery = "
    SELECT COUNT(*) AS total,  
           SUM(CASE WHEN role = 'Department Head' THEN 1 ELSE 0 END) AS department_head_count,
           SUM(CASE WHEN role = 'Teacher' THEN 1 ELSE 0 END) AS teacher_count
    FROM useracc 
    WHERE role != 'Admin' AND Status = 'Approved'";
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalUsersData = mysqli_fetch_assoc($totalUsersResult);

// Assign PHP variables
$totalUsers = $totalUsersData['total'] ?? 0;
$departmentHeadCount = $totalUsersData['department_head_count'] ?? 0;
$teacherCount = $totalUsersData['teacher_count'] ?? 0;


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <!-- Load Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    
    <title>Dashboard | Home</title>
    <style>
        .title{
            padding-bottom: 30px;
        }

        .info-data {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: -10px;
        }

        .card .head {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card .head h1, .card {
            margin: 0;
            font-weight: bold;
        }

        .card .head i.icon {
            font-size: 40px;
            color: #9B2035;
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

        canvas {
            background: #ffffff;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 10px;
        }

        .charts {
            margin-top: 30px;
        }

        .container {
            padding: 20px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
            border-radius: 10px; 
            background: #fff; 
            margin-bottom: 20px;
        }

        .card {
            padding: 20px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
            border-radius: 10px; 
            background: #fff; 
            margin-bottom: 20px;
        }

        #productivityChart {
            display: block;
            width: 100%;
            max-height: 400px;
        }

        .carousel-inner canvas {
            width: 100% !important;
            height: 400px !important;
            max-height: 400px;
        }

        .carousel-controls {
            position: absolute;
            top: -50px;
            right: 50px;
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 40px;
            height: 40px;
            background-color: #fff;
            border-radius: 50%; 
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: #9b2035; 
        }

        .circle-button {
            position: absolute;
            top: 0;
            right: 50px;
            width: 40px;
            height: 40px;
            background-color: #9b2035;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .doughnut-container {
            width: 70px; 
            height: 70px; 
            display: inline-block; 
            position: relative; 
        }

        .doughnut-container canvas {
            width: 100% !important; 
            height: 100% !important;
        }

        .user-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 10px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
            background-color: #ffffff; 
            transition: box-shadow 0.3s; 
        }

        .user-container:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); 
        }

        .table {
            width: 100%;                     
            border-collapse: collapse;       
            margin: 20px 0;                  
        }

        .table th {
            background-color: #9B2035;         
            color: white;                     
            text-align: center;              
        }

        .table td {
            padding: 10px;                    
            text-align: center;               
            border: 1px solid #ccc;          
            background-color: #ffff;
            font-weight: bold;
        }

        .table tr:nth-child(even) {
            background-color: #f2f2f2;        
        }

        .table tr:nth-child(odd) {
            background-color: #ffffff;        
        }

        .table tr:hover {
            background-color: #e0e0e0;        
        }

        @media (max-width: 767px) {
            .card {
                margin-bottom: 10px;
            }

            .carousel-inner canvas {
                height: auto !important;
            }

            .carousel-controls {
                top: -40px;
                right: 30px;
            }

            .circle-button {
                right: 20px;
                top: 0;
            }

            .info-data {
                flex-direction: column;
                align-items: center;
            }

            .col-md-7, .col-md-5 {
                max-width: 100%;
                flex: 0 0 100%;
                margin-bottom: 15px;
            }

            .table {
                font-size: 12px;
            }

            .user-container {
                flex-direction: column;
                text-align: center;
            }

            .user-container h1, .user-container h5 {
                font-size: 1.2rem;
            }

        }
        .card-body {
            position: relative; /* Ensures the positioning is correct */
        }

        .icon1 {
            color: #9b2035; /* Set icon color */
            font-size: 40px; /* Set icon size */
            position: absolute; /* Positioning the icon absolutely */
            top: 15px; /* Distance from the top */
            right: 15px; /* Distance from the right */
        }
        .chart-container {
            width: 90px; /* Width of the chart container */
            height: 90px; /* Height of the chart container */
            margin: 0 auto; /* Center the charts within their columns */
            border: none; /* No border */
            background-color: transparent; /* Transparent background */
            position: relative; /* Allows positioning */
        }

        .chart-container.top-right {
            position: absolute; /* Positioning relative to its parent */
            top: -30px; /* Position from the top */
            right: -20px; /* Position from the right */
        }

        canvas {
        
            border: none; /* No border */
            background-color: transparent; /* Transparent background */
            position: relative; /* Allows positioning */
        }


    </style>
</head>
<body>

<?php
// Check if login was successful
if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true) {
    // Unset the session variable after using it
    unset($_SESSION['login_success']);
    echo "
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Login Successful',
            text: 'Welcome back, " . htmlspecialchars($fname) . "!',
            confirmButtonText: 'OK'
        });
    });
    </script>";
}
?>
    <!-- SIDEBAR -->
    <section id="sidebar">
        
        <?php include 'navbar.php'; ?>
    </section>
    <!-- SIDEBAR -->

    <!-- NAVBAR -->
    <section id="content">
        <?php include 'topbar.php'; ?>
        <!-- NAVBAR -->

       <!-- MAIN -->
    <main>
        <div style="display: flex; align-items: center; margin-bottom: 30px;">
            <h2 class="title" style="font-weight: bold; color: #9b2035; margin: 0;">
                <?php echo $greeting; ?>, <?php echo htmlspecialchars($fname); ?>!
                <img src="img/EYYY.gif" alt="Animated GIF" style="height: 30px; vertical-align: middle;">
            </h2>
        </div>
        <div class="info-data">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card" style="height:230px;">
                                                <div class="card-body">
                                                    <!-- Top Row: Content -->
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <h1 style="font-weight:bold;"><?php echo $totalUsers; ?></h1>
                                                            <p style="font-weight: bold;margin-top:20px;">Users</p>
                                                        </div>
                                                        <div class="col-6 text-end">
                                                            <i class="fa-solid fa-users icon1"></i>
                                                        </div>
                                                    </div>

                                                    <!-- Bottom Row: Empty Space -->
                                                   <div class="row">
                                                        <div class="col-12">
                                                            <div class="row">
                                                                <!-- Department Head Count -->
                                                                <div class="col-6 text-left">
                                                                    <h4 style="font-weight: bold;"><?php echo $departmentHeadCount; ?></h4>
                                                                    <div class="chart-container top-right">
                                                                        <canvas id="deptHeadChart"></canvas>
                                                                    </div>
                                                                    <p style="font-weight: bold; font-size: 13px; margin-top: 10px;">Dept Head</p>
                                                                </div>

                                                                <!-- Teacher Count -->
                                                                <div class="col-6 text-left">
                                                                    <h4 style="font-weight: bold;"><?php echo $teacherCount; ?></h4>
                                                                    <div class="chart-container top-right">
                                                                        <canvas id="teacherChart"></canvas>
                                                                    </div>
                                                                    <p style="font-weight: bold; font-size: 13px; margin-top: 10px;">Teacher</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card" style="height:230px;">
                                                <div class="head">
                                                    <div>
                                                        <h1><?php echo $totalDepartment; ?></h1>
                                                        <p style="font-weight: bold;">Departments</p>
                                                    </div>
                                                    <i class="fa-solid fa-building icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card" style="height:260px;">
                                                <h4>Top Performing Teachers</h4>
                                                <div id="userList" style="max-height: 400px; overflow-y: auto; padding:15px;">
                                                    <!-- User containers will be dynamically appended here by the script -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div id="chartCarousel" class="carousel slide container" data-ride="carousel">
                                <h4 class="">School Performance</h4>
                                <!-- Carousel controls on top -->
                                <div class="carousel-controls">
                                    <a href="view_charts.php" class="circle-button">
                                        <i class="fa-solid fa-eye" style="color:white;text-decoration:none;"></i>
                                    </a>
                                    <a class="carousel-control-prev" href="#chartCarousel" role="button" data-slide="prev">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                    <a class="carousel-control-next" href="#chartCarousel" role="button" data-slide="next">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </div>

                                <div class="carousel-inner">
                                    <!-- Chart 1 -->
                                    <div class="carousel-item active">                                 
                                        <h6>ENROLLMENT (Total Enrollment)</h6>
                                        <canvas id="enrollmentChart"></canvas>                                   
                                    </div>
                                    <!-- Chart 2 -->
                                    <div class="carousel-item">                                     
                                        <h6>DROP OUT RATE (%)</h6>
                                        <canvas id="dropoutChart"></canvas>                                      
                                    </div>
                                    <!-- Chart 3 -->
                                    <div class="carousel-item">                                       
                                        <h6>PROMOTION RATE (%)</h6>
                                        <canvas id="promotionChart"></canvas>                                       
                                    </div>
                                    <!-- Chart 4 -->
                                    <div class="carousel-item">                                      
                                        <h6>COHORT SURVIVAL RATE (%)</h6>
                                        <canvas id="cohortChart"></canvas>                                       
                                    </div>
                                    <!-- Chart 5 -->
                                    <div class="carousel-item">                                     
                                        <h6>REPETITION RATE (%)</h6>
                                        <canvas id="repetitionChart"></canvas>                                       
                                    </div>
                                </div>
                            </div>

                            
                        </div>
                    </div>
                </div>
                

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="card" style="height: 500px; overflow-y: auto; position: relative;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="Title">Task Submissions / Department</h5>
                                    <!-- Search bar -->
                                    <input type="text" id="search-input" class="form-control" placeholder="Search..." style="width: 250px;">
                                </div>
                                <table class="table table-bordered table-striped" style="margin-top: 15px;">
                                    <thead>
                                        <tr>
                                            <th>Department</th>
                                            <th>Department Head</th>
                                            <th>Tasks Completion</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="department-table-body">
                                        <!-- Data will be inserted here via AJAX -->
                                    </tbody>
                                </table>
                            </div>        
                        </div>
                        <div class="col-md-7">
                            <div class="card" style="height: 97%; padding: 15px;">
                                <div class="head">
                                    <h5 style="margin-bottom: 15px;">Productivity by Month</h5>
                                </div>
                                <!-- Canvas container -->
                                <div style="width: 100%; height: 88%; display: flex; justify-content: center; align-items: center;">
                                    <canvas id="productivityChart" style="width: 90%; height: 100%;"></canvas>
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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="assets/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            // Overall user count and role-specific counts
            const totalUsers = <?php echo $totalUsers; ?>;
            const deptHeadCount = <?php echo $departmentHeadCount; ?>;
            const teacherCount = <?php echo $teacherCount; ?>;

            // Validate totalUsers to prevent division by zero
            const deptHeadPercentage = totalUsers > 0 ? (deptHeadCount / totalUsers) * 100 : 0;
            const teacherPercentage = totalUsers > 0 ? (teacherCount / totalUsers) * 100 : 0;

            // Department Head Chart Data
            const dataDeptHead = {
                datasets: [{
                    data: [deptHeadPercentage, 100 - deptHeadPercentage],
                    backgroundColor: ['#9b2035', '#cccccc'],
                    hoverOffset: 4
                }]
            };

            // Teacher Chart Data
            const dataTeacher = {
                datasets: [{
                    data: [teacherPercentage, 100 - teacherPercentage],
                    backgroundColor: ['#9b2035', '#cccccc'],
                    hoverOffset: 4
                }]
            };

            // Render Department Head Pie Chart
            new Chart(document.getElementById('deptHeadChart').getContext('2d'), {
                type: 'pie',
                data: dataDeptHead,
                options: {
                    responsive: true,
                    plugins: { legend: { display: true } }
                }
            });

            // Render Teacher Pie Chart
            new Chart(document.getElementById('teacherChart').getContext('2d'), {
                type: 'pie',
                data: dataTeacher,
                options: {
                    responsive: true,
                    plugins: { legend: { display: true } }
                }
            });
        });
    </script>




    <script>
        document.getElementById('search-input').addEventListener('keyup', function() {
            var input = this.value.toLowerCase();
            var tableRows = document.querySelectorAll('#department-table-body tr');

            tableRows.forEach(function(row) {
                var rowText = row.textContent.toLowerCase();
                if (rowText.includes(input)) {
                    row.style.display = ''; // Show row if it matches search
                } else {
                    row.style.display = 'none'; // Hide row if it doesn't match
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: 'fetch_tasks.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response); // Check the response structure
                    let tableBody = '';
                    
                    $.each(response, function(index, dept) {
                        let tasks = dept.totalSubmit + " / " + dept.totalAssigned;

                        let userRows = dept.users.map(user => `
                            <div class="user-row">
                                <img src="../img/UserProfile/${user.profile}" alt="${user.fullname}" class="profile-image" />
                                ${user.fullname}
                            </div>
                        `).join('');

                        tableBody += `
                            <tr>
                                <td>${dept.dept_name}</td>
                                <td>${userRows}</td>
                                <td>${tasks}</td>
                                <td>
                                    <div class="chart-container">
                                        <canvas id="trendChart${index}" width="40" height="40"></canvas>
                                    </div>
                                </td>
                            </tr>`;
                    });

                    $('#department-table-body').html(tableBody);

                    // Initialize charts after the table body is populated
                    $.each(response, function(index, dept) {
                        let ctx = document.getElementById(`trendChart${index}`).getContext('2d');
                        new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: ['Submitted', 'Assigned'],
                                datasets: [{
                                    data: [dept.totalSubmit, dept.totalAssigned],
                                    backgroundColor: ['rgba(75, 192, 192, 0.6)', 'rgba(255, 99, 132, 0.6)'],
                                    borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: false // Hide the legend
                                    },
                                    tooltip: {
                                        enabled: false // Disable tooltip if desired
                                    }
                                },
                                elements: {
                                    arc: {
                                        borderWidth: 1 // Adjust border width if needed
                                    }
                                }
                            }
                        });
                    });
                },
                error: function(error) {
                    console.log("Error fetching data", error);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $.ajax({
                url: "fetchTopTeachers.php", // Backend script to fetch data
                method: "GET",
                dataType: "json",
                success: function (response) {
                    const container = $("#userList");
                    container.empty();

                    let rank = 1;

                    response.forEach(user => {
                        const chartId = `chart-${rank}`; // Unique ID for each chart

                        // Determine the rank style
                        let rankStyle;
                        if (rank === 1) {
                            rankStyle = '<i class="fas fa-star" style="color: gold; font-size: 40px;" ></i>';
                        } else if (rank === 2) {
                            rankStyle = '<i class="fas fa-star" style="color: silver; font-size: 30px;"></i>';
                        } else if (rank === 3) {
                            rankStyle = '<i class="fas fa-star" style="color: #cd7f32; font-size: 42px;"></i>';
                        } else {
                            rankStyle = rank;
                        }

                        // User container with doughnut chart
                        const userContainer = `
                            <div class="user-container" >
                                <!-- Leftmost: Rank -->
                                <div style="flex: 1; text-align: center; font-weight: bold; font-size: 18px;">
                                    ${rankStyle}
                                </div>
                                
                                <!-- Middle: Profile and Full Name -->
                                <div style="flex: 6; display: flex; align-items: center;">
                                    <img src="../img/UserProfile/${user.profile}" alt="Profile Picture" 
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; margin-right: 15px;">
                                    <div>
                                        <h5 style="margin: 0;">${user.full_name}</h5>
                                    </div>
                                </div>
                                
                                <!-- Rightmost: Doughnut Chart -->
                                <div class="doughnut-container">
                                    <canvas id="${chartId}"></canvas>
                                </div>
                            </div>
                        `;

                        container.append(userContainer);

                        // Create the doughnut chart
                        const ctx = document.getElementById(chartId).getContext("2d");
                        new Chart(ctx, {
                            type: "doughnut",
                            data: {
                                labels: ["Precision", "Remaining"],
                                datasets: [{
                                    data: [user.precision, 100 - user.precision],
                                    backgroundColor: ["#4CAF50", "#E0E0E0"],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: false, // Disable responsiveness for fixed size
                                maintainAspectRatio: false, // Disable aspect ratio to control size
                                cutout: "75%", // Adjust inner hole size
                                plugins: {
                                    tooltip: { enabled: false },
                                    legend: { display: false }
                                }
                            },
                            plugins: [createCenterTextPlugin(user.precision)]
                        });

                        rank++;
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching data:", error);
                }
            });
        });

        // Plugin to add percentage in the middle of the doughnut
        function createCenterTextPlugin(precision) {
            return {
                id: "centerText",
                beforeDraw(chart) {
                    const { width } = chart;
                    const { height } = chart;
                    const ctx = chart.ctx;

                    ctx.restore();
                    const fontSize = (height / 5).toFixed(2); // Dynamic font size based on height
                    ctx.font = `${fontSize}px Arial`;
                    ctx.textBaseline = "middle";

                    const text = `${precision}%`;
                    const textX = Math.round((width - ctx.measureText(text).width) / 2);
                    const textY = height / 2;

                    ctx.fillStyle = "#333"; // Text color
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
            };
        }
    </script>


   <script>
        document.addEventListener("DOMContentLoaded", () => {
            const data = <?php echo json_encode($data); ?>;
            const labels = data.map((item) => `Grade ${item.Grade_Level}`);
            const enrollmentData = data.map((item) => item.total_enrollment);
            const dropoutData = data.map((item) => item.avg_dropout_rate);
            const promotionData = data.map((item) => item.avg_promotion_rate);
            const cohortData = data.map((item) => item.avg_cohort_rate);
            const repetitionData = data.map((item) => item.avg_repetition_rate);
            const transitionData = data.map((item) => item.avg_transition_rate);

            // Function to create a chart
            function createChart(canvasId, label, labels, data) {
                const ctx = document.getElementById(canvasId).getContext("2d");
                new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: label,
                                data: data,
                                backgroundColor: "rgba(155, 32, 53, 0.2)", // Change to the desired background color
                                borderColor: "rgba(155, 32, 53, 1)", // Border color remains the same
                                borderWidth: 1,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                            },
                        },
                    },
                });
            }

            // Initialize charts
            createChart("enrollmentChart", "Total Enrollment", labels, enrollmentData);
            createChart("dropoutChart", "Dropout Rate (%)", labels, dropoutData);
            createChart("promotionChart", "Promotion Rate (%)", labels, promotionData);
            createChart("cohortChart", "Cohort Survival Rate (%)", labels, cohortData);
            createChart("repetitionChart", "Repetition Rate (%)", labels, repetitionData);
            createChart("transitionChart", "Transition Rate (%)", labels, transitionData);

            // Update charts on dropdown change
            document.querySelectorAll('.form-select').forEach(select => {
                select.addEventListener('change', () => {
                    const schoolYear = document.querySelector('select[name="school_year"]').value;
                    const gradeLevel = document.querySelector('select[name="grade_level"]').value;
                    const url = new URL(window.location.href);
                    if (schoolYear) url.searchParams.set('school_year', schoolYear);
                    else url.searchParams.delete('school_year');
                    if (gradeLevel) url.searchParams.set('grade_level', gradeLevel);
                    else url.searchParams.delete('grade_level');
                    window.location.href = url.toString(); // Automatically reload the page with new parameters
                });
            });

            // Update the page URL and reload the page when school year or grade level is changed
            document.querySelectorAll('.form-select').forEach(dropdown => {
                dropdown.addEventListener("change", () => {
                    const schoolYear = document.querySelector('select[name="school_year"]').value;
                    const gradeLevel = document.querySelector('select[name="grade_level"]').value;

                    const url = new URL(window.location.href);

                    // Update the query parameters
                    if (schoolYear) {
                        url.searchParams.set("school_year", schoolYear);
                    } else {
                        url.searchParams.delete("school_year");
                    }

                    if (gradeLevel) {
                        url.searchParams.set("grade_level", gradeLevel);
                    } else {
                        url.searchParams.delete("grade_level");
                    }

                    // Reload the page with updated parameters
                    window.location.href = url.toString();
                });
            });
        });
    </script>
    <script>
        // Fetch data from the backend and render the chart
        fetch('productivity_data.php')
            .then(response => response.json())
            .then(data => {
                // Parse data for the chart
                const labels = data.map(item => item.Month);
                const submittedTasks = data.map(item => parseInt(item.SubmittedTasks));
                const approvedTasks = data.map(item => parseInt(item.ApprovedTasks));

                // Create the chart
                const ctx = document.getElementById('productivityChart').getContext('2d');
                const productivityChart = new Chart(ctx, {
                    type: 'line', // Line chart for monthly trends
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Submitted Tasks',
                                data: submittedTasks,
                                borderColor: 'blue',
                                backgroundColor: 'rgba(66, 133, 244, 0.2)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Approved Tasks',
                                data: approvedTasks,
                                borderColor: 'green',
                                backgroundColor: 'rgba(52, 168, 83, 0.2)',
                                fill: true,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Disable aspect ratio preservation
                        
                        scales: {
                            x: {
                                title: { display: true, text: 'Month' }
                            },
                            y: {
                                title: { display: true, text: 'Tasks' },
                                beginAtZero: true
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching productivity data:', error);
            });
    </script>
</body>
</html>
