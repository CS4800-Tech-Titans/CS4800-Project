<?php
include_once "../protected/ensureLoggedIn.php";
include "../protected/connSql.php";

if ($_SESSION["role"] == 0) // student role
{
    $stmt = $conn->prepare("SELECT groups.id, groups.name, groups.description, groups.photo, 
                                  IF(linkUserGroup.userId IS NOT NULL, 1, 0) AS isUserInGroup
                           FROM `groups`
                           LEFT JOIN linkUserGroup ON linkUserGroup.userId = ? AND linkUserGroup.groupId = groups.id
                           JOIN linkUserClass ON linkUserClass.userId = ? AND linkUserClass.classId = ?
                           WHERE groups.classId = ?;");

    $stmt->bind_param("iiii", $_SESSION["userId"], $_SESSION["userId"], $classId, $classId);

    $stmt->execute();

    $stmt->bind_result($groupId, $groupName, $groupDescription, $groupPhoto, $isUserInGroup);
}
$groupCount = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page Title</title>
    <style>
        <?php include "style.css"?>
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

        /* Ensure card is clickable and cursor changes to pointer */
        .card {
            cursor: pointer;
        }
    </style>
</head>

<body translate="no">
    <h2 style="color:black;">Groups</h2>
    <ul class="cards">
        <?php while ($stmt->fetch()) { 
            $groupCount++;
        ?>
            <li class="cards__item">
                <a href="/classes/<?=$classId?>/groups/<?=$groupId?>" class="card">
                    <div class="card__image card__image--fence"></div>
                    <div class="card__content">
                        <div class="card__title"><?=$groupName?></div>
                        <p class="card__text"><?=$groupDescription?></p>
                        <?php
                            if ($isUserInGroup) {
                                echo '<button class="join-group-button" disabled>Joined</button>';
                            } else {
                                echo '<button class="join-group-button" data-group-id="'.$groupId.'">Join</button>';
                            }
                        ?>
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

    <script>
        // JavaScript to handle the card click
        document.querySelectorAll('.card').forEach(function(card) {
            card.addEventListener('click', function(event) {
                // Check if the click was directly on the join button
                const clickedElement = event.target;
                if (!(clickedElement.classList.contains('join-group-button') || clickedElement.closest('.join-group-button'))) {
                    // Add any other card click handling code here
                    // For example, redirect to the card's link
                    const cardLink = card.querySelector('a');
                    window.location.href = cardLink.href;
                }
            });
        });

        // JavaScript to handle the join group button click
        const joinButtons = document.querySelectorAll('.join-group-button');
        joinButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                // Stop event propagation to prevent click on the group card
                event.stopPropagation();

                const groupId = button.getAttribute('data-group-id');

                // Change the color of the button to a random color
                button.style.backgroundColor = getRandomColor();

                // Redirect the user to a page or API endpoint to join the selected group
                // window.location.href = `/join_group.php?group_id=${groupId}`;
            });
        });

        // JavaScript to handle the create group button click
        document.getElementById('createGroupButton').addEventListener('click', function (event) {
            // Stop event propagation to prevent click on the group card
            event.stopPropagation();

            // Redirect the user to a new group creation page or display a form for creating a new group
            window.location.href = '/create_group.php';
        });

        // Function to generate a random color
        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }
    </script>
</body>

</html>
