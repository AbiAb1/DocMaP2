<div href="dash.php" class="brand" style="text-align:center; font-size:38px; margin-top: 15px;">
    <i class="icon">
        <img src="img/Logo/docmap-logo-1.png" alt="Icon" style="max-width: 40px; height: auto;">
    </i>
    <img src="img/Logo/docmap.png" alt="Logo" style="max-width: 150px; height: auto;">
</div>

<ul class="side-menu">
    <li><a href="dash.php" class="active"><i class='bx bxs-dashboard icon'></i> Dashboard</a></li>
    <li class="divider" data-text="main">Main</li>
    <li><a href="subjects.php"><i class='bx bxs-bookmarks icon'></i>All Grades</a></li>
    <li>
        <a href="#" style="background-color:#9B2035;color:#fff;">
            <i class='bx bxs-bookmark icon'></i> Grades
        </a>
        <!-- Search Bar -->
        <input type="text" id="subjectSearch" placeholder="Search Grade..." style="margin: 10px 0; padding: 5px; width: 95%;">

        <!-- Subject List -->
        <ul class="side-dropdown1" id="subjectList">
        <?php
        include 'connection.php';
        $user_id = $_SESSION['user_id']; // Initialize $user_id from session

        // Query to fetch subjects from feedcontent table including ContentColor
        $sql_subjects = "SELECT fs.ContentID, fs.Title, fs.Captions, fs.ContentColor
                        FROM feedcontent fs
                        INNER JOIN usercontent uc ON fs.ContentID = uc.ContentID
                        WHERE uc.UserID = $user_id AND uc.Status=1";
        $result_subjects = mysqli_query($conn, $sql_subjects);

        // Check if subjects are found
        if (mysqli_num_rows($result_subjects) > 0) {
            while ($row_subject = mysqli_fetch_assoc($result_subjects)) {
                $subject_id = $row_subject['ContentID'];
                $subject_name = $row_subject['Title'] . ' - ' . $row_subject['Captions'];
                $content_color = htmlspecialchars($row_subject['ContentColor']); // Fetch the ContentColor

                // Generate initials using the original subject name
                $words = explode(' ', $subject_name);

                // Get the first letter of the first word and the entire second word if it's a number (e.g., "Grade 10")
                $initials = strtoupper(substr($words[0], 0, 1));
                if (isset($words[1]) && is_numeric($words[1])) {
                    $initials .= $words[1]; // Include the full number (e.g., "G10")
                } else {
                    $initials .= strtoupper(substr($words[1], 0, 1)); // Default behavior for non-numeric words
                }

                // Use regex to replace "Grade X" with "GX" for display purposes
                $short_subject_name = preg_replace('/Grade\s+(\d+)/i', 'G$1', $subject_name);

                // Apply the ContentColor dynamically to the background color of the subject link
                echo "<li>";
                echo "<div class='subject-container'>";
                echo "<div class='initial-circle' style='background-color: $content_color;'>$initials</div>"; // Circle with initials
                echo "<a href='tasks.php?content_id=$subject_id' class='subject-name' style='background-color: transparent;'>$short_subject_name</a>";
                echo "</div>";
                echo "</li>";
            }
        } else {
            echo "<li><a href='#'>No subjects found</a></li>";
        }

        // Close database connection
        mysqli_close($conn);
        ?>

        </ul>
    </li>
    
    <li class="divider" data-text="productivity">Productivity</li>
    <li><a href="mps.php"><i class='bx bx-spreadsheet icon'></i>MPS</a></li>
    <li><a href="doc_ufolder.php"><i class='bx bx-file icon'></i>Documents</a></li>

    <?php
        // Include your database connection
        include 'connection.php';

        // Start session to get user details (assuming the user is logged in and their ID is stored in session)
        $user_id = $_SESSION['user_id'];// Assuming UserID is stored in session

        // Query to check if the user is a chairperson
        $sql = "SELECT * FROM chairperson WHERE UserID = ?";

        // Prepare and execute the query
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the user is a chairperson
        $isChairperson = $result->num_rows > 0;
    ?>

    <!-- Only show the "Performance Indicator" tab if the user is a chairperson -->
    <?php if ($isChairperson): ?>
        <li><a href="performance_indicator.php"><i class='bx bx-grid icon'></i></i>Performance Indicator</a></li>
    <?php endif; ?>
    
    <li class="divider" data-text="others">Others</li>
 <li>
        <a href="#"><i class='bx bxs-download icon'></i> Forms <i class='bx bx-chevron-right icon-right'></i></a>
        <ul class="side-dropdown">
            <?php
            include 'connection.php'; // Ensure this file establishes the connection

            // Query to fetch form templates
            $sql_templates = "SELECT TemplateID, name, filename FROM templates";
            $result_templates = mysqli_query($conn, $sql_templates);

            // Check if templates are found
            if (mysqli_num_rows($result_templates) > 0) {
                // Loop through each template and create a list item
                while ($row_template = mysqli_fetch_assoc($result_templates)) {
                    $template_id = $row_template['TemplateID'];
                    $template_name = $row_template['name'];
                    $filename = $row_template['filename']; // Get the filename

                    // Generate the path to the file in the Admin/Templates folder
                    $file_path = "Admin/Templates/" . $filename;

                    // Determine the file extension
                    $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    // Select an icon based on the file type
                    $icon = '';
                    switch ($file_extension) {
                        case 'pdf':
                            $icon = '<i class="bx bxs-file-pdf"></i>'; // PDF icon
                            break;
                        case 'doc':
                        case 'docx':
                            $icon = '<i class="bx bxs-file-doc"></i>'; // Word document icon
                            break;
                        case 'xls':
                        case 'xlsx':
                            $icon = '<i class="bx bxs-file-earmark-excel"></i>'; // Excel file icon
                            break;
                        case 'ppt':
                        case 'pptx':
                            $icon = '<i class="bx bxs-file-presentation"></i>'; // PowerPoint file icon
                            break;
                        case 'txt':
                            $icon = '<i class="bx bxs-file-text"></i>'; // Text file icon
                            break;
                        case 'jpg':
                        case 'jpeg':
                        case 'png':
                        case 'gif':
                            $icon = '<i class="bx bxs-file-image"></i>'; // Image file icon
                            break;
                        default:
                            $icon = '<i class="bx bxs-file"></i>'; // Default file icon
                            break;
                    }

                    // Link to the file when clicked with added spacing
                    echo "<li><a href='$file_path' target='_blank'>$icon <span style='margin-left: 8px;'>$template_name</span></a></li>";
                }
            } else {
                echo "<li><a href='#'>No forms available</a></li>";
            }

            // Close the database connection
            mysqli_close($conn);
            ?>
        </ul>
    </li>
    <li>
        <a href="#"><i class='bx bx-table icon'></i> Archived Subjects <i class='bx bx-chevron-right icon-right'></i></a>
        <ul class="side-dropdown" id="subjectList">
        <?php
        include 'connection.php';
        $user_id = $_SESSION['user_id']; // Initialize $user_id from session

        // Query to fetch subjects from feedcontent table including ContentColor
        $sql_subjects = "SELECT fs.ContentID, fs.Title, fs.Captions, fs.ContentColor
                        FROM feedcontent fs
                        INNER JOIN usercontent uc ON fs.ContentID = uc.ContentID
                        WHERE uc.UserID = $user_id AND uc.Status= 3";
        $result_subjects = mysqli_query($conn, $sql_subjects);

        // Check if subjects are found
        if (mysqli_num_rows($result_subjects) > 0) {
            while ($row_subject = mysqli_fetch_assoc($result_subjects)) {
                $subject_id = $row_subject['ContentID'];
                $subject_name = $row_subject['Title'] . ' - ' . $row_subject['Captions'];
                $content_color = htmlspecialchars($row_subject['ContentColor']); // Fetch the ContentColor

                // Generate initials using the original subject name
                $words = explode(' ', $subject_name);

                // Get the first letter of the first word and the entire second word if it's a number (e.g., "Grade 10")
                $initials = strtoupper(substr($words[0], 0, 1));
                if (isset($words[1]) && is_numeric($words[1])) {
                    $initials .= $words[1]; // Include the full number (e.g., "G10")
                } else {
                    $initials .= strtoupper(substr($words[1], 0, 1)); // Default behavior for non-numeric words
                }

                // Use regex to replace "Grade X" with "GX" for display purposes
                $short_subject_name = preg_replace('/Grade\s+(\d+)/i', 'G$1', $subject_name);

                // Apply the ContentColor dynamically to the background color of the subject link
                echo "<li>";
                echo "<div class='subject-container'>";
                echo "<div class='initial-circle' style='background-color: grey;'>$initials</div>"; // Circle with initials
                echo "<a href='tasks.php?content_id=$subject_id' class='subject-name' style='background-color: transparent; color:grey;'>$short_subject_name</a>";
                echo "</div>";
                echo "</li>";
            }
        } else {
            echo "<li><a href='#'>No subjects found</a></li>";
        }

        // Close database connection
        mysqli_close($conn);
        ?>

        </ul>

    </li>




   


