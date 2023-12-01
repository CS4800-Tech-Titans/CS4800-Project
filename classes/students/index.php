<?php
include_once "../protected/ensureLoggedIn.php";
include "../protected/connSql.php";

if ($_SESSION["role"] == 0) // student role
{
    $stmt = $conn->prepare("SELECT groups.id, groups.name, groups.description, groups.photo, users.name 
                            FROM `groups` 
                            JOIN linkUserClass 
                            ON linkUserClass.classId = groups.classId
                            AND linkUserClass.userId = ?
                            JOIN users
                            ON linkUserClass.userId = users.id
                            WHERE groups.classId = ?;");

    $stmt->bind_param("ii", $_SESSION["userId"], $classId);

    $stmt->execute();

    $stmt->bind_result($groupId, $groupName, $groupDescription, $groupPhoto, $studentName);
}

$groupCount = 0;

// Dummy data for demonstration (replace with your actual logic to fetch student names)
$students = ["Student 1", "Student 2", "Student 3"];
?>

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

    /* Style for the join group button */
    .join-group-button {
        background-color: #03bfbc;
        border: 1px solid #03bfbc;
        padding: 10px;
        color: #231e39;
        border-radius: 3px;
        cursor: pointer;
        transition: background-color 0.5s;
    }

    /* Hover effect for the join group button */
    .join-group-button:hover {
        background-color: #007BFF;
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
                        <p class="card__text">Student: <?=$studentName?></p>
                        <button class="join-group-button" data-group-id="<?=$groupId?>" title="Join Group">Join</button>
                        <button class="join-group-button" data-group-id="<?=$groupId?>" title="Invite Student">Invite</button>
                    </div>
                </a>
            </li>
        <?php } ?>

        <h2 style="color:black;">Students</h2>
        <ul class="cards">
            <?php foreach ($students as $student) { ?>
                <li class="cards__item">
                    <div class="card__content">
                        <div class="card__title"><?=$student?></div>
                    </div>
                </li>
            <?php } ?>
        </ul>

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

    // JavaScript to handle the join and invite group button clicks
    const joinButtons = document.querySelectorAll('.join-group-button');
    joinButtons.forEach(button => {
        button.addEventListener('click', function () {
            const groupId = button.getAttribute('data-group-id');
            const action = button.title.toLowerCase(); // Get the action from the button title
            if (action === 'join group') {
                // Redirect the user to a page or API endpoint to join the selected group
                window.location.href = `/join_group.php?group_id=${groupId}`;
            } else if (action === 'invite student') {
                // Implement the logic to invite a student
                console.log(`Invite student to group with ID ${groupId}`);
            }
        });
    });
</script>
