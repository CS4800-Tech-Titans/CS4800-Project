<?php
session_start(); // Start a new or resume the existing session

$roleStr = "";
if (isset($_SESSION["role"])) {
    if ($_SESSION["role"] == 0) {
        $roleStr = "Student";
    } elseif ($_SESSION["role"] == 1) {
        $roleStr = "Teacher";
    }
} else {
    // Redirect or handle the case where the role is not set in the session
    header("Location: /login"); // Redirect to the login page if the role is not set
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Class</title>
    <style>
        /* CSS styles for the join class page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0; /* Reset default margin */
        }

        .container {
            text-align: center;
            margin-top: 100px;
        }

        h2 {
            color: #333;
        }

        p {
            margin-bottom: 20px;
        }

        /* Style for the buttons */
        .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .button-group button {
            padding: 10px;
            cursor: pointer;
        }

        .button-group button.yes {
            background-color: #4CAF50;
            color: white;
            border: none;
        }

        .button-group button.no {
            background-color: #f44336;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <?php include "../sidebar.html"; ?>

    <div class="container">
        <h2>Welcome, <?php echo isset($_SESSION["name"]) ? $_SESSION["name"] : 'Guest'; ?>!</h2>
        <p>Would you like to join this class?</p>

        <div class="button-group">
            <button class="yes" onclick="joinClass('yes')">Yes</button>
            <button class="no" onclick="joinClass('no')">No</button>
        </div>
    </div>

    <script>
        function joinClass(choice) {
            if (choice === 'yes') {
                // Extract classId from the URL
                var currentPageUrl = window.location.href;
                var parts = currentPageUrl.split('/');
                var classId = parts[parts.length - 1];

                // Ensure classId is a valid integer
                if (!isNaN(classId)) {
                    // Display the alert message with the joined classId
                    alert('You joined class ' + classId);
                } else {
                    alert('Invalid classId.');
                }
            } else {
                // Implement the action for not joining the class
                alert('You clicked No. Implement the action for not joining the class here.');
            }
        }
    </script>
</body>
</html>