</ul>

<!-- JavaScript for search functionality -->
<script>
    document.getElementById('subjectSearch').addEventListener('keyup', function() {
        var filter = this.value.toLowerCase();
        var subjectContainers = document.querySelectorAll('.subject-container');

        subjectContainers.forEach(function(container) {
            var subjectName = container.querySelector('.subject-name').innerText.toLowerCase();

            if (subjectName.includes(filter)) {
                container.parentElement.style.display = '';
            } else {
                container.parentElement.style.display = 'none';
            }
        });
    });
</script>

<!-- Style for search bar and subject initials -->
<style>
    
    .side-dropdown1 {
        padding-left: 10px;
    }

    .subject-container {
        display: flex;
        align-items: center;
    }

    .subject-container a {
        margin-left: 10px;
        text-decoration: none;
        color: #555;
        font-weight: bold;
    }

    .initial-circle {
        width: 36px;
        height: 36px;
       
        color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50%;
        font-weight: bold;
        font-size: 14px;
        margin-right: 10px;
    }

    #subjectSearch {
        margin: 10px;
        padding: 10px; /* Increased padding for better usability */
        width: calc(100% - 20px); /* Adjust width */
        border: 1px solid #ddd; /* Border color */
        border-radius: 20px; /* Rounded corners */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        font-size: 15px; /* Increase font size for better readability */
        outline: none; /* Remove outline on focus */
        text-align: center; /* Center align text */
    }

    #subjectSearch::placeholder {
        color: #999; /* Placeholder color */
        text-align: center; /* Center the placeholder text */
    }

    #subjectSearch:focus {
        border-color: #9B2035; /* Change border color on focus */
        box-shadow: 0 0 5px rgba(155, 32, 53, 0.5); /* Highlight shadow on focus */
    }
</style>
