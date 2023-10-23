<?php
    include_once "../protected/ensureLoggedIn.php";
    include "../protected/connSql.php";

    if ($_SESSION["role"] == 0) // student role
    {
        $stmt = $conn->prepare("SELECT groups.id, groups.name, groups.description, groups.photo FROM groups 
                                JOIN linkUserClass 
                                ON linkUserClass.userId = ? 
                                AND linkUserClass.classId = ? 
                                WHERE groups.classId = ?;");

        // SQL query here gets the classes that the user is in, the name of the classes, and the name of the teacher. 
        // Inner subquery gets the list of class IDs assosciated with the user, and the first join joins it with the class table
        // Second join adds the column for the teacher's name. 

        $stmt->bind_param("iii", $_SESSION["userId"], $classId, $classId);

        $stmt->execute();

        $stmt->bind_result($groupId, $groupName, $groupDescription, $groupPhoto);
    }
    /*else if ($_SESSION["role"] == 1) 
    {
        $stmt = $conn->prepare("SELECT classes.id, classes.name FROM `classes` WHERE classes.teacherId = ?;");

        // SQL query here gets the classes that the user is teacher of, since this user is a teacher (role is 1). Nice simple query. 

        $stmt->bind_param("s",  $_SESSION["userId"]);

        $stmt->execute();

        $stmt->bind_result($classId, $className);
        $teacherName = "";
    }*/
    
    $groupCount = 0;
?>

<!--<link rel="stylesheet" type="text/css" href="style.css">-->

<style>
    <?php include "style.css"?>
</style>

<body translate="no">
    <h2 style="color:black;">Groups</h2>
    <ul class="cards">
        <?php while ($stmt->fetch()) 
        { 
            $groupCount++;?>
            <li class="cards__item">
                <a href="/classes/<?=$classId?>/groups/<?=$groupId?>" class="card" outline=none>
                    <div class="card__image card__image--fence"></div>
                    <div class="card__content">
                        <div class="card__title"><?=$groupName?></div>
                        <p class="card__text"><?=$groupDescription?></p>
                        <!--<button class="btn btn--block card__btn">Button</button>-->
                    </div>
                </a>
            </li>
        <?php 
        };
        if ($groupCount == 0)
        {
            ?>
                <p style="color:black">There are no groups in this class.</p>
            <?php
        } 
        ?>

    </ul>
    
</body>