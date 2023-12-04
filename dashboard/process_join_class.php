
<?php
include_once "../protected/ensureLoggedIn.php";
include_once "../protected/connSql.php";



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in and has a valid session
    if (isset($_SESSION["userId"])) {
        // Validate and sanitize user inputs
        $classId = $_POST["classId"];

        // Define the user ID (fetched from the session)
        $userId = $_SESSION["userId"];
        

        // Create an entry in the linkuserclass table
        $linkInsertQuery = $conn->prepare("INSERT INTO linkuserclass (userId, classId) VALUES (?, ?)");

        $linkInsertQuery->bind_param("ii", $userId, $classId);

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
        // User is not logged in, handle the error
        echo "Error: User ID is missing. Please log in and try again.";
    }
} else {
    // Handle an invalid request (not a POST request)
    echo "Invalid request method. Please use the form to join a class.";
}

$conn->close(); // Close the database connection
?>