<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$errorMessage = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get input data
    $email = $_POST["email"];
    $password = $_POST["password"];
    $cPassword = $_POST["cPassword"];
    $name = $_POST["name"];
    $role = $_POST["role"];


    if ($password != $cPassword) {
        $errorMessage = "Error: Passwords do not match.";
    } else {


        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);

        if (!($uppercase || $lowercase) || !$number || strlen($password) < 8) {
            $errorMessage = 'Error: Password should be at least 8 characters in length, should include at least one uppercase or lowercase letter, and at least one number.';
        } else {
            // Strong enough password
            $hashedPass = password_hash($password, PASSWORD_DEFAULT);


            $servername = "localhost";
            $dbname = "backendDatabase";
            $dbuser = "admin";
            $dbpass = "password"; // i know this looks bad, and looks unsecure and stuff. i dont care right now. sql cant be accessed from the internet anyways.
            
            $conn = new mysqli($servername, $dbuser, $dbpass, $dbname, 3307);

            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);

            $stmt->execute();

            $stmt->bind_result($userId);//, $column2, $column3, $column4, $column /* and so on for all columns */);

            // Fetch and process the results
            if ($stmt->fetch()) {
                $errorMessage = "Error: User with email ".$email." already exists.";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (email, name, password, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $email, $name, $hashedPass, $role);
                $stmt->execute();

                //echo "You have successfully signed up.\n";
                header("Location: /dashboard");
                $_SESSION["userId"] = $conn->insert_id;//$userId;
                $_SESSION["name"] = $name; 
                $_SESSION["email"] = $email; 
                $_SESSION["role"] = $role; 
                //echo password_hash($password, PASSWORD_DEFAULT);
            }

        }

    }

}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    //echo "lol";
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Sign Up</title>
    <style>
        /* CSS styles for the login page */
        * {
            box-sizing: border-box;
        }
        body {

            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-size: 18px;
        }

        .container {
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            width: 30%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .error-message
        {
            color: red;
            font-weight: bold;
            font-size: 18px;
            margin: 15px;
        }

        h1 {
            color: #333;
            font-size: 32px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            width: 90%;
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
            width: 24%;
        }

        a {
            text-decoration: underline;
            color: #333;
        }


    </style>
</head>

<body>
    <div class="container">
        <h1>Create New Account</h1>
        
        <form class = "form" method="POST" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="cPassword">Confirm Password:</label>
                <input type="password" id="cPassword" name="cPassword" required>
            </div>

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>


            <div class="form-group">
                <label for="role">Account Type:</label>
                <label>
                    <input type="radio" name="role" value="0" required> Student
                </label>
                <label>
                    <input type="radio" name="role" value="1" required> Teacher
                </label>
            </div>

            <div class="form-group">
                <input type="submit" value="Sign Up">
            </div>
            
            <?php 
                if ($errorMessage)
                    echo '<div class="error-message">'.$errorMessage.'</div>';
            ?>

        </form>
        <p>Already have an account? <a href="/login">Log In</a></p>
    </div>
</body>

</html>