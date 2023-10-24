<?php
include "protected/connSql.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize user inputs
    $groupName = $_POST["groupName"];
    $groupDescription = $_POST["groupDescription"];
    
    
    // Process the uploaded image
    if (isset($_FILES["groupPhoto"]) && $_FILES["groupPhoto"]["error"] === UPLOAD_ERR_OK) {
        $groupPhoto = file_get_contents($_FILES["groupPhoto"]["tmp_name"]); // Get the image data
    } else {
        $groupPhoto = null; // Set to null
    }
    
    
    // Insert the new group into the database
    $insertQuery = $conn->prepare("INSERT INTO groups (name, description, classId, photo) VALUES (?, ?, ?, ?)");

    // TODO: figure out how to get id of current class
    $classId = 1;
    //$classId = $_POST["classId"]; // Get the classId from the form data

    //s=string, i=int, b=blob for images
    $insertQuery->bind_param("ssib", $groupName, $groupDescription, $classId, $groupPhoto);

    // Check if creation successful
    if ($insertQuery->execute()) {
        // Group creation successful, redirect to a success page or back to the group listing
        header("Location: /classes/{$classId}");
    } else {
        // Error handling: Display an error message or redirect to an error page
        echo "Group creation failed. Please try again.";
    }

    $insertQuery->close();
} else {
    // Handle invalid request (not a POST request)
    echo "Invalid request method. Please use the form to create a group.";
}

// Close the database connection if necessary
$conn->close();
?>