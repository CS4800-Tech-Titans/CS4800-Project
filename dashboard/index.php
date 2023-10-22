<?php
include_once "../protected/ensureLoggedIn.php";

$roleStr = "";
if ($_SESSION["role"] == 0 )
    $roleStr = "Student";
else if ($_SESSION["role"] == 1 )
    $roleStr = "Teacher";

?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        /* CSS styles for the dashboard page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        .container {
            text-align: center;
            margin-top: 100px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #333;
        }

        a {
            text-decoration: none;
            color: #333;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $_SESSION["name"]; ?>!</h2>
        <p>This is the dashboard. You are now authenticated. You are a <?=$roleStr?>.</p>
        <a href="/classes">Go to my classes</a>
        <p><a href="/logout">Logout</a></p>
    </div>
</body>
</html>
