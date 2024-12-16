<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include 'connection.php'; // Include your database connection file

$userId = $_SESSION['user_id']; // Get the logged-in user's ID

// Fetch available school years
$schoolYearsQuery = "
    SELECT DISTINCT sy.School_Year_ID, sy.Year_Range
    FROM schoolyear sy
    JOIN performance_indicator pi ON sy.School_Year_ID = pi.School_Year_ID
    WHERE pi.UserID = ?
    ORDER BY sy.Year_Range DESC";

$stmt = $conn->prepare($schoolYearsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$schoolYearsResult = $stmt->get_result();

$schoolYears = [];
if ($schoolYearsResult && $schoolYearsResult->num_rows > 0) {
    while ($row = $schoolYearsResult->fetch_assoc()) {
        $schoolYears[$row['School_Year_ID']] = $row['Year_Range'];
    }
}
$stmt->close();

// Fetch records based on the selected school year
$selectedYear = isset($_GET['year']) && ctype_digit($_GET['year']) ? $_GET['year'] : "All";
$records = [];

if ($selectedYear === "All") {
    foreach ($schoolYears as $yearId => $year) {
        $query = "
            SELECT 
                pi.Performance_ID,
                sy.School_Year_ID,
                g.Grade_Level,
                e.Enroll_Gross,
                e.Enroll_Net,
                c.Cohort_Figure,
                c.Cohort_Rate,
                d.Dropout_Figure,
                d.Dropout_Rate,
                r.Repeaters_Figure,
                r.Repeaters_Rate,
                p.Promotion_Figure,
                p.Promotion_Rate,
                t.Transition_Figure,
                t.Transition_Rate,
                a.Age_Group_12To15,
                n.NAT_Score
            FROM performance_indicator pi
            JOIN schoolyear sy ON pi.School_Year_ID = sy.School_Year_ID
            JOIN grade g ON pi.Grade_ID = g.Grade_ID
            JOIN enroll e ON pi.Enroll_ID = e.Enroll_ID
            JOIN dropout d ON pi.Dropout_ID = d.Dropout_ID
            JOIN promotion p ON pi.Promotion_ID = p.Promotion_ID
            JOIN cohort_survival c ON pi.Cohort_ID = c.Cohort_ID
            JOIN repetition r ON pi.Repetition_ID = r.Repetition_ID
            JOIN age a ON pi.Age_ID = a.Age_ID
            JOIN nat n ON pi.NAT_ID = n.NAT_ID
            JOIN transition t ON pi.Transition_ID = t.Transition_ID
            WHERE sy.School_Year_ID = ? AND pi.UserID = ?
            ORDER BY g.Grade_Level";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $yearId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $records[$year] = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
    }
} else {
    $query = "
        SELECT 
            pi.Performance_ID,
            sy.School_Year_ID,
            g.Grade_Level,
            e.Enroll_Gross,
            e.Enroll_Net,
            c.Cohort_Figure,
            c.Cohort_Rate,
            d.Dropout_Figure,
            d.Dropout_Rate,
            r.Repeaters_Figure,
            r.Repeaters_Rate,
            p.Promotion_Figure,
            p.Promotion_Rate,
            t.Transition_Figure,
            t.Transition_Rate,
            a.Age_Group_12To15,
            n.NAT_Score
        FROM performance_indicator pi
        JOIN schoolyear sy ON pi.School_Year_ID = sy.School_Year_ID
        JOIN grade g ON pi.Grade_ID = g.Grade_ID
        JOIN enroll e ON pi.Enroll_ID = e.Enroll_ID
        JOIN dropout d ON pi.Dropout_ID = d.Dropout_ID
        JOIN promotion p ON pi.Promotion_ID = p.Promotion_ID
        JOIN cohort_survival c ON pi.Cohort_ID = c.Cohort_ID
        JOIN repetition r ON pi.Repetition_ID = r.Repetition_ID
        JOIN age a ON pi.Age_ID = a.Age_ID
        JOIN nat n ON pi.NAT_ID = n.NAT_ID
        JOIN transition t ON pi.Transition_ID = t.Transition_ID
        WHERE sy.School_Year_ID = ? AND pi.UserID = ?
        ORDER BY g.Grade_Level";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $selectedYear, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $records[$schoolYears[$selectedYear]] = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
}

// Close database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Indicator - Admin</title>
    <link rel="icon" type="image/png" href="img/Logo/docmap-logo-1.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .header-section {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-right: 20px;
            margin-bottom: 20px;
        }

        h1.title {
            margin: 0;
        }

        /* Style to center the table */
        .table-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        table {
            width: 80%;
            border-collapse: collapse;
        }

        th, td {
            text-align: center;
            padding: 10px;
        }

        th {
            background-color: #9B2035;
            color: white;
        }

        td {
            background-color: #ffff;
        }

        .button-group {
            display: flex; /* Align buttons in a row */
            gap: 10px; /* Space between buttons */
            justify-content: center; /* Center buttons horizontally */
            margin-bottom: 10px;
        }

        

        .centered {
            text-align: center;
            vertical-align: middle;
        }

        .text-school-year{
            text-align: flex;
            margin-top: 20px;
        }

        /* Modal Background */
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5); /* Slightly transparent black background */
        }

        /* Modal Dialog */
        .modal-dialog {
            max-width: 800px;
            margin: 1.75rem auto;
            padding: 20px;
        }

        /* Modal Content */
        .modal-content {
            border-radius: 8px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            padding: 20px;
        }

        /* Modal Header */
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #aaa;
        }

        .close:hover,
        .close:focus {
            color: #000;
            cursor: pointer;
        }

        /* Modal Body */
        .modal-body {
            padding: 20px 0;
        }

        /* Form Labels */
        .modal-body label {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }

        /* Form Inputs */
        .modal-body input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* Buttons */
        .modal-body .btn {
            padding: 10px 15px;
            font-size: 1rem;
            margin-right: 10px;
        }

        .btn-primary {
            background-color: #9B2035;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            margin-left: 10px;
        }

        .btn-primary:hover {
            background-color: #7a1a2b;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Button Group */
        .modal-body button {
            margin-top: 15px;
        }

        /* Ensure modal appears above the backdrop */
        .modal {
            z-index: 1055 !important; /* Ensure modal is above */
        }

        .modal-backdrop {
            z-index: 1045; /* Ensure backdrop is below the modal */
        }

        /* Responsive Adjustments */
        @media (max-width: 767px) {
            .modal-dialog {
                margin-top: 10px;
                width: 100%;
            }

            .modal-body input {
                font-size: 0.9rem;
            }

            .modal-title {
                font-size: 1.25rem;
            }
        }
         .buttonEdit {
            background-color: blue;
            color: white;
            border: none;
            border-radius: 50%; /* Makes the button a perfect circle */
            padding: 10px;
            cursor: pointer;
            transition: background-color 0.3s; /* Smooth transition effect */
            width: 40px; /* Set a fixed width for the button */
            height: 40px; /* Set a fixed height for the button */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .buttonEdit:hover {
            background-color: darkblue; /* Darker shade on hover for emphasis */
        }
        .buttonDelete {
            background-color: #d92b2b; /* A deep red color */
            color: white;
            border: none;
            border-radius: 50%; /* Makes the button a perfect circle */
            padding: 10px;
            cursor: pointer;
            transition: background-color 0.3s; /* Smooth transition effect for background color */
            width: 40px; /* Set a fixed width for the button */
            height: 40px; /* Set a fixed height for the button */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .buttonDelete:hover {
            background-color: #b72828; /* Slightly darker red on hover for emphasis */
        }
        .form-control{
           background-color:#F1F0F6;
        }

    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <?php include 'navbar.php'; ?>
    </section>

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <?php include 'topbar.php'; ?>

        <!-- MAIN -->
        <main>
            <div class="header-section">
                <h1 class="title">Performance Indicator</h1>
                <!-- Buttons side by side -->
                <div class="button-group">
                    <button class="btn btn-primary" onclick="openAddRecordModal()">Add Record</button> <!-- Add Record Button -->
                </div>
            </div>

            <!-- Dropdowns Above the Table -->
            <div class="filter-station">
                <label for="schoolYearFilter">School Year:</label>
                <select id="schoolYearFilter" class="form-control" style ="background-color:#ffff;" onchange="filterByYear()">
                    <option value="All" <?= $selectedYear === "All" ? "selected" : "" ?>>All</option>
                    <?php foreach ($schoolYears as $yearId => $year): ?>
                        <option value="<?= $yearId ?>" <?= $selectedYear == $yearId ? "selected" : "" ?>>
                            <?= $year ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if (!empty($records)): ?>
                    <?php foreach ($records as $year => $yearRecords): ?>
                        <h3 class="text-center text-school-year"><?= $year ?></h3>
                        <div class="table-container">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="1">Grade</th>
                                        <th colspan="2">Enrollment</th>
                                        <th colspan="2">Cohort</th>
                                        <th colspan="2">Drop/School Leavers</th>
                                        <th colspan="2">Repeaters</th>
                                        <th colspan="2">Graduation</th>
                                        <th colspan="2">Transition</th>
                                        <th colspan="2">Age</th>
                                        <th rowspan="2">NAT</th>
                                        <th rowspan="2">Action</th>
                                    </tr>
                                    <tr>
                                        <th>Level</th>
                                        <th>Gross</th>
                                        <th>Net</th>
                                        <th>Figure</th>
                                        <th>Rate</th>
                                        <th>Figure</th>
                                        <th>Rate</th>
                                        <th>Figure</th>
                                        <th>Rate</th>
                                        <th>Figure</th>
                                        <th>Rate</th>
                                        <th>Figure</th>
                                        <th>Rate</th>
                                        <th  colspan='2'>12-15</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($yearRecords)): ?>
                                        <?php foreach ($yearRecords as $record): ?>
                                            <tr>
                                                <td><?= $record['Grade_Level'] ?></td>
                                                <td><?= $record['Enroll_Gross'] ?></td>
                                                <td><?= $record['Enroll_Net'] ?></td>
                                                <td><?= $record['Cohort_Figure'] ?></td>
                                                <td><?= $record['Cohort_Rate'] ?></td>
                                                <td><?= $record['Dropout_Figure'] ?></td>
                                                <td><?= $record['Dropout_Rate'] ?></td>
                                                <td><?= $record['Repeaters_Figure'] ?></td>
                                                <td><?= $record['Repeaters_Rate'] ?></td>
                                                <td><?= $record['Promotion_Figure'] ?></td>
                                                <td><?= $record['Promotion_Rate'] ?></td>
                                                <td><?= $record['Transition_Figure'] ?></td>
                                                <td><?= $record['Transition_Rate'] ?></td>
                                                <td  colspan='2'><?= $record['Age_Group_12To15'] ?></td>
                                                <td><?= $record['NAT_Score'] ?></td>
                                                <td colspan="2">
                                                    <div class="button-group">
                                                        <button class='buttonEdit' 
                                                            onclick="editRecord(
                                                            <?= $record['Performance_ID'] ?>, 
                                                                <?= $record['School_Year_ID'] ?>,
                                                            
                                                                '<?= htmlspecialchars($record['Grade_Level'], ENT_QUOTES) ?>',
                                                                '<?= htmlspecialchars($record['Enroll_Gross'], ENT_QUOTES) ?>',
                                                                '<?= htmlspecialchars($record['Enroll_Net'], ENT_QUOTES) ?>',
                                                                '<?= htmlspecialchars($record['Cohort_Figure'], ENT_QUOTES) ?>',
                                                                '<?= htmlspecialchars($record['Cohort_Rate'], ENT_QUOTES) ?>',
                                                                '<?= htmlspecialchars($record['Dropout_Figure'], ENT_QUOTES) ?>',
                                                                '<?= htmlspecialchars($record['Dropout_Rate'], ENT_QUOTES) ?>',
                                                                '<?= htmlspecialchars($record['Repeaters_Figure'], ENT_QUOTES) ?>',
                                                                '<?= htmlspecialchars($record['Repeaters_Rate'], ENT_QUOTES) ?>',
                                                                '<?= htmlspecialchars($record['Promotion_Figure'], ENT_QUOTES) ?>',
                                                                '<?= htmlspecialchars($record['Promotion_Rate'], ENT_QUOTES) ?>',
                                                                '<?= htmlspecialchars($record['Transition_Figure'], ENT_QUOTES) ?>',
                                                                '<?= htmlspecialchars($record['Transition_Rate'], ENT_QUOTES) ?>',
                                                                '<?= htmlspecialchars($record['Age_Group_12To15'], ENT_QUOTES) ?>',
                                                                '<?= htmlspecialchars($record['NAT_Score'], ENT_QUOTES) ?>'
                                                            )"><i class="fas fa-edit"></i></button>
                                                        <button class='buttonDelete' onclick="deleteRecord(<?= $record['Performance_ID'] ?>)"><i class="fas fa-trash-alt"></i></button>
                                                    </div>
                                               
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="17" class="text-center">No records found for this school year.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="1">Grade</th>
                                    <th colspan="2">Enrollment</th>
                                    <th colspan="2">Cohort</th>
                                    <th colspan="2">Drop/School Leavers</th>
                                    <th colspan="2">Repeaters</th>
                                    <th colspan="2">Graduation</th>
                                    <th colspan="2">Transition</th>
                                    <th colspan="2">Age</th>
                                    <th rowspan="2">NAT</th>
                                    <th rowspan="2" class="action-column">Action</th>
                                </tr>
                                <tr>
                                    <th>Level</th>
                                    <th>Gross</th>
                                    <th>Net</th>
                                    <th>Figure</th>
                                    <th>Rate</th>
                                    <th>Figure</th>
                                    <th>Rate</th>
                                    <th>Figure</th>
                                    <th>Rate</th>
                                    <th>Figure</th>
                                    <th>Rate</th>
                                    <th>Figure</th>
                                    <th>Rate</th>
                                    <th>12-15</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="17" class="text-center">No records available.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

        </main>
    </section>

    <!-- Modal for Adding a Record -->
    <div class="modal fade" id="addRecordModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl-custom" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addRecordForm">
                        <!-- School Year and Grade -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="school_year" class ="formlabel">School Year</label>
                                    <select class="form-control" id="school_year" name="school_year"></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="grade" class ="formlabel">Grade</label>
                                    <select class="form-control" id="grade" name="grade"></select>
                                </div>
                            </div>
                        </div>
                        <!-- Other Inputs in Rows of 2 -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="enrollment_gross" class ="formlabel">Enrollment - Gross</label>
                                    <input type="number" class="form-control" id="enrollment_gross" name="enrollment_gross">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="enrollment_net" class ="formlabel">Enrollment - Net</label>
                                    <input type="number" class="form-control" id="enrollment_net" name="enrollment_net">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cohort_figure" class ="formlabel">Cohort - Figure</label>
                                    <input type="number" class="form-control" id="cohort_figure" name="cohort_figure">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cohort_rate" class ="formlabel">Cohort - Rate</label>
                                    <input type="number" step="0.01" class="form-control" id="cohort_rate" name="cohort_rate">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="drop_figure" class ="formlabel">Drop/School Leavers - Figure</label>
                                    <input type="number" class="form-control" id="drop_figure" name="drop_figure">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="drop_rate" class ="formlabel">Drop/School Leavers - Rate</label>
                                    <input type="number" step="0.01" class="form-control" id="drop_rate" name="drop_rate">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="repeaters_figure" class ="formlabel">Repeaters - Figure</label>
                                    <input type="number" class="form-control" id="repeaters_figure" name="repeaters_figure">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="repeaters_rate" class ="formlabel">Repeaters - Rate</label>
                                    <input type="number" step="0.01" class="form-control" id="repeaters_rate" name="repeaters_rate">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="graduation_figure" class ="formlabel">Graduation - Figure</label>
                                    <input type="number" class="form-control" id="graduation_figure" name="graduation_figure">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="graduation_rate" class ="formlabel">Graduation - Rate</label>
                                    <input type="number" step="0.01" class="form-control" id="graduation_rate" name="graduation_rate">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="transition_figure" class ="formlabel">Transition - Figure</label>
                                    <input type="number" class="form-control" id="transition_figure" name="transition_figure">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="transition_rate" class ="formlabel">Transition - Rate</label>
                                    <input type="number" step="0.01" class="form-control" id="transition_rate" name="transition_rate">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="age" class ="formlabel">Age 12-15</label>
                                    <input type="number" class="form-control" id="age" name="age">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nat_score" class ="formlabel">NAT</label>
                                    <input type="number" step="0.01" class="form-control" id="nat_score" name="nat_score">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Record</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



        <!-- Modal for Updating a Record -->
    <div class="modal fade" id="updateRecordModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" action="update_records.php" method="POST">
                        <!-- Hidden Inputs for IDs -->
                        <input type="hidden" id="editPerformanceID" name="performanceID">
                        <input type="hidden" id="editSchoolYearID" name="schoolYearID">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="editGradeLevel">Grade Level</label>
                                <input type="number" id="editGradeLevel" name="Grade_Level" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editEnrollGross">Enroll Gross</label>
                                <input type="number" id="editEnrollGross" name="Enroll_Gross" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="editEnrollNet">Enroll Net</label>
                                <input type="number" id="editEnrollNet" name="Enroll_Net" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editCohortFigure">Cohort Figure</label>
                                <input type="number" id="editCohortFigure" name="Cohort_Figure" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="editCohortRate">Cohort Rate</label>
                                <input type="number" step="0.01" id="editCohortRate" name="Cohort_Rate" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editDropoutFigure">Dropout Figure</label>
                                <input type="number" id="editDropoutFigure" name="Dropout_Figure" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="editDropoutRate">Dropout Rate</label>
                                <input type="number" step="0.01" id="editDropoutRate" name="Dropout_Rate" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editRepeatersFigure">Repeaters Figure</label>
                                <input type="number" id="editRepeatersFigure" name="Repeaters_Figure" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="editRepeatersRate">Repeaters Rate</label>
                                <input type="number" step="0.01" id="editRepeatersRate" name="Repeaters_Rate" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editPromotionFigure">Promotion Figure</label>
                                <input type="number" id="editPromotionFigure" name="Promotion_Figure" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="editPromotionRate">Promotion Rate</label>
                                <input type="number" step="0.01" id="editPromotionRate" name="Promotion_Rate" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editTransitionFigure">Transition Figure</label>
                                <input type="number" id="editTransitionFigure" name="Transition_Figure" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="editTransitionRate">Transition Rate</label>
                                <input type="number" step="0.01" id="editTransitionRate" name="Transition_Rate" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="editAgeGroup">Age Group (12-15)</label>
                                <input type="number" id="editAgeGroup" name="Age_Group_12To15" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="editNATScore">NAT Score</label>
                                <input type="number" step="0.01" id="editNATScore" name="NAT_Score" class="form-control" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('updateForm').style.display='none'">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="assets/js/script.js"></script>

    <script>
        function filterByYear() {
            const selectedYear = document.getElementById("schoolYearFilter").value;
            window.location.href = "?year=" + selectedYear;
        }

    </script>

    <script>
        // Fetch options for dropdowns
        $.get('fetch_options.php', function(data) {
            const options = JSON.parse(data);

            options.schoolYears.forEach(item => {
                $('#school_year').append(`<option value="${item.School_Year_ID}">${item.Year_Range}</option>`);
            });

            options.grades.forEach(item => {
                $('#grade').append(`<option value="${item.Grade_ID}">Grade ${item.Grade_Level}</option>`);
            });
        });

    </script>

    <script>
        // Open the modal and set the grade
        function openAddRecordModal(grade) {
            $('#grade').val('Grade ' + grade);  // Set grade in the modal input field
            $('#addRecordModal').modal('show'); // Show the modal
        }

       // Handle form submission
        $('#addRecordForm').submit(function(e) {
            e.preventDefault();
            
            // Gather form data
            var formData = $(this).serialize();
            
            // Send the data to the server via AJAX
            $.ajax({
                type: 'POST',
                url: 'save_records.php', // Your PHP handler to save the record
                data: formData,
                success: function(response) {
                    // Display a success SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Record Saved!',
                        text: 'The performance record was successfully saved to the database.',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Reload the page to display the new task
                            location.reload();
                        }
                    });

                    // Close the modal after saving
                    $('#addRecordModal').modal('hide');
                    
                    // Optionally, refresh the table or perform other actions here
                    populateTable();
                    location.reload();
                },
                error: function(error) {
                    // Display an error SweetAlert if something goes wrong
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'There was an error saving the record.',
                        confirmButtonText: 'Try Again'
                    });
                }
            });
        });

    </script>

    <script>
        // Function to calculate and update the drop rate
        function calculateDropRate() {
            const enrollmentGross = parseFloat(document.getElementById('enrollment_gross').value) || 0;
            const dropFigure = parseFloat(document.getElementById('drop_figure').value) || 0;

            if (enrollmentGross > 0) {
                const dropRate = (dropFigure / enrollmentGross) * 100;
                document.getElementById('drop_rate').value = dropRate.toFixed(2);
            } else {
                document.getElementById('drop_rate').value = '';
            }
        }

        // Function to calculate and update the graduation rate
        function calculateGraduationRate() {
            const enrollmentGross = parseFloat(document.getElementById('enrollment_gross').value) || 0;
            const graduationFigure = parseFloat(document.getElementById('graduation_figure').value) || 0;

            if (enrollmentGross > 0) {
                const graduationRate = (graduationFigure / enrollmentGross) * 100;
                document.getElementById('graduation_rate').value = graduationRate.toFixed(2);
            } else {
                document.getElementById('graduation_rate').value = '';
            }
        }

        // Event listeners for input changes
        document.getElementById('enrollment_gross').addEventListener('input', () => {
            calculateDropRate();
            calculateGraduationRate();
        });
        document.getElementById('drop_figure').addEventListener('input', calculateDropRate);
        document.getElementById('graduation_figure').addEventListener('input', calculateGraduationRate);
    </script>

    <script>
        // Function to populate and display the modal for editing a record
        function editRecord(performanceID, schoolYearId, gradeLevel, enrollGross, enrollNet, cohortFigure, cohortRate, dropoutFigure,
            dropoutRate, repeatersFigure, repeatersRate, promotionFigure, promotionRate, transitionFigure,
            transitionRate, ageGroup, natScore) {
            
            // Populate form fields with the data from the record
            const formFields = {
                'editPerformanceID': performanceID,
                'editSchoolYearID': schoolYearId,
                'editGradeLevel': gradeLevel,
                'editEnrollGross': enrollGross,
                'editEnrollNet': enrollNet,
                'editCohortFigure': cohortFigure,
                'editCohortRate': cohortRate,
                'editDropoutFigure': dropoutFigure,
                'editDropoutRate': dropoutRate,
                'editRepeatersFigure': repeatersFigure,
                'editRepeatersRate': repeatersRate,
                'editPromotionFigure': promotionFigure,
                'editPromotionRate': promotionRate,
                'editTransitionFigure': transitionFigure,
                'editTransitionRate': transitionRate,
                'editAgeGroup': ageGroup,
                'editNATScore': natScore
            };

            for (let fieldId in formFields) {
                document.getElementById(fieldId).value = formFields[fieldId];
            }

            // Show the modal
            const modal = document.getElementById('updateRecordModal');
            modal.setAttribute('aria-hidden', 'false');
            $('#updateRecordModal').modal('show');
        }

        // Accessibility: Ensure modal accessibility attributes are managed
        document.getElementById('updateRecordModal').addEventListener('shown.bs.modal', function () {
            this.setAttribute('aria-hidden', 'false');
            this.querySelector('input, select, textarea').focus(); // Set focus on the first field
        });

        document.getElementById('updateRecordModal').addEventListener('hidden.bs.modal', function () {
            this.setAttribute('aria-hidden', 'true');
        });

        // Handle form submission using Fetch API
        document.getElementById('updateForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);

            // Send the data to the server
            fetch('update_records.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // If successful, close modal and refresh the table or provide feedback
                    $('#updateRecordModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Record updated successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload(); // Reload the page or refresh the data table
                    });
                } else {
                    // Show error message if update failed
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Failed to update the record',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'An unexpected error occurred. Please try again later.',
                });
            });
        });


        function deleteRecord(performanceId) {
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
                    fetch('delete_records.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ Performance_ID: performanceId }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to update the table
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message,
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: "An error occurred while deleting the record.",
                        });
                    });
                }
            });
        }

    </script>


</body>
</html>
