<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input data

    $email = $_POST["email"];
    $password = $_POST["password"];


    $servername = "localhost";
    $dbname = "backendDatabase";
    $dbuser = "admin";
    $dbpass = "password"; // i know this looks bad, and looks unsecure and stuff. i dont care right now. sql cant be accessed from the internet anyways.
    
    $conn = new mysqli($servername, $dbuser, $dbpass, $dbname, 3307);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);

    $stmt->execute();

    $stmt->bind_result($userId, $userName, $column3, $userHashedPass, $userRole /* and so on for all columns */);

    // Fetch and process the results
    if (!$stmt->fetch()) {
        echo "Error: User with email '$email' does not exist.";
    } 
    else 
    {
        //$hashedPass = password_hash($password, PASSWORD_DEFAULT);
        if (!password_verify($password,$userHashedPass))
        {
            echo "Error: Password does not match.";
        }
        else
        {
            $_SESSION["userId"] = $userId;
            $_SESSION["name"] = $userName; 
            $_SESSION["email"] = $email; 
            $_SESSION["role"] = $userRole; 
            header("Location: /dashboard");
        }
    }






    /*// Replace this with our actual user authentication logic (e.g., database query)
    if ($username === "user" && $password === "password") {
        // Authentication successful; set a session variable and redirect to the dashboard
        $_SESSION["username"] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }*/
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        /* CSS styles for the login page */
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
        <h2>Login</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <input type="submit" value="Login">
            </div>
        </form>
        <p>Don't have an account? <a href="/signup">Sign up</a></p>
    </div>
</body>
</html>
