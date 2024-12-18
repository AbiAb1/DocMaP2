<nav>
    <i class='bx bx-menu toggle-sidebar'></i>
    <form action="#">
        <!-- Your form content here if needed -->
    </form>
    <div class="profile">
        <?php
        // Ensure session is started at the beginning of the script if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Include database connection
        include 'connection.php';

        // Check if user is logged in
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];

            // Query to fetch profile image filename and user name
            $sql = "SELECT Profile, CONCAT(fname, ' ', lname) AS fullname FROM useracc WHERE UserID = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("s", $userId);
                $stmt->execute();
                $stmt->bind_result($profileImage, $fullName);
                $stmt->fetch();
                $stmt->close(); // Close statement after fetching results

                // Build path to profile image
                $profileImagePath = "../img/UserProfile/" . $profileImage;

                // Check if the profile image exists
                if (file_exists($profileImagePath)) {
                    echo "<span class='user-name'>{$fullName}</span>";
                    echo "<img src='{$profileImagePath}' alt='Profile Image' class='profile-image'>";
                } else {
                    // Default image if profile image not found
                    echo "<span class='user-name'>{$fullName}</span>";
                    echo "<img src='default_profile_image.jpg' alt='Profile Image' class='profile-image'>";
                }
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        } else {
            echo "User not logged in.";
        }

        // Close database connection at the end of the script
        $conn->close();
        ?>
        <ul class="profile-link">
            <li><a href="profile.php"><i class='bx bxs-user-circle icon'></i> Profile</a></li>
			<li><a href="settings.php"><i class='bx bxs-cog'></i> General</a></li>
            
            <li><a href="logout.php" id="logout"><i class='bx bxs-log-out-circle'></i> Logout</a></li>
        </ul>
    </div>
</nav>

<style>
    .profile {
        display: flex;
        align-items: center;
    }
    .user-name {
        margin-right: 10px; /* Adjust the space between the name and image */
        font-weight: bold;
        color: #9B2035; /* Underline color */
    }
    .profile-image {
        width: 40px; /* Adjust the width as needed */
        height: 40px; /* Adjust the height as needed */
        border-radius: 50%; /* Make the image round */
    }
    .nav-link {
    	position: relative; /* Ensure the dropdown is positioned relative to this parent */
    }

    .dropdown-menu {
    	display: none; /* Hidden by default */
	position: absolute;
    	top: 100%; /* Position dropdown below the parent */
    	right: 0; /* Align dropdown to the right edge of the parent */
    	background-color: white;
    	box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    	width: 430px; /* Adjust the width as needed */
    	z-index: 1000; /* Ensure it appears above other content */
    	border-radius: 10px; /* Add rounded corners */
    	overflow: hidden; /* Ensure contents stay within rounded corners */
    	padding: 10px; /* Add padding to the dropdown menu */
     }
</style>


