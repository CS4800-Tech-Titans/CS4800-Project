<?php
include_once "../protected/ensureLoggedIn.php";
include_once "../protected/connSql.php";

$classJoinCode = "";

$isStudent = $_SESSION["role"] == 0;
$myUserId = $_SESSION["userId"];
if ($isStudent) { // if user is a student
    $stmt = $conn->prepare("SELECT classes.name, classes.description, classes.teacherId, users.name AS teacherName
        FROM `classes`
        JOIN users ON classes.teacherId = users.id
        JOIN linkUserClass ON linkUserClass.userId = ? AND linkUserClass.classId = ?
        WHERE classes.id = ?;");

    $stmt->bind_param("iii", $_SESSION["userId"], $classId, $classId);

    $stmt->execute();

    $stmt->bind_result($className, $classDescription, $teacherId, $teacherName);

    if (!$stmt->fetch()) {
        http_response_code(404);
        include_once("404.html");
        die();
    }

    // Close the main statement
    $stmt->close();



    $myInvites = array();

    // Get the group invites for this user
    /*$invitesStmt = $conn->prepare("SELECT groupInvite.senderUserId, groupInvite.groupId
    FROM groupInvite
    JOIN groups ON groupInvite.groupId = groups.id
    WHERE groups.classId = ? 
    AND groupInvite.receiverUserId = ?;");*/
    $invitesStmt = $conn->prepare("
        SELECT 
            groupInvite.id,
            groupInvite.senderUserId, 
            users.name, 
            groupInvite.groupId, 
            groups.name
        FROM groupInvite
        JOIN `groups` ON groupInvite.groupId = groups.id
        JOIN `users` ON groupInvite.senderUserId = users.id
        WHERE groups.classId = ? 
        AND groupInvite.receiverUserId = ?;
    ");

    $invitesStmt->bind_param("ii", $classId, $_SESSION["userId"]);

    $invitesStmt->execute();

    $invitesStmt->bind_result($inviteId, $senderUserId, $senderName, $groupId, $groupName);

    while ($invitesStmt->fetch()) {
        $myInvites[] = [$inviteId, $senderUserId, $senderName, $groupId, $groupName];
    }

    // Close the students statement
    $invitesStmt->close();


} 
else if ($_SESSION["role"] == 1) 
{  // if user is a teacher
    $stmt = $conn->prepare("SELECT classes.name, classes.description, classes.joinCode FROM `classes` WHERE classes.id = ? AND classes.teacherId = ?;");


    $stmt->bind_param("ii", $classId, $_SESSION["userId"]);

    $stmt->execute();

    $stmt->bind_result($className, $classDescription, $classJoinCode);

    if (!$stmt->fetch()) {
        http_response_code(404);
        include_once("404.html");
        die();
    }
    $stmt->close();

    $teacherId = $_SESSION["userId"];
    $teacherName = $_SESSION["name"];

    // Close the main statement
    
    // Initialize an empty array for students
    /*$students = array();

    // Get the names of all students in the class, excluding the current user
    $studentsStmt = $conn->prepare("SELECT users.name
    FROM `users`
    JOIN linkUserClass ON users.id = linkUserClass.userId
    WHERE linkUserClass.classId = ? AND users.id != ?");

    $studentsStmt->bind_param("ii", $classId, $_SESSION["userId"]);

    $studentsStmt->execute();

    $studentsStmt->bind_result($studentName);

    while ($studentsStmt->fetch()) {
    $students[] = $studentName;
    }
    
    // Close the students statement
    $studentsStmt->close();

    /*$stmt = $conn->prepare("SELECT classes.name, classes.description, classes.teacherId, users.name AS teacherName
        FROM `classes`
        JOIN users ON classes.teacherId = users.id
        WHERE classes.id = ?;");

    $stmt->bind_param("i",$classId);

    $stmt->execute();

    $stmt->bind_result($className, $classDescription, $teacherId, $teacherName);*/
}
// Initialize an empty array for students
$students = array();

// Get the names of all students in the class
$studentsStmt = $conn->prepare("SELECT users.id, users.name
    FROM `users`
    JOIN linkUserClass ON users.id = linkUserClass.userId
    WHERE linkUserClass.classId = ?");

$studentsStmt->bind_param("i", $classId);

$studentsStmt->execute();

$studentsStmt->bind_result($studentId, $studentName);

while ($studentsStmt->fetch()) {
    $students[] = [$studentId, $studentName];
}

$studentGroupMemberships = $conn->prepare("SELECT
groups.id, groups.name, users.id, users.name
FROM `groups`
JOIN linkUserGroup ON groups.id = linkUserGroup.groupId
JOIN `users` ON linkUserGroup.userId = users.id
WHERE groups.classId = ?;
");
$studentGroupMemberships->bind_param("i", $classId);

$studentGroupMemberships->execute();

$studentGroupMemberships->bind_result($groupId, $groupName, $userId, $userName);

// PHP uses hashmap implementation of array apparently.
$groupMembers = array();

while ($studentGroupMemberships->fetch())
{
if (!isset($groupMembers[$groupId]))
    $groupMembers[$groupId] = array();

// APPEND TO LIST. BASICALLY $groupMembers[$groupId].append(val)
$groupMembers[$groupId][] = $userName;
}


// Close the students statement
$studentsStmt->close();

?>

<head>
    <title>
        <?= $className ?>
    </title>
    <style>
        <?php include "style.css" ?>
        /* Additional styles for the QR Code Popup */
        .qr-code-popup {
            display: none; /* Initially hidden */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .qr-code-popup span {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }
        .invite-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 15px;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            width: 30%;
        }

        .invite-text {
            margin-right: 20px;
            color: black;
        }

        .button {
            background-color: #d1d1d1;
            color: black;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        #profile-modal button {
            background-color: #d6eaff;
            color: rgb(0, 0, 0);
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        #profile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Dim overlay color */
            z-index: 1; /* Higher z-index than the popup */
        }

        #profile-modal {
            font-family: Arial, sans-serif;
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 40px;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 2;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        #modal-bio-container {
            text-align: left;
            width: 100%;
        }

        #profile-modal h2 {
            color: #333;
        }

        #profile-modal img {
            max-width: 100%;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        #profile-modal button:disabled{
            opacity: 0.5; 
            cursor: not-allowed;
            
        }

        #profile-close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #ddd;
            color: #333;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        #user-list {
            list-style-type: none;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }

        #user-list li {
            background-color: white;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.3s ease;
            text-align: center;
            display: flex;
            flex-direction: column; 
            justify-content: center; 
        }

        #user-list h3 {
            margin-top: 10px;
            margin-bottom: 10px;
            margin-left: 0px;
            margin-right: 0px;

        }

        #user-list li:hover {
            transform: scale(1.05);
        }
    </style>

