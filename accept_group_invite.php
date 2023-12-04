<?php
session_start(); // Start a new or resume the existing session

include "protected/connSql.php"; // Include the code to establish a database connection


// Print to the console for debugging purposes
error_log("I am in join_group");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in and has a valid session
    if (isset($_SESSION["userId"])) {
        // Validate and sanitize user inputs
        $groupId = $_POST["groupId"];
        $inviteId = $_POST["inviteId"];

        // Define the user ID (fetched from the session)
        $userId = $_SESSION["userId"];
        
        // TODO: Replace this with the actual user role
        $role = 1;

        // Create an entry in the linkusergroup table
        $linkInsertQuery = $conn->prepare("INSERT INTO linkUserGroup (userId, groupId, role) VALUES (?, ?, ?)");

        $linkInsertQuery->bind_param("iii", $userId, $groupId, $role);

        // Check if the entry creation is successful
        if ($linkInsertQuery->execute()) 
        {
            $inviteDeleteQuery = $conn->prepare("
                DELETE FROM `groupInvite`
                WHERE id = ?;"
            );
            $inviteDeleteQuery->bind_param("i", $inviteId);

            $inviteDeleteQuery->execute();

            // Linkusergroup entry creation successful, redirect to a success page or back to the group listing
            header("Location: /classes/{$classId}");
        } else {
            // Error handling: Display an error message or redirect to an error page
            echo "Failed to create a linkusergroup entry. Please try again.";
        }

        $linkInsertQuery->close(); // Close the prepared statement
    } else {
        // User is not logged in, handle the error
        echo "Error: User ID is missing. Please log in and try again.";
    }
} else {
    // Handle an invalid request (not a POST request)
    echo "Invalid request method. Please use the form to join a group.";
}

$conn->close(); // Close the database connection
?>
