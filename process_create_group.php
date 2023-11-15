<?php
session_start(); // Start a new or resume the existing session

include "protected/connSql.php"; // Include the code to establish a database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in and has a valid session
    if (isset($_SESSION["userId"])) {
        // Validate and sanitize user inputs
        $groupName = $_POST["groupName"];
        $groupDescription = $_POST["groupDescription"];
        
        // Process the uploaded image
        if (isset($_FILES["groupPhoto"]) && $_FILES["groupPhoto"]["error"] === UPLOAD_ERR_OK) {
            $groupPhoto = file_get_contents($_FILES["groupPhoto"]["tmp_name"]); // Get the image data
        } else {
            $groupPhoto = null; // Set to null
        }
        

        // Get the selected class from the dropdown (this works)
        $selectedClass = $_POST["classId"];

        $classId = $selectedClass;

         // Display the selected class information
         echo "<p>Selected Class ID: $selectedClass</p>";

       // $classId = getClassId($selectedClass); // Implement getClassId function (this breaks)

        // Display the selected class information
        echo "<p>Selected Class ID454645: $classId</p>";
        // TODO: Replace this with the actual class ID
        //$classId = 1;
        
        // Create an entry in the groups table
        $insertQuery = $conn->prepare("INSERT INTO groups (name, description, classId, photo) VALUES (?, ?, ?, ?)");
        
        $insertQuery->bind_param("ssib", $groupName, $groupDescription, $classId, $groupPhoto);
        
        // Check if group creation is successful
        if ($insertQuery->execute()) {
            // Get the ID of the newly created group
            $newGroupId = $insertQuery->insert_id;
        
            // Create an entry in the linkusergroup table
            $linkInsertQuery = $conn->prepare("INSERT INTO linkusergroup (userId, groupId, role) VALUES (?, ?, ?)");
        
            // Define the user ID (fetched from the session)
            $userId = $_SESSION["userId"];
        
             // TODO: Replace this with the actual user role
            $role = 1;
        
            $linkInsertQuery->bind_param("iii", $userId, $newGroupId, $role);
        
            // Check if the entry creation is successful
            if ($linkInsertQuery->execute()) {
                // Group and linkusergroup entry creation successful, redirect to a success page or back to the group listing
                header("Location: /classes/{$classId}");
            } else {
                // Error handling: Display an error message or redirect to an error page
                echo "Failed to create a linkusergroup entry. Please try again.";
            }
        
            $linkInsertQuery->close(); // Close the prepared statement
        } else {
            // Error handling: Display an error message or redirect to an error page
            echo "Group creation failed. Please try again.";
        }
        
        $insertQuery->close(); // Close the prepared statement
    } else {
        // User is not logged in, handle the error
        echo "Error: User ID is missing. Please log in and try again.";
    }
} else {
    // Handle an invalid request (not a POST request)
    echo "Invalid request method. Please use the form to create a group.";
}

$conn->close(); // Close the database connection

function getClassId($selectedClass) {
    include "./protected/connSql.php";

    // TODO: Replace this query with your actual database query
    $query = $conn->prepare("SELECT classes.id 
                            FROM classes 
                            JOIN linkuserclass ON classes.id = linkuserclass.classId
                            WHERE classes.name = ? AND linkuserclass.userId = ?");
    $query->bind_param("ss", $selectedClass, $_SESSION["userId"]);

    $query->execute();
    $query->bind_result($classId);

    if ($query->fetch()) {
        // Class found, return the classId
        return $classId;
    } else {
        // Class not found, handle this according to your application logic
        // For now, return 0; you might want to throw an exception or handle it differently
        return 2;
    }

    $query->close();
    $conn->close();
}
?>