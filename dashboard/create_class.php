<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Class</title>
</head>
<body>
    <h1>Create a New Class</h1>

    <?php
    include_once "../protected/ensureLoggedIn.php";
    include_once "../protected/connSql.php";
    ?>

    <form action="process_create_class.php" method="POST" enctype="multipart/form-data">
        <label for="className">Class Name:</label>
        <input type="text" id="className" name="className" required>

        <label for="classDescription">Class Description:</label>
        <textarea id="classDescription" name="classDescription" required></textarea>

        <input type="hidden" name="userRole" value="<?php echo $_SESSION["role"]; ?>">
        <input type="hidden" name="userId" value="<?php echo $_SESSION["userId"]; ?>">

       


        <button type="submit">Create Class</button>
    </form>

</body>
</html>