</head>

<body translate="no">
    <div id="profile-overlay"></div>

    <div id="profile-modal">
        <img id="modal-user-photo" alt="User Profile">
        <h1 id="modal-user-name">First Last</h1>  

        <div id="modal-bio-container">
            <h3>Bio</h3>
            <p id="modal-user-bio">We know nothing about this guy.</p>
        </div>
        <br>
        <button id="invite-btn">Invite to Group</button>
        <button id="message-btn" onclick="closeProfilePopup()">Private Message</button>
        <button id="profile-close-btn" onclick="closeProfilePopup()">X</button> 

        <!-- Add more profile information as needed -->
    </div>

    <h1><br></h1>
    <h1 style="color:black;">
        <?= $className ?>
    </h1>
    <p style="color:black;"><b>Instructor:</b>
        <?= $teacherName ?>
    </p>
    <p style="color:black;">
        <?= $classDescription ?>
    </p>

    <?php if ($_SESSION["role"] == 1): // Check if the user is a teacher ?>
        <br>
        <p style="color:black;margin:0px;"><b>Join Code: </b><?=$classJoinCode?></p>
        <div style="display: inline-flex; margin: 0px;">
            <p id="classInviteLinkTxt" style="color:black;margin-top:5px;margin-bottom:5px;"></p>
            <button id="showQRCodeBtn" style="margin-left: 25px;">Show QR Code</button>
        </div>
        <div id="qrCodePopup" class="qr-code-popup">
            <span class="close-btn" id="closeQRCodePopup" font-size="20px">&times;</span>
            <h3>Your QR Code</h3>
            <div id="qrCode"></div> <!-- Container for the QR code -->
        </div>
    <?php endif; ?>
</body>

<body translate="no">
    <h2 style="color:black;">Students</h2>
    <ul id="user-list">
        <?php foreach ($students as $student) { ?>
            <li onclick="openProfileModal(<?=$student[0]?>)">
                <h3><?= $student[1] ?></h3>
            </li>
        <?php } ?>
        <?php if (empty($students)) { ?>
            <p style="color:black">There are no students in this class.</p>
        <?php } ?>
    </ul>
        
        <?php if ($isStudent) { ?>
            <h2 style="color:black;">My Invites</h2>
            <?php foreach ($myInvites as $invite) { ?>
                <div class="invite-card">
                    <div class="invite-text">
                        <?=$invite[2]?> has invited you to join '<?=$invite[4]?>'
                    </div>
                    <button class="button" style="background-color:#4CAF50" onclick="acceptInvitation(<?=$invite[0]?>, <?=$invite[3]?>)">Accept</button>
                    <button class="button" style="background-color:#f44336" onclick="rejectInvitation(<?=$invite[0]?>, <?=$invite[3]?>)">Reject</button>
                </div>
            <?php } ?>
            <?php if (empty($myInvites)) { ?>
                <p style="color:black">You have no invites.</p>
            <?php } ?>
        <?php } ?>
        
