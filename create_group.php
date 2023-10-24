<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group</title>
</head>
<body>
    <h1>Create a New Group</h1>
    
    <form action="process_create_group.php" method="POST" enctype="multipart/form-data">
    <label for="groupName">Group Name:</label>
    <input type="text" id="groupName" name="groupName" required>

    <label for="groupDescription">Group Description:</label>
    <textarea id="groupDescription" name="groupDescription" required></textarea>

    <label for="groupPhoto">Group Photo:</label>
    <input type="file" id="groupPhoto" name="groupPhoto" accept="image/*">
    
    <input type="hidden" name="classId" value="<?php echo $classId; ?>">
    
    <button type="submit">Create Group</button>
</form>



</body>
</html>
