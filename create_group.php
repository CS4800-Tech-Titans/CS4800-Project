<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group</title>
</head>
<body>
    <h1>Create a New Group</h1>

    <?php
    include_once "./protected/ensureLoggedIn.php";
    include_once "./protected/connSql.php";

    $stmt = $conn->prepare("SELECT classes.id, classes.name FROM classes JOIN 
    ( 
        SELECT classId FROM linkUserClass WHERE userId = ? 
    ) subquery 
    ON classes.id = subquery.classId 
    JOIN users 
    ON classes.teacherId = users.id;");

    $stmt->bind_param("s", $_SESSION["userId"]);

    $stmt->execute();

    $stmt->bind_result($classId, $className);
    $classCount = 0;
    ?>

    <form action="process_create_group.php" method="POST" enctype="multipart/form-data">
        <label for="groupName">Group Name:</label>
        <input type="text" id="groupName" name="groupName" required>

        <label for="groupDescription">Group Description:</label>
        <textarea id="groupDescription" name="groupDescription" required></textarea>

        <label for="groupPhoto">Group Photo:</label>
        <input type="file" id="groupPhoto" name="groupPhoto" accept="image/*">

        <label for="classId">Select Class:</label>
        <select id="classId" name="classId" required>
            <?php
            while ($stmt->fetch()) {
                echo "<option value=\"$classId\">$className</option>";
                $classCount++;
            }
            ?>
        </select>

        <input type="hidden" name="userRole" value="<?php echo $_SESSION["role"]; ?>">

        <?php
        if ($classCount === 0) {
            echo "<p style=\"color:black\">You are not enrolled in any classes.</p>";
        }
        ?>

        <button type="submit">Create Group</button>
    </form>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
