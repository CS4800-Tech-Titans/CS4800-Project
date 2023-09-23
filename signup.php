<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input data
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Replace this with our actual user registration logic (e.g., database insert)
    // We might also want to hash the password before storing it.
    // Example: $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // After successful registration, set a session variable and redirect to the dashboard
    $_SESSION["username"] = $username;
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <style>
        /* CSS styles for the sign-up page */
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

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
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
        <h2>Sign Up</h2>
        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <input type="submit" value="Sign Up">
            </div>
        </form>
        <p>Already have an account? <a href="index.php">Login</a></p>
    </div>
</body>
</html>
