<?php
include_once "../protected/ensureLoggedIn.php";
include_once "../protected/connSql.php";

$classJoinCode = "";

$isStudent = $_SESSION["role"] == 0;
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
    </style>

</head>

<body translate="no">
    <h1><br></h1>
    <h1 style="color:black;">
        <?= $className ?>
    </h1>
    <p style="color:black;">Instructor:
        <?= $teacherName ?>
    </p>
    <p style="color:black;">
        <?= $classDescription ?>
    </p>

    <?php if ($_SESSION["role"] == 1): // Check if the user is a teacher ?>
        <p style="color:black;">Join Code: <?=$classJoinCode?><p>
        <button id="copyInviteLink" onclick="copyInviteLink()">Copy Invite Link</button>
        <button id="showQRCodeBtn">Show QR Code</button>
        <div id="qrCodePopup" class="qr-code-popup">
            <span class="close-btn" id="closeQRCodePopup" font-size="20px">&times;</span>
            <h3>Your QR Code</h3>
            <div id="qrCode"></div> <!-- Container for the QR code -->
        </div>
    <?php endif; ?>
</body>

<body translate="no">
    <h2 style="color:black;">Students</h2>
    <ul class="cards">
        <?php foreach ($students as $student) { ?>
            <li class="cards__item">
                <div class="card__content">
                    <div class="card__title">
                        <?= $student[1] ?>
                    </div>

                    <?php if ($isStudent) { ?>
                        <button class="invite-button" student-id="<?= $student[0] ?>" student-name = "<?= $student[1]?>">Invite</button>
                    <?php } ?>
                    
                </div>
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
                    <button class="button" onclick="acceptInvitation(<?=$invite[0]?>, <?=$invite[3]?>)">Accept Invitation</button>
                </div>
            <?php } ?>
            <?php if (empty($myInvites)) { ?>
                <p style="color:black">You have no invites.</p>
            <?php } ?>
        <?php } ?>
        
</body>


<script src="https://rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>

<script>
    
    const joinCode = '<?=$classJoinCode?>';
    const joinUrl = "http://" + window.location.host + "/join_class/" + joinCode;

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