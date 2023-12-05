<?php
session_start(); // Start a new or resume the existing session

include "../protected/connSql.php"; // Include the code to establish a database connection


function generateRandomString($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function isStringUnique($randomString, $conn) {
    $count = 1;
    $query = "SELECT COUNT(*) FROM `classes` WHERE joinCode = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $randomString);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count == 0;
}




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in and has a valid session
    if (isset($_SESSION["userId"])) {
        // Validate and sanitize user inputs
        $className = $_POST["className"];
        $classDescription = $_POST["classDescription"];
        

        $imageContent = null;

        if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) 
        {
            $imagePath = $_FILES["image"]["tmp_name"];
            $imageContent = file_get_contents($imagePath);
        }
        
        // Define the user ID (fetched from the session)
        $userId = $_SESSION["userId"];
        
   
        do {
            $joinCode = generateRandomString(8);
        } 
        while (!isStringUnique($joinCode, $conn));
        

        // Create an entry in the classs table
        $insertQuery = $conn->prepare("INSERT INTO classes (name, teacherId, description, photo, joinCode) VALUES (?, ?, ?, ?, ?)");

        $insertQuery->bind_param("sisss", $className, $userId, $classDescription, $imageContent, $joinCode);
        
        // Check if class creation is successful
        if ($insertQuery->execute()) {
            // Get the ID of the newly created class
            $newClassId = $insertQuery->insert_id;
        
            /*// Create an entry in the linkUserClass table
            $linkInsertQuery = $conn->prepare("INSERT INTO linkUserClass (userId, classId) VALUES (?, ?)");
        
           
        
            $linkInsertQuery->bind_param("ii", $userId, $newClassId);
        
            // Check if the entry creation is successful
            if ($linkInsertQuery->execute()) {
                // Class and linkuserclass entry creation successful, redirect to a success page or back to the class listing
                header("Location: /classes/{$classId}");
            } else {
                // Error handling: Display an error message or redirect to an error page
                echo "Failed to create a linkUserClass entry. Please try again.";
            }
        
            $linkInsertQuery->close(); // Close the prepared statement*/

            header("Location: /classes/{$newClassId}");
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