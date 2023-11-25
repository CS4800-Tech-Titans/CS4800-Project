<?php
include_once "../protected/ensureLoggedIn.php";
include "../protected/connSql.php";

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["group_id"])) {
    $groupId = $_GET["group_id"];
    
    // Check if the user is already in the group
    $checkUserInGroup = $conn->prepare("SELECT 1 FROM linkUserGroup WHERE userId = ? AND groupId = ?");
    $checkUserInGroup->bind_param("ii", $_SESSION["userId"], $groupId);
    $checkUserInGroup->execute();
    $checkUserInGroup->store_result();

    if ($checkUserInGroup->num_rows > 0) {
        // User is already in the group, handle accordingly (redirect or show a message)
        echo "You are already a member of this group.";
    } else {
        // User is not in the group, insert a new entry
        $insertUserInGroup = $conn->prepare("INSERT INTO linkUserGroup (userId, groupId, role) VALUES (?, ?, 0)");
        $insertUserInGroup->bind_param("ii", $_SESSION["userId"], $groupId);

        if ($insertUserInGroup->execute()) {
            echo "You have successfully joined the group!";
        } else {
            echo "Failed to join the group. Please try again.";
        }

        $insertUserInGroup->close();
    }

    $checkUserInGroup->close();
} else {
    // Invalid request
    echo "Invalid request.";
}
?>
