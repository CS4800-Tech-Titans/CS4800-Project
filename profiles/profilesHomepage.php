<?php
    include_once "../protected/ensureLoggedIn.php";
    include_once "../protected/connSql.php";

    if ($_SESSION["role"] == 0)
    {

        $stmt = $conn->prepare("SELECT classes.name, classes.description, classes.teacherId, users.name FROM `classes`
        JOIN users 
        ON classes.teacherId = users.id
        JOIN linkUserClass
        ON linkUserClass.userId = ? AND linkUserClass.classId = ?
        WHERE classes.id = ?;");

        // Fat SQL statement query. Gets the name of the class, description, and info on the teacher. First join adds the teacher description to the result. 
        // Second join ensures that the user is actually a part of that class, by checking the junction table.
        // If the user is part of the class, it returns the row of data. If not, then nothing gets returned, which is caught by the if statement on fetch below.

        $stmt->bind_param("iii", $_SESSION["userId"], $classId, $classId);

        $stmt->execute();

        $stmt->bind_result($className, $classDescription, $teacherId, $teacherName);

    }
    else if ( $_SESSION["role"] == 1)
    {
        $stmt = $conn->prepare("SELECT classes.name, classes.description FROM `classes` WHERE classes.id = ? AND classes.teacherId = ?;");

        // Simpler SQL query. Gets name of class and description. If the class in question doesnt actually belong to this teacher, nothing is returned. 
        $stmt->bind_param("ii", $classId, $_SESSION["userId"]);

        $stmt->execute();

        $stmt->bind_result($className, $classDescription);
        $teacherId = $_SESSION["userId"];
        $teacherName = $_SESSION["name"];
    }

    if (!$stmt->fetch()) // if the result is empty, then either this class does not exist, or the user is not a member of this class. Show them 404 in this case.
    {
        http_response_code(404); 
        include_once("404.html");
        die();
    }

?>

<!--<link rel="stylesheet" type="text/css" href="style.css">-->

<style>
    <?php include "style.css"?>
</style>

<body translate="no">
    <h1 style="color:black;"><?=$className?></h1>
    <p style="color:black;">Instructor: <?=$teacherName?></p>
    <p style="color:black;"><?=$classDescription?></p>
    
    
</body>