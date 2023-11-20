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
        <?php include "style.css"?>
    </style>
</head>
<body>
  <div class="login-box">
    <h2>Login</h2>
    <form method="POST" action="">
      <div class="user-box">
        <input type="text" name="email" required>
        <label>Email</label>
      </div>
      <div class="user-box">
        <input type="password" name="password" required>
        <label>Password</label>
      </div>
      <a href="#">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
        <button type="submit">Submit</button>
      </a>
    </form>
  </div>
</body>

