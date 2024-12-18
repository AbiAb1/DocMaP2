<nav>
    <i class='bx bx-menu toggle-sidebar'></i>
    <form action="#">
        <!-- Your form content here if needed -->
    </form>
    <a href="#" class="nav-link">
        <?php
        // Ensure session is started at the beginning of the script if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Include database connection
        include 'connection.php';

        // Check if user is logged in
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        ?>
        <i class='bx bxs-bell icon' id="notification-icon"></i>
        <div class="dropdown-menu" id="notifications-dropdown">
            <h4 style="font-weight:bold;color:#9B2035">Notifications</h4>
            <?php
                // Query to fetch notifications
                $sql = "SELECT ts.NotifID, ts.TaskID, ts.ContentID, ts.UserID, ts.Title, ts.Content, ts.Status, ts.TimeStamp, CONCAT(ua.fname, ' ', ua.lname) as fullname
                        FROM notifications ts
                        INNER JOIN usercontent uc ON ts.ContentID = uc.ContentID
                        INNER JOIN useracc ua ON ts.UserID = ua.UserID
                        WHERE uc.UserID = ? 
                        ORDER BY ts.TimeStamp DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $user_id);
                $stmt->execute();
                $stmt->bind_result($id, $taskID, $contentID, $notifUserID, $title, $content, $status, $timestamp, $fullname);

                echo "<ul class='notifications-list'>";

                while ($stmt->fetch()) {
                    $title_color = $status == 1 ? 'red' : 'gray';
                    $notif_status_class = $status == 1 ? 'new-notification' : '';
                    echo "<li class='notification-item {$notif_status_class}'>";
                    //echo "<a href='tasks.php?content_id={$contentID}' style='text-decoration: none; color: inherit;'>"; 
                    echo "<div class='notif-header'><strong>{$fullname}</strong></div>";
                    echo "<div class='notif-title' style='font-weight:bold;'>{$title}</div>";
                    echo "<div class='notif-content'>{$content}</div>";
                    echo "<div class='notif-timestamp'>{$timestamp}</div>";
                    echo "</li>";
                }
                echo "</ul>";

                $stmt->close(); // Close statement after fetching results
            } else {
                echo "User not logged in.";
            }

            // Close database connection at the end of the script
            $conn->close();
            ?>
        </div>
    </a>
    <a href="#" class="nav-link">
        <i class='bx bxs-notepad icon'></i>
        <!-- Notepad icon content -->
    </a>
    <span class="divider"></span>
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

.notifications-list {
    list-style: none;
    padding: 0;
    margin: 0;
    max-height: 400px; /* Set the maximum height */
    overflow-y: auto; /* Enable vertical scrolling */
}

.notification-item {
    padding: 15px; /* Add padding to each notification item */
    margin-bottom: 10px; /* Add space between notification items */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Add shadow */
    background-color: white; /* Background color */
    position: relative; /* Required for the pseudo-element and timestamp */
    transition: background-color 0.3s, box-shadow 0.3s; /* Smooth transition for hover effect */
    border-style: solid;
    border-width: 1px;
    border-color: grey;
}

.notification-item:hover {
    background-color: #f5f5f5; /* Change background color on hover */
    box-shadow: 0 8px 16px rgba(0,0,0,0.2); /* Intensify shadow on hover */
}

.notif-header {
    font-weight: bold;
}

.notif-title {
    margin-top: 5px;
}

.notif-content {
    margin-top: 5px;
    color: #555;
}

.notif-timestamp {
    position: absolute;
    top: 10px; /* Adjust as needed */
    right: 15px; /* Adjust as needed */
    font-size: 12px;
    color: #999;
}

.new-notification::after {
    content: '';
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    width: 10px;
    height: 10px;
    background-color: #9B2035; /* Dot color */
    border-radius: 50%;
}

</style>

<script>
    document.getElementById('notification-icon').addEventListener('click', function() {
        var dropdown = document.getElementById('notifications-dropdown');
        if (dropdown.style.display === 'none' || dropdown.style.display === '') {
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    });

    // Close the dropdown if clicked outside
    window.onclick = function(event) {
        if (!event.target.matches('.icon')) {
            var dropdown = document.getElementById('notifications-dropdown');
            if (dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
            }
        }
    }
</script>
