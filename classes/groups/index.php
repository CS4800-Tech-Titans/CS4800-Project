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
                <a class="card" id="card<?=$groupId?>" onclick="navigateToURL(<?=$groupId?>, <?=$classId?>)">
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
       // JavaScript to handle the join group button click
        const joinButtons = document.querySelectorAll('.join-group-button');
        joinButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                // Stop event propagation to prevent click on the group card
                event.stopPropagation();

                const groupId = button.getAttribute('data-group-id');

                // Change the color of the button to a random color
                button.style.backgroundColor = getRandomColor();

                // Display the groupId
                console.log('Joining group: ' + groupId);

                // Call join_group.php directly using AJAX
                const xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Handle the response, if needed
                        console.log('Join group response:', xhr.responseText);
                        // Refresh the page after the join is successful
                        location.reload();
                    }
                };
                
                // Define the parameters to send to join_group.php
                const params = `groupId=${groupId}`;
                
                xhr.open('POST', '/join_group.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.send(params);
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

        // Function to change the color of the card randomly and navigate to the specified URL
        function navigateToURL(groupId, classId) {
            // Change the color of the card (optional)
            var randomColor = getRandomColor();
            var card = document.getElementById("card" + groupId);
            card.style.backgroundColor = randomColor;

            // Navigate to the specified URL
            window.location.href = "/classes/" + classId + "/groups/" + groupId;
        }
    </script>
</body>

</html>