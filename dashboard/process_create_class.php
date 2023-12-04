<?php
session_start(); // Start a new or resume the existing session

include "../protected/connSql.php"; // Include the code to establish a database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in and has a valid session
    if (isset($_SESSION["userId"])) {
        // Validate and sanitize user inputs
        $className = $_POST["className"];
        $classDescription = $_POST["classDescription"];
        
        // Define the user ID (fetched from the session)
        $userId = $_SESSION["userId"];
        
        // Create an entry in the classs table
        $insertQuery = $conn->prepare("INSERT INTO classses (name, teacherId, description) VALUES (?, ?, ?)");
        
        $insertQuery->bind_param("sis", $className, $userId, $classDescription);
        
        // Check if class creation is successful
        if ($insertQuery->execute()) {
            // Get the ID of the newly created class
            $newClassId = $insertQuery->insert_id;
        
            // Create an entry in the linkuserclass table
            $linkInsertQuery = $conn->prepare("INSERT INTO linkuserclass (userId, classId) VALUES (?, ?)");
        
       
        
            $linkInsertQuery->bind_param("ii", $userId, $newClassId);
        
            // Check if the entry creation is successful
            if ($linkInsertQuery->execute()) {
                // Class and linkuserclass entry creation successful, redirect to a success page or back to the class listing
                header("Location: /classes/{$classId}");
            } else {
                // Error handling: Display an error message or redirect to an error page
                echo "Failed to create a linkuserclass entry. Please try again.";
            }
        
            $linkInsertQuery->close(); // Close the prepared statement
        } else {
            // Error handling: Display an error message or redirect to an error page
            echo "Class creation failed. Please try again.";
        }
        
        $insertQuery->close(); // Close the prepared statement
    } else {
        // User is not logged in, handle the error
        echo "Error: User ID is missing. Please log in and try again.";
    }
} else {
    // Handle an invalid request (not a POST request)
    echo "Invalid request method. Please use the form to create a class.";
}

$conn->close(); // Close the database connection

?>