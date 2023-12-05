<?php

include_once "protected/ensureLoggedIn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user ID from the POST request
    $userId = $_SESSION["userId"];

    $imageContent = null;

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) 
    {
        $imagePath = $_FILES["image"]["tmp_name"];
        $imageContent = file_get_contents($imagePath);
    }

    $bio = $_POST["bio"];

    include "protected/connSql.php";
    

    if (isset($imageContent))
    {
        $stmt = $conn->prepare("UPDATE `users`
        SET photo = ?, bio = ?
        WHERE id = ?;");

        $stmt->bind_param("ssi", $imageContent, $bio, $userId);
    }
    else
    {
        $stmt = $conn->prepare("UPDATE `users`
        SET bio = ?
        WHERE id = ?;");

        $stmt->bind_param("si",$bio, $userId);
    }

    header('Location: /dashboard');

    # !!! THIS LINE REQUIRES "max_allowed_packet=32M" OR SOMETHING SIMILAR IN my.ini FILE FOR MYSQL CONFIG. MYSQL DEFAULTS TO ONLY 1MB MAX PACKET SIZE. for photos
    $stmt->execute();

    $stmt->close();
}
?>