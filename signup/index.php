<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get input data
    $email = $_POST["email"];
    $password = $_POST["password"];
    $cPassword = $_POST["cPassword"];
    $name = $_POST["name"];
    $role = $_POST["role"];

    if ($password != $cPassword) {
        echo "Error: Passwords do not match.";
    } else {


        // Validate password strength
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);

        if (!($uppercase || $lowercase) || !$number || strlen($password) < 8) {
            echo 'Error: Password should be at least 8 characters in length, should include at least one uppercase or lowercase letter, and at least one number.';
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
                echo "Error: User already exists.";
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
        <?php include "../signup/style.css"?>
    </style>
</head>

<body>
    <a href="https://front.codes/" class="logo" target="_blank">
        <img src="https://assets.codepen.io/1462889/fcy.png" alt="">
    </a>

    <div class="section">
        <div class="container">
            <div class="row full-height justify-content-center">
                <div class="col-12 text-center align-self-center py-5">
                    <div class="section pb-5 pt-5 pt-sm-2 text-center">
                        <h6 class="mb-0 pb-3"><span>Log In </span><span>Sign Up</span></h6>
                        <input class="checkbox" type="checkbox" id="reg-log" name="reg-log"/>
                        <label for="reg-log"></label>
                        <div class="card-3d-wrap mx-auto">
                            <div class="card-3d-wrapper">
                                <div class="card-back">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <h4 class="mb-4 pb-3">Sign Up</h4>
                                            <div class="form-group">
                                                <input type="text" name="logname" class="form-style" placeholder="Your Full Name" id="logname" autocomplete="off">
                                                <i class="input-icon uil uil-user"></i>
                                            </div>    
                                            <div class="form-group mt-2">
                                                <input type="email" name="logemail" class="form-style" placeholder="Your Email" id="logemail" autocomplete="off">
                                                <i class="input-icon uil uil-at"></i>
                                            </div>    
                                            <div class="form-group mt-2">
                                                <input type="password" name="logpass" class="form-style" placeholder="Your Password" id="logpass" autocomplete="off">
                                                <i class="input-icon uil uil-lock-alt"></i>
                                            </div>
                                            <a href="#" class="btn mt-4" id="toggle-btn">Switch to Log In</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include your other scripts here -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.getElementById('toggle-btn');
            const cardWrapper = document.querySelector('.card-3d-wrapper');

            toggleButton.addEventListener('click', function () {
                cardWrapper.classList.toggle('is-flipped');
            });
        });

    </script>
</body>
</html>