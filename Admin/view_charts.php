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

        @keyframes animate {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-100%);
            }
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
        <main id="printableContent"> <h1 class="title" style="display: flex; align-items: center;">
                School Performance
                <a href="#" onclick="printAllCharts();" style="margin-left: auto; margin-right: 20px; font-size: 20px; color: #9b2035;">
                    <i class="fas fa-print"></i> Print All
                </a>
            </h1>
            <div class="info-data">
                <div class="row">
 
                    <!-- Filter Section -->
                    <div class="col-md-12">
                        <div class="filter-container" style="background-color:#ffff;padding: 10px; box-shadow: 4px 4px 30px rgba(0, 0, 0, 0.05); border-radius: 10px;">
                            <form method="GET" action="">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="filterYear">Select School Year:</label>
                                        <select id="filterYear" name="school_year" class="form-control">
                                            <option value="">Select Year</option>
                                            <?php while ($row = $schoolYears->fetch_assoc()) : ?>
                                                <option value="<?php echo $row['Year_Range']; ?>" 
                                                    <?php if ($row['Year_Range'] == $selectedYear) echo 'selected'; ?>>
                                                    <?php echo $row['Year_Range']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gradeLevel">Grade:</label>
                                        <select id="gradeLevel" name="grade_level" class="form-control">
                                            <option value="">Select Grade</option>
                                            <?php while ($row = $grades->fetch_assoc()) : ?>
                                                <option value="<?php echo $row['Grade_Level']; ?>" 
                                                    <?php if ($row['Grade_Level'] == $selectedGrade) echo 'selected'; ?>>
                                                    <?php echo $row['Grade_Level']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Chart Section in 2x3 grid -->
                    <div class="col-md-12" style="margin-top: 20px;">
                        <div class="chart-container">
                            <!-- Row 1: Three charts -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Enrollment Rate</h6>
                                    <div class="chart-item" >
                                        <canvas id="enrollmentChart"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Dropout Rate</h6>
                                    <div class="chart-item" >
                                        <canvas id="dropoutChart"></canvas>
                                    </div>
                                </div>    
                            </div>

                            <!-- Row 2: Three more charts -->
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-6">
                                    <h6>Cohort-Survival Rate</h6>
                                    <div class="chart-item">
                                        <canvas id="cohortChart"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Repetition Rate</h6>
                                    <div class="chart-item">
                                        <canvas id="repetitionChart"></canvas>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-6">
                                    <h6>Promotion Rate</h6>
                                    <div class="chart-item" >
                                        <canvas id="promotionChart"></canvas>
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
    function printAllCharts() {
        // Get all chart canvas elements
        const charts = document.querySelectorAll('canvas');
        
        // Create an iframe to print each chart
        const iframe = document.createElement('iframe');
        iframe.style.position = 'absolute';
        iframe.style.top = '-10000px';
        document.body.appendChild(iframe);

        charts.forEach((chart, index) => {
            const chartCanvas = chart.cloneNode(true);
            chartCanvas.style.width = '100%';  // Make the chart fit the iframe

            // Append cloned chart to the iframe document body
            const doc = iframe.contentWindow.document;
            const div = doc.createElement('div');
            div.appendChild(chartCanvas);
            doc.body.appendChild(div);

            if (index < charts.length - 1) {
                doc.write('<div style="page-break-before: always;"></div>');
            }
        });

        // Trigger the print
        iframe.contentWindow.print();

        // Cleanup: remove iframe from the DOM after printing
        setTimeout(() => {
            document.body.removeChild(iframe);
        }, 1000);
    }
</script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const yearDropdown = document.querySelector('select[name="school_year"]');
            const gradeDropdown = document.querySelector('select[name="grade_level"]');

            [yearDropdown, gradeDropdown].forEach(dropdown => {
                dropdown.addEventListener("change", () => {
                    const schoolYear = yearDropdown.value;
                    const gradeLevel = gradeDropdown.value;

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

        // Trigger chart update when selecting a filter
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

        document.addEventListener("DOMContentLoaded", () => {
            const data = <?php echo json_encode($data); ?>;
            const labels = data.map((item) => `Grade ${item.Grade_Level}`);
            const enrollmentData = data.map((item) => item.total_enrollment);
            const dropoutData = data.map((item) => item.avg_dropout_rate);
            const promotionData = data.map((item) => item.avg_promotion_rate);
            const cohortData = data.map((item) => item.avg_cohort_rate);
            const repetitionData = data.map((item) => item.avg_repetition_rate);
            const transitionData = data.map((item) => item.avg_transition_rate);

            createChart("enrollmentChart", "Total Enrollment", labels, enrollmentData);
            createChart("dropoutChart", "Dropout Rate (%)", labels, dropoutData);
            createChart("promotionChart", "Promotion Rate (%)", labels, promotionData);
            createChart("cohortChart", "Cohort Survival Rate (%)", labels, cohortData);
            createChart("repetitionChart", "Repetition Rate (%)", labels, repetitionData);
            createChart("transitionChart", "Transition Rate (%)", labels, transitionData);

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
        });
    </script>
</body>
</html>
