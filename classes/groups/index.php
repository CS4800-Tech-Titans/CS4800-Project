<?php
    include_once "../protected/ensureLoggedIn.php";
    include "../protected/connSql.php";

    if ($_SESSION["role"] == 0) // student role
    {
        $stmt = $conn->prepare("SELECT groups.id, groups.name, groups.description, groups.photo FROM `groups` 
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
<style>
    /* Add custom CSS for the plus button */
    .add-group-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        background-color: #000; /* Black background color */
        border: none;
        border-radius: 50%;
        color: #FFF;
        font-size: 24px;
        text-align: center;
        line-height: 50px;
        cursor: pointer;
        transition: background-color 0.3s; /* Add a smooth transition effect */
    }

    /* Hover effect: Display "Add Group" on hover */
    .add-group-button:hover {
        background-color: #007BFF; /* Change the background color on hover */
        content: "Add Group"; /* Add the text on hover */
    }
</style>


<body translate="no">
    <h2 style="color:black;">Groups</h2>
    <ul class="cards">
        <?php while ($stmt->fetch()) { 
            $groupCount++;
        ?>
            <li class="cards__item">
                <a href="/classes/<?=$classId?>/groups/<?=$groupId?>" class="card" outline=none>
                    <div class="card__image card__image--fence"></div>
                    <div class="card__content">
                        <div class="card__title"><?=$groupName?></div>
                        <p class="card__text"><?=$groupDescription?></p>
                        <button class="join-group-button" data-group-id="<?=$groupId?>">Join</button>
                    </div>
                </a>
            </li>
        <?php } ?>
        <?php if ($groupCount == 0) { ?>
            <p style="color:black">There are no groups in this class.</p>
        <?php } ?>
        <!-- Plus button to create a new group with a tooltip -->
        <button class="add-group-button" id="createGroupButton" title="Add Group">+</button>
    </ul>
</body>

<script>
    // JavaScript to handle the create group button click
    document.getElementById('createGroupButton').addEventListener('click', function () {
        // Redirect the user to a new group creation page or display a form for creating a new group
        window.location.href = '/create_group.php';
    });

    // JavaScript to handle the join group button click
    const joinButtons = document.querySelectorAll('.join-group-button');
    joinButtons.forEach(button => {
        button.addEventListener('click', function () {
            const groupId = button.getAttribute('data-group-id');
            // Redirect the user to a page or API endpoint to join the selected group
            window.location.href = `/join_group.php?group_id=${groupId}`;
        });
    });
</script>