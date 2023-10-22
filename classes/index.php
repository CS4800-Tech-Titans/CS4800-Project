<?php
    include_once "../protected/ensureLoggedIn.php";
    include_once "../protected/connSql.php";

    $stmt = $conn->prepare("SELECT classes.id, classes.name, users.name FROM classes JOIN 
    ( 
        SELECT classId FROM linkUserClass WHERE userId = ? 
    ) subquery 
    ON classes.id = subquery.classId 
    JOIN users 
    ON classes.teacherId = users.id;");

    // SQL query here gets the classes that the user is in, the name of the classes, and the name of the teacher. 
    // Inner subquery gets the list of class IDs assosciated with the user, and the first join joins it with the class table
    // Second join adds the column for the teacher's name. 

    $stmt->bind_param("s",  $_SESSION["userId"]);

    $stmt->execute();

    $stmt->bind_result($classId, $className, $teacherName);
    
    $classCount = 0;
?>

<!--<link rel="stylesheet" type="text/css" href="style.css">-->

<style>
    <?php include "style.css"?>
</style>

<body translate="no">
    <h1 style="color:black;">My Classes</h1>
    <ul class="cards">
        <?php while ($stmt->fetch()) 
        { 
            $classCount++;?>
            <li class="cards__item">
                <a href="<?=$classId?>" class="card" outline=none>
                    <div class="card__image card__image--fence"></div>
                    <div class="card__content">
                        <div class="card__title"><?=$className?></div>
                        <p class="card__text"><?=$teacherName?></p>
                        <!--<button class="btn btn--block card__btn">Button</button>-->
                    </div>
                </a>
            </li>
        <?php 
        };
        if ($classCount == 0)
        {
            ?>
                <p style="color:black">You are not enrolled in any classes.</p>
            <?php
        } 
        ?>

    </ul>
    
</body>