</body>


<script src="https://rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>

<script>
    myUserId = <?=$myUserId?>;
    function openProfileModal(userId)
    {
        
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) 
            {
                userProfile = JSON.parse(xhr.responseText);
                
                document.getElementById("modal-user-name").innerHTML = userProfile.name;

                if (userProfile.bio)
                {
                    document.getElementById("modal-user-bio").innerHTML = userProfile.bio;
                    document.getElementById("modal-bio-container").style.display = "block";
                }
                else
                    document.getElementById("modal-bio-container").style.display = "none";

                if (userProfile.photo)
                    document.getElementById("modal-user-photo").src = 'data:image/*;base64, '+userProfile.photo;
                else
                    document.getElementById("modal-user-photo").src = "/images/defaultProfilePicture.jpg";

                if (userId == myUserId)
                {
                    document.getElementById("invite-btn").style.display = 'none';
                    document.getElementById("message-btn").style.display = 'none';
                }
                else
                {
                    var inviteBtn = document.getElementById("invite-btn");
                    inviteBtn.style.display = 'inline-block';
                    inviteBtn.onclick = () => inviteUserToGroup(userId, inviteBtn);
                    document.getElementById("message-btn").style.display = 'inline-block';
                }

                document.getElementById("profile-overlay").style.display = "block";
                document.getElementById("profile-modal").style.display = "block";
            }
        };

        // Define the parameters to send to join_group.php
        const params = `userId=`+userId;

        xhr.open('POST', '/get_user_profile.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.send(params);

       
    }

    function inviteUserToGroup(studentId, button)
    {
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
    }


    function closeProfilePopup() {
        document.getElementById("profile-overlay").style.display = "none";
        document.getElementById("profile-modal").style.display = "none";
    }
    const joinCode = '<?=$classJoinCode?>';
    const joinUrl = "http://" + window.location.host + "/join_class/" + joinCode;

    document.getElementById('classInviteLinkTxt').innerHTML = "<b>Invite Link: </b>" + joinUrl;

    function copyInviteLink()
    {
        navigator.clipboard.writeText(joinUrl);
        alert("The invite link has been copied to your clipboard. ")
    }

    function acceptInvitation(inviteId, groupId)
    {
        console.log('Accepting invite: ' + inviteId + " For group id: " + groupId);

        // Call join_group.php directly using AJAX
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Handle the response, if needed
                console.log('Accept Invite Response:', xhr.responseText);
                // Refresh the page after the join is successful
                location.reload();
            }
        };

        // Define the parameters to send to join_group.php
        const params = `inviteId=${inviteId}&groupId=${groupId}`;

        xhr.open('POST', '/accept_group_invite.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.send(params);
    }
    function rejectInvitation(inviteId, groupId)
    {
        console.log('Rejecting invite: ' + inviteId + " For group id: " + groupId);

        // Call join_group.php directly using AJAX
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Handle the response, if needed
                console.log('Reject Invite Response:', xhr.responseText);
                // Refresh the page after the join is successful
                location.reload();
            }
        };

        // Define the parameters to send to join_group.php
        const params = `inviteId=${inviteId}&groupId=${groupId}`;

        xhr.open('POST', '/reject_group_invite.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.send(params);
    }

    <?php if ($_SESSION["role"] == 1): // Teacher ?>
    // Additional JavaScript for QR Code generation and popup handling
    function createQRCode(classId) {
        //const url = `http://localhost:8080/dashboard/join_class_page.php/${classId}`;
        //const url = `http://localhost/dashboard/join_class_page.php/${classId}`;
        
        document.getElementById("qrCode").innerHTML = "";
        new QRCode(document.getElementById("qrCode"), {
            text: joinUrl,
            width: 512,
            height: 512,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    document.getElementById('showQRCodeBtn').addEventListener('click', function () {
        document.getElementById('qrCodePopup').style.display = 'block';
        createQRCode(<?= json_encode($classId) ?>); // Generate QR code with the class-specific URL
    });

    document.getElementById('closeQRCodePopup').addEventListener('click', function () {
        document.getElementById('qrCodePopup').style.display = 'none';
    });
    <?php endif; ?>
</script>


<?php
include_once "groups/index.php";
include "../sidebar.html";
?>