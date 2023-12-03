<?php
session_start();

$errorMessage = null;

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
		$errorMessage = "Error: User with email '$email' does not exist.";
	} else {
		//$hashedPass = password_hash($password, PASSWORD_DEFAULT);
		if (!password_verify($password, $userHashedPass)) {
			$errorMessage = "Error: Incorrect password.";
		} else {
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
			width: 25%;
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

		.error-message {
			color: red;
			font-weight: bold;
			font-size: 18px;
			margin: 15px;
		}

		h1 {
			color: #333;
			font-size: 32px;
			margin-bottom: 50px;
		}

		.form-group {
			margin-bottom: 20px;
			width: 90%;
		}

		label {
			display: block;
			font-weight: bold;
			margin-bottom: 5px;
			text-align: left;
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
			margin-top: 30px;
		}

		a {
			text-decoration: underline;
			color: #333;
		}
	</style>
</head>

<body>
	<div class="container">
		<h1>Login</h1>
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
				<input type="submit" value="Login">
			</div>
			
            <?php 
                if ($errorMessage)
                    echo '<div class="error-message">'.$errorMessage.'</div>';
            ?>

		</form>
		<p>Don't have an account? <a href="/signup">Sign up</a></p>
	</div>
</body>

</html>