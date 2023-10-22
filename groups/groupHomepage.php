<?php
    include_once "../protected/ensureLoggedIn.php";
    include_once "../protected/connSql.php";

    if ($_SESSION["role"] == 0)
    {

        $stmt = $conn->prepare("SELECT groups.name, groups.description, groups.photo, users.name FROM `groups`
        JOIN linkUserGroup
        ON linkUserGroup.userId = ? AND linkUserGroup.groupId = ?
        WHERE groups.id = ?;");

        // Fat SQL statement query. Gets the name of the group, description, and group photo. 
        // Second join ensures that the user is actually a part of that group, by checking the junction table.
        // If the user is part of the group, it returns the row of data. If not, then nothing gets returned, which is caught by the if statement on fetch below.

        $stmt->bind_param("iii", $_SESSION["userId"], $groupId, $groupId);

        $stmt->execute();

        $stmt->bind_result($groupName, $groupDescription, $teacherId, $teacherName);

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

    if (!$stmt->fetch()) // if the result is empty, then either this group does not exist, or the user is not a member of this group. Show them 404 in this case.
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
    <h1 style="color:black;"><?=$groupName?></h1>
    <p style="color:black;">Instructor: <?=$teacherName?></p>
    <p style="color:black;"><?=$groupDescription?></p>
    
    
</body>