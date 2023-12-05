<?php

include_once "protected/connSql.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user ID from the POST request
    $userId = $_POST['userId'];

    $stmt = $conn->prepare("SELECT users.name, users.photo, users.bio
        FROM `users`
        WHERE users.id = ?;");

    $stmt->bind_param("i", $userId);

    $stmt->execute();

    $stmt->bind_result($name, $photo, $bio);

    if (!$stmt->fetch()) {
        http_response_code(404);
        include_once("404.html");
        die();
    }

    // Sample data for demonstration purposes
    $userData = array(
        'name' => $name,
        'bio' => $bio,
        'photo' => base64_encode($photo), // Sample image URL
    );

    // Convert the data to JSON
    $jsonData = json_encode($userData);

    // Set the content type to JSON
    header('Content-Type: application/json');

    // Return the JSON data
    echo $jsonData;
}
?>