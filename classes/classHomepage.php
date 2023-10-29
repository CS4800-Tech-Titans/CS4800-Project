<?php
include_once "../protected/ensureLoggedIn.php";
include_once "../protected/connSql.php";

if ($_SESSION["role"] == 0) {
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
    
    // Initialize an empty array for students
    $students = array();

    // Get the names of all students in the class
    $studentsStmt = $conn->prepare("SELECT users.name
        FROM `users`
        JOIN linkUserClass ON users.id = linkUserClass.userId
        WHERE linkUserClass.classId = ?");
    
    $studentsStmt->bind_param("i", $classId);
    
    $studentsStmt->execute();
    
    $studentsStmt->bind_result($studentName);
    
    while ($studentsStmt->fetch()) {
        $students[] = $studentName;
    }
    
    // Close the students statement
    $studentsStmt->close();
}
else if ($_SESSION["role"] == 1)
{
    $stmt = $conn->prepare("SELECT classes.name, classes.description FROM `classes` WHERE classes.id = ? AND classes.teacherId = ?;");

    $stmt->bind_param("ii", $classId, $_SESSION["userId"]);

    $stmt->execute();

    $stmt->bind_result($className, $classDescription);
    $teacherId = $_SESSION["userId"];
    $teacherName = $_SESSION["name"];
}

?>


<style>
    <?php include "style.css"?>
</style>

<body translate="no">
    <h1><br></h1>
    <h1 style="color:black;"><?=$className?></h1>
    <p style="color:black;">Instructor: <?=$teacherName?></p>
    <p style="color:black;"><?=$classDescription?></p>
</body>

<body translate="no">
    <h2 style="color:black;">Students</h2>
    <ul class="cards">
        <?php foreach ($students as $student) { ?>
            <li class="cards__item">
                <div class="card__content">
                    <div class="card__title"><?=$student?></div>
                </div>
            </li>
        <?php } ?>
        <?php if (empty($students)) { ?>
            <p style="color:black">There are no students in this class.</p>
        <?php } ?>
    </ul>
</body>

<?php
include_once "groups/index.php";
include "../sidebar.html";
?>