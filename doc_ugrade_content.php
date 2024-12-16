<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include 'connection.php';

// Initialize variables
$documents = [];
$contentName = ''; 

// Check if GradeLevelFolderID is set in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Sanitize the input to prevent SQL injection
    $userFolderId = intval($_GET['id']); // Use intval to ensure it's an integer

    // Fetch documents from the database based on the GradeLevelFolderID
    $query = "SELECT DocuID, GradeLevelFolderID, UserFolderID, UserID, ContentID, TaskID, name, mimeType, size, uri, Status, TimeStamp 
          FROM documents 
          WHERE UserFolderID = $userFolderId AND (Status = 1 OR Status = 0)";

    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    while ($row = mysqli_fetch_assoc($result)) {
        // Fetch Content Title and Caption based on ContentID
        $contentId = $row['ContentID'];
        $contentQuery = "SELECT Title, Captions FROM feedcontent WHERE ContentID = $contentId";
        $contentResult = mysqli_query($conn, $contentQuery);
        if (!$contentResult) {
            die("Query failed: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($contentResult) > 0) {
            $contentRow = mysqli_fetch_assoc($contentResult);
            $row['DisplayName'] = $contentRow['Title'] . ' - ' . $contentRow['Captions'];
        }
        $documents[] = $row;
    }

    // Fetch the selected folder name based on GradeLevelFolderID
    $contentQuery = "SELECT fc.Title, fc.Captions 
                FROM userfolders gf
                JOIN usercontent uc ON uc.UserContentID = gf.UserContentID
                JOIN feedfontent fc ON fc.ContentID = uc.ContentID
                WHERE gf.UserFolderID = $userFolderId";
    $contentResult = mysqli_query($conn, $contentQuery);
    if (!$contentResult) {
        die("Query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($contentResult) > 0) {
        $contentRow = mysqli_fetch_assoc($contentResult);
        $contentName = $contentRow['Title'] . ' - ' . $contentRow['Captions']; // Set contentName here
    }
} else {
    // Handle case where ID is not provided
    $documents = []; // No documents to display
}

// Function to get the file icon based on the file extension
function getFileIcon($mimeType) {
    $icon = 'fas fa-file'; // Default file icon

    if (strpos($mimeType, 'pdf') !== false) {
        $icon = 'fas fa-file-pdf';
    } elseif (strpos($mimeType, 'word') !== false || strpos($mimeType, 'doc') !== false) {
        $icon = 'fas fa-file-word';
    } elseif (strpos($mimeType, 'excel') !== false) {
        $icon = 'fas fa-file-excel';
    } elseif (strpos($mimeType, 'image') !== false) {
        $icon = 'fas fa-file-image';
    } elseif (strpos($mimeType, 'powerpoint') !== false) {
        $icon = 'fas fa-file-powerpoint';
    }

    return $icon;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="icon" type="image/png" href="img/Logo/docmap-logo-1.png">
    <title>Teacher Document Management</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   
    <style>
        .no-documents-message {
            text-align: center;
            font-size: 1.5rem;
            color: #555;
        }
        .search-filter {
            display: flex;
            justify-content: flex-end;
            margin: 20px 0;
        }
        .search-filter input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            max-width: 300px;
        }
        .document-item {
            background-color: white;
            border-radius: 10px;
            padding: 20px; /* Increase padding to make it wider */
            width: 100%; /* Full width within the grid column */
            display: flex;
            align-items: center; /* Align items vertically */
            text-align: left; /* Align text to the left */
            height: 80px; /* Adjust height if needed */
            transition: box-shadow 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden; /* Hide overflow */
        }

        .document-item:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .document-item .icon {
            font-size: 40px; /* Adjust icon size */
            color: #9B2035; /* Icon color */
            margin-right: 20px; /* Space between icon and text */
        }

        .document-name {
            white-space: nowrap; /* Prevent text wrapping */
            overflow: hidden; /* Hide overflow */
            text-overflow: ellipsis; /* Show ellipsis for overflow */
            max-width: calc(100% - 50px); /* Adjust based on icon size */
            text-decoration: none; /* Remove underline */
            color: black; /* Make text color black */
        }

        .document-name:hover {
            text-decoration: none; /* Ensure no underline on hover */
        }
        .document-item {
            text-decoration: none; /* Remove underline */
            display: flex;
            align-items: center;
            color: inherit; /* Inherit color from parent */
        }

        .document-item:hover {
            text-decoration: none; /* Ensure no underline on hover */
        }
        .filter-container {
    display: flex; /* Use flexbox to align elements */
    align-items: center; /* Center vertically */
    margin: 10px 0; /* Add margin for spacing */
}

.filter-select, .search-input {
    border: none; /* Remove borders */
    border-radius: 4px; /* Add rounded corners */
    padding: 10px; /* Add padding for spacing */
    margin-left: 10px; /* Add space between elements */
    font-size: 16px; /* Increase font size */
    outline: none; /* Remove outline on focus */
}

.filter-select {
    background-color: #ffff; /* Light gray background for select */
    color: #333; /* Dark text color */
    cursor: pointer; /* Change cursor to pointer */
}

.search-input {
    background-color: #fff; /* White background for input */
    color: #333; /* Dark text color */
    width: 200px; /* Set a fixed width for input */
}

.filter-select:hover, .search-input:hover {
    background-color: #ffff; /* Darker gray on hover */
}

.filter-select:focus, .search-input:focus {
    border: 1px solid #007bff; /* Blue border on focus */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Light blue shadow on focus */
}
.document-item {
    position: relative; /* Set the position relative to the document item */
}

.info-icon {
    position: absolute; /* Absolute positioning */
    top: 10px; /* Position from the top */
    right: 10px; /* Position from the right */
    margin-left: auto; /* Push icon to the right */
    cursor: pointer; /* Change cursor to pointer */
    display: none; /* Initially hidden */
    color: grey; /* Color of the icon */
    font-size: 20px; /* Adjust icon size if necessary */
}

.document-item:hover .info-icon {
    display: inline; /* Show icon on hover */
}



    </style>
</head>
<body>
    <section id="sidebar">
        <?php include 'navbar.php'; ?>
    </section>

    <section id="content">
        <?php include 'topbar.php'; ?>

        <main>
        <div class="Title">
            <h1 class="title">
                <a href="doc_ufolder.php" style="text-decoration: none; color: inherit;">My Documents</a>
                <i class="fas fa-angle-right" style="margin: 0 8px; color: #9B2035;"></i> <!-- Right angle icon -->
                <i class="fas fa-folder" style="margin-left: 8px; color: gray;"></i> <!-- Gray folder icon -->
                <?php if (!empty($contentName)) { echo htmlspecialchars($contentName); } ?> <!-- Display Content Title - Captions -->

                
            </h1>
        </div>


            <div class="container">
                <div class="search-filter">
                    
                    
                    <select id="filter-alphabetical" class="filter-select">
                        <option value="">Sort Alphabetically</option>
                        <option value="asc">A-Z</option>
                        <option value="desc">Z-A</option>
                    </select>
                    
                    <select id="filter-date" class="filter-select">
                        <option value="">Sort by Date</option>
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                    </select>

                    <input type="text" placeholder="Search by document name" id="search" style = "margin-left:10px;">
                </div>


                <div class="row">
                    <?php if (!empty($documents)): ?>
                        <?php foreach ($documents as $document): ?>
                            <div class="col-md-4 mb-4">
                                <a href="Documents/<?php echo htmlspecialchars($document['name']); ?>" target="_blank" class="document-item" data-date="<?php echo htmlspecialchars($document['TimeStamp']); ?>">
                                    <i class="<?php echo getFileIcon($document['mimeType']); ?> icon"></i>
                                        <div class="document-name">
                                            <?php 
                                            // Strip the prefix up to the first underscore
                                            $displayName = preg_replace('/^[^_]*_/', '', $document['name']);
                                            echo htmlspecialchars($displayName); 
                                            ?>
                                            <i class="fas fa-info-circle info-icon" data-toggle="modal" data-target="#modal-<?php echo $document['DocuID']; ?>" title="View Details"></i>
                                        </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="no-documents-message">No documents found.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </section>

    <script>
       document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const filterAlphabetical = document.getElementById('filter-alphabetical');
    const filterDate = document.getElementById('filter-date');
    const documentItems = Array.from(document.querySelectorAll('.document-item'));

    searchInput.addEventListener('input', filterDocuments);
    filterAlphabetical.addEventListener('change', filterDocuments);
    filterDate.addEventListener('change', filterDocuments);

    function filterDocuments() {
    const searchTerm = searchInput.value.toLowerCase();
    const alphabeticalSort = filterAlphabetical.value;
    const dateSort = filterDate.value;

    // Filter by search term
    let filteredItems = documentItems.filter(item => {
        const documentName = item.querySelector('.document-name').textContent.toLowerCase();
        return documentName.includes(searchTerm);
    });

    // Sort alphabetically if selected
    if (alphabeticalSort === 'asc') {
        filteredItems.sort((a, b) => {
            const nameA = a.querySelector('.document-name').textContent.toLowerCase().replace(/^[^_]*_/, ''); // Strip prefix
            const nameB = b.querySelector('.document-name').textContent.toLowerCase().replace(/^[^_]*_/, ''); // Strip prefix
            return nameA.localeCompare(nameB);
        });
    } else if (alphabeticalSort === 'desc') {
        filteredItems.sort((a, b) => {
            const nameA = a.querySelector('.document-name').textContent.toLowerCase().replace(/^[^_]*_/, ''); // Strip prefix
            const nameB = b.querySelector('.document-name').textContent.toLowerCase().replace(/^[^_]*_/, ''); // Strip prefix
            return nameB.localeCompare(nameA);
        });
    }

    // Sort by date if selected
    if (dateSort === 'newest') {
        filteredItems.sort((a, b) => {
            const dateA = new Date(a.getAttribute('data-date'));
            const dateB = new Date(b.getAttribute('data-date'));
            return dateB - dateA; // Newest first
        });
    } else if (dateSort === 'oldest') {
        filteredItems.sort((a, b) => {
            const dateA = new Date(a.getAttribute('data-date'));
            const dateB = new Date(b.getAttribute('data-date'));
            return dateA - dateB; // Oldest first
        });
    }

    // Display filtered and sorted items
    documentItems.forEach(item => {
        item.parentElement.style.display = 'none'; // Hide all items first
    });

    filteredItems.forEach(item => {
        item.parentElement.style.display = 'block'; // Show filtered items
    });

    // Handle no documents message
    const noDocumentsMessage = document.querySelector('.no-documents-message');
    noDocumentsMessage.style.display = filteredItems.length === 0 ? 'block' : 'none';
}

});

    </script>

    <script src="assets/js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
