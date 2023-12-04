<?php

include_once "protected/ensureLoggedIn.php";
include_once "protected/connSql.php";

$roleStr = "";
if (isset($_SESSION["role"])) {
    if ($_SESSION["role"] == 0) {
        $roleStr = "Student";
    } elseif ($_SESSION["role"] == 1) {
        $roleStr = "Teacher";
    }
    
    #echo $_SERVER["REQUEST_URI"];

    $lastSegment = basename($_SERVER["REQUEST_URI"]);
    $segments = explode('/', $lastSegment);
    $joinCode = end($segments);

    # echo $joinCode;

    $classQuery = $conn->prepare("SELECT id, name, teacherId, description, photo, joinCode FROM classes WHERE joinCode = ?;");
    
    $classQuery->bind_param("s", $joinCode);

    $classQuery->execute();

    $classQuery->bind_result($classId, $className, $teacherId, $classDescription, $classPhoto, $joinCode);

    if (!$classQuery->fetch())
    {
        http_response_code(404); 
        include_once("404.html");
        die();
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
    <?php include "sidebar.html"; ?>

    <div class="container">
        <h2>Welcome, <?php echo isset($_SESSION["name"]) ? $_SESSION["name"] : 'Guest'; ?>!</h2>
        <p>Would you like to join the class <b><?=$className?></b></p>

        <div class="button-group">
            <button class="yes" onclick="joinClass('yes')">Yes</button>
            <button class="no" onclick="joinClass('no')">No</button>
        </div>
    </div>

    <script>
        function joinClass(choice) {
            if (choice === 'yes') {
                <?php
                    // Echo the userId into the JavaScript code
                    echo "var userId = " . json_encode($_SESSION["userId"]) . ";";
                ?>
                // Extract classId from the URL
                /*var currentPageUrl = window.location.href;
                var parts = currentPageUrl.split('/');
                var classId = parts[parts.length - 1];*/
                var classId = <?=$classId?>
                
                // Call process_join_class.php directly using AJAX
                const xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Handle the response, if needed
                        // console.log('Join class response:', xhr.responseText);
                        // Refresh the page after the join is successful
                        //location.reload();
                        window.location.href = "/classes/" + classId;
                    }
                };
                
                // Define the parameters to send to join_group.php
                const params = `classId=${classId}&userId=${userId}`;
                
                xhr.open('POST', '/dashboard/process_join_class.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                
                
                // Ensure classId is a valid integer
                if (!isNaN(classId)) {
                    // Display the alert message with the joined classId
                    
                    xhr.send(params);
                } else {
                    alert('Invalid classId.');
                }
            } else {
                // Implement the action for not joining the class
                //alert('You clicked No. Implement the action for not joining the class here.');
                window.location.href = "/dashboard"
            }
        }
    </script>
</body>
</html>