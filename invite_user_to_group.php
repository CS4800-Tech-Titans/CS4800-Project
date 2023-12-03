<?php
session_start(); // Start a new or resume the existing session

include "protected/connSql.php"; // Include the code to establish a database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in and has a valid session
    if (isset($_SESSION["userId"])) {
        // Validate and sanitize user inputs
        $groupId = $_POST["groupId"];

        // Define the user ID (fetched from the session)
        $senderUserId = $_SESSION["userId"];
        
        $receiverUserId = $_POST["receiverUserId"];

        $message = $_POST["message"];

        // Create an entry in the groupInvite table
        $query = $conn->prepare("INSERT INTO groupInvite (senderUserId, receiverUserId, groupId, message) VALUES (?, ?, ?, ?)");

        $query->bind_param("iiis", $senderUserId, $receiverUserId, $groupId, $message);

        // Check if the entry creation is successful
        if ($query->execute()) {
            // Linkusergroup entry creation successful, redirect to a success page or back to the group listing
            header("Location: /classes/{$classId}");
        } else {
            // Error handling: Display an error message or redirect to an error page
            echo "Failed to create a groupInvite entry. Please try again.";
        }

        $linkInsertQuery->close(); // Close the prepared statement
    } else {
        // User is not logged in, handle the error
        echo "Error: User ID is missing. Please log in and try again.";
    }
} else {
    // Handle an invalid request (not a POST request)
    echo "Invalid request method. Please use the form to invite a user to a group.";
}

$conn->close(); // Close the database connection
?>