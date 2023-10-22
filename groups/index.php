<?php
    include_once "../protected/ensureLoggedIn.php";
    include_once "../protected/connSql.php";

    if ($_SESSION["role"] == 0) // student role
    {
        $stmt = $conn->prepare("SELECT groups.id, groups.name, users.name FROM groups JOIN 
        ( 
            SELECT groupId FROM linkUserGroup WHERE userId = ? 
        ) subquery 
        ON groups.id = subquery.groupId 
        JOIN users 
        ON groups.groupId = users.id;");

        // SQL query here gets the classes that the user is in, the name of the classes, and the name of the teacher. 
        // Inner subquery gets the list of class IDs assosciated with the user, and the first join joins it with the class table
        // Second join adds the column for the teacher's name. 

        $stmt->bind_param("s",  $_SESSION["userId"]);

        $stmt->execute();

        //$stmt->bind_result($groupId, $groupName, $teacherName);
    }
    else if ($_SESSION["role"] == 1) 
    {
        $stmt = $conn->prepare("SELECT groups.id, groups.name FROM `groups` WHERE groups.teacherId = ?;");

        // SQL query here gets the classes that the user is teacher of, since this user is a teacher (role is 1). Nice simple query. 

        $stmt->bind_param("s",  $_SESSION["userId"]);

        $stmt->execute();

        $stmt->bind_result($groupId, $groupName);
        $teacherName = "";
    }
    
    $groupCount = 0;
?>

<!--<link rel="stylesheet" type="text/css" href="style.css">-->

<style>
    <?php include "style.css"?>
</style>

<body translate="no">
    <h1 style="color:black;">My Groups</h1>
    <ul class="cards">
        <?php while ($stmt->fetch()) 
        { 
            $groupCount++;?>
            <li class="cards__item">
                <a href="<?=$classId?>" class="card" outline=none>
                    <div class="card__image card__image--fence"></div>
                    <div class="card__content">
                        <div class="card__title"><?=$groupName?></div>
                        <p class="card__text"><?=$teacherName?></p>
                        <!--<button class="btn btn--block card__btn">Button</button>-->
                    </div>
                </a>
            </li>
        <?php 
        };
        if ($groupCount == 0)
        {
            ?>
                <p style="color:black">You are not enrolled in any groups.</p>
            <?php
        } 
        ?>

    </ul>
    
</body>