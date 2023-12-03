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
$myGroup = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page Title</title>
    <style>
        <?php include "style.css" ?>
        /* Add custom CSS for the plus button */
        .add-group-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background-color: #000;
            /* Black background color */
            border: none;
            border-radius: 50%;
            color: #FFF;
            font-size: 24px;
            text-align: center;
            line-height: 50px;
            cursor: pointer;
            transition: background-color 0.3s;
            /* Add a smooth transition effect */
        }

        /* Hover effect: Display "Add Group" on hover */
        .add-group-button:hover {
            background-color: #007BFF;
            /* Change the background color on hover */
            content: "Add Group";
            /* Add the text on hover */
        }

        /* Ensure card is clickable and cursor changes to pointer */
        .card {
            cursor: pointer;
        }

        * {
            box-sizing: border-box;
        }

        .modal-container {
            /*display: none;
            align-items: center;*/
            background-color: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            top: 0;
            left: 0;
            height: 100vh;
            width: 100vw;
            position: fixed;

            opacity: 0;
            pointer-events: none;
            transition: opacity 0.40s ease
        }

        .modal-container.show {
            opacity: 1;
            pointer-events: auto;
        }

        .inner-modal-container {
            max-height: 100%;
            overflow-y: auto;
        }

        .modal {
            background-color: white;
            border-radius: 10px;
            width: 600px;
            padding: 30px;
            overflow-y: auto;
            text-align: center;
            font-family: Arial, sans-serif;
            color:black;
            font-size: 18px;
            font-weight: bold;

        }

        .form-group {
            display: flex;
            flex-direction: column;
            font-size: 18px;
            margin: 20px 0px;
        }

        .form-group input,
        .form-group textarea {
            font-family: Arial, sans-serif;
            font-size: 18px;
            line-height: 1.2;
        }

        .form-group textarea {
            resize: none;
            overflow-y: hidden;
        }

        .form-group textarea.max-size {
            overflow-y: visible;
        }

        #img-preview {
            max-width: 100%;
            margin-top: 10px;
            margin-bottom: 10px;
            border: 3px solid black;
        }

        .buttons input,
        .buttons button {
            font-size: 18px;
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
                <a class="card" id="card<?= $groupId ?>" onclick="navigateToURL(<?= $groupId ?>, <?= $classId ?>)">
                    <div class="card__image" style="<?php
                    if ($groupPhoto === null)
                        echo "background-image: url(https://unsplash.it/800/600?image=82);";
                    else
                        echo "background-image: url(data:image/*;base64," . base64_encode($groupPhoto);
                    ?>)"></div>

                    <div class="card__content">
                        <div class="card__title">
                            <?= $groupName ?>
                        </div>
                        <p class="card__text">
                            <?= $groupDescription ?>
                        </p>
                        <?php
                        if ($isUserInGroup) {
                            $myGroup = $groupId;
                            #echo '<button class="join-group-button" disabled>Joined</button>';
                        } else {
                            echo '<button class="join-group-button" data-group-id="' . $groupId . '">Request to Join</button>';
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
            // window.location.href = '/create_group.php';
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

    <div class="modal-container" id="modalContainer">
        <div class="inner-modal-container">

            <div class="modal">
                <h1>Create New Group</h1>
                <form method="POST" action=<?= "/classes/".$classId."/createGroup"?> enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="image">Group Logo</label>
                        <img id="img-preview" src="/images/defaultGroupImage.jpg" alt="Default Image"
                            onclick="document.getElementById('image').click()">
                        <input type="file" id="image" name="image" accept="image/*"
                            onchange="displayImagePreview(this)">
                    </div>

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" cols="50" oninput="resizeTextArea(this)"
                            required></textarea>
                    </div>

                    <div class="buttons">
                        <input type="submit" value="Create Group">
                        <button type="button" id="closeModal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

    </div>


    <script>
        function resizeTextArea(textarea) {
            textarea.style.height = 'auto';
            newHeight = textarea.scrollHeight;

            const maxHeight = 35 * parseFloat(getComputedStyle(textarea).lineHeight);

            if (newHeight > maxHeight) {
                textarea.style.height = maxHeight + "px";
                textarea.classList.add("max-size");
            }
            else {
                textarea.style.height = newHeight + "px";
                textarea.classList.remove("max-size");
            }
        }

        function displayImagePreview(input) {
            var preview = document.getElementById('img-preview');
            var file = input.files[0];
            var reader = new FileReader();

            reader.onloadend = function () {
                preview.src = reader.result;
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = "defaultGroupImage.jpg";
            }
        }


        document.getElementById("createGroupButton").addEventListener("click", () => {
            document.getElementById("modalContainer").classList.add("show");
        });
        document.getElementById("closeModal").addEventListener("click", () => {
            document.getElementById("modalContainer").classList.remove("show");
        })
    </script>
</body>

<script>
    myGroupId = <?=$myGroup?>;
    // Function to generate a random color
    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
    document.addEventListener('DOMContentLoaded', function () {
        // JavaScript to handle the invite button click
        const inviteButtons = document.querySelectorAll('.invite-button');
        inviteButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                // Stop event propagation to prevent other click events
                event.stopPropagation();

                const studentName = button.getAttribute('student-name');
                const studentId = button.getAttribute('student-id');

                // You can implement the logic to send an invite here
                // For simplicity, I'm just logging the student name to the console
                console.log('Inviting student: ' + studentName);


                // Call invite_user_to_group.php directly using AJAX
                const xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        // Handle the response, if needed
                        //console.log('Join group response:', xhr.responseText);
                        // Refresh the page after the join is successful
                        //location.reload();
                        button.innerHTML = "Invited";
                        button.disabled = true;
                    }
                };

                // Define the parameters to send to join_group.php
                const params = `groupId=${myGroupId}&receiverUserId=${studentId}&message=none`;

                xhr.open('POST', '/invite_user_to_group.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.send(params);

                //console.log(params);
                // Change the color of the button to a random color
                button.style.backgroundColor = getRandomColor();
            });
        });
    });
</script>

</html>