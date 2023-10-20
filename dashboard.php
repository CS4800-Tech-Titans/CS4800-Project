<?php
session_start();
if (!isset($_SESSION["userId"])) {
    header("Location: login.php"); // Redirect to the login page if not authenticated
    exit();
}
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
        <p>This is the dashboard. You are now authenticated.</p>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
