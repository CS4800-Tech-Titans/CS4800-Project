<?php
    include_once "../protected/ensureLoggedIn.php";
    include_once "../protected/connSql.php";

    $stmt = $conn->prepare("SELECT classes.id, classes.name, users.name FROM classes JOIN 
    ( -- Your initial query that returns a list of classIds 
        SELECT classId FROM linkUserClass WHERE userId = ? 
    ) subquery 
    ON classes.id = subquery.classId 
    JOIN users 
    ON classes.teacherId = users.id;");

    $stmt->bind_param("s",  $_SESSION["userId"]);

    $stmt->execute();

    $stmt->bind_result($classId, $className, $teacherName /* and so on for all columns */);

    // Fetch and process the results
    //while ($stmt->fetch()) {
    //    echo $className." with Teacher: ".$teacherName."---";
    //}
    //echo $conn;

    /*$userProfiles = array(
        array(
            'name' => 'John Doe',
            'description' => 'This is the shorthand for flex-grow, flex-shrink and flex-basis combined. The
            second and third parameters (flex-shrink and flex-basis) are optional. Default is 0 1 auto. ',
            'age' => 25,
            'city' => 'New York',
            // Add more profile information here
        ),
        array(
            'name' => 'Jane Smith',
            'description' => 'Class B',
            'age' => 30,
            'city' => 'Los Angeles',
            // Add more profile information here
        ),
        array(
            'name' => 'Joe Biden',
            'description' => 'Cthird parameters (flex-shrink and flex-basis) are optional. Default is 0 1 a',
            'age' => 22,
            'city' => 'Los Angeles',
            // Add more profile information here
        ),
        // Add more profiles as needed
    );           */
?>

<!--<link rel="stylesheet" type="text/css" href="style.css">-->

<style>
    <?php include "style.css"?>
</style>

<body translate="no">
    <h1 style="color:black;">My Classes</h1>
    <ul class="cards">
        <?php while ($stmt->fetch()) { ?>
            <li class="cards__item">
                <a href="<?=$classId?>" class="card" outline=none>
                    <div class="card__image card__image--fence"></div>
                    <div class="card__content">
                        <div class="card__title"><?=$className?></div>
                        <p class="card__text"><?=$teacherName?></p>
                        <!--<button class="btn btn--block card__btn">Button</button>-->
                    </div>
                </a>
            </li>
        <?php }; ?>
        <!--
        <li class="cards__item">
            <div class="card">
                <div class="card__image card__image--fence"></div>
                <div class="card__content">
                    <div class="card__title">Flex</div>
                    <p class="card__text">This is the shorthand for flex-grow, flex-shrink and flex-basis combined. The
                        second and third parameters (flex-shrink and flex-basis) are optional. Default is 0 1 auto. </p>
                    <button class="btn btn--block card__btn">Button</button>
                </div>
            </div>
        </li>
        <li class="cards__item">
            <div class="card">
                <div class="card__image card__image--river"></div>
                <div class="card__content">
                    <div class="card__title">Flex Grow</div>
                    <p class="card__text">This defines the ability for a flex item to grow if necessary. It accepts a
                        unitless value that serves as a proportion. It dictates what amount of the available space
                        inside the flex container the item should take up.</p>
                    <button class="btn btn--block card__btn">Button</button>
                </div>
            </div>
        </li>
        <li class="cards__item">
            <div class="card">
                <div class="card__image card__image--record"></div>
                <div class="card__content">
                    <div class="card__title">Flex Shrink</div>
                    <p class="card__text">This defines the ability for a flex item to shrink if necessary. Negative
                        numbers are invalid.</p>
                    <button class="btn btn--block card__btn">Button</button>
                </div>
            </div>
        </li>
        <li class="cards__item">
            <div class="card">
                <div class="card__image card__image--flowers"></div>
                <div class="card__content">
                    <div class="card__title">Flex Basis</div>
                    <p class="card__text">This defines the default size of an element before the remaining space is
                        distributed. It can be a length (e.g. 20%, 5rem, etc.) or a keyword. The auto keyword means
                        "look at my width or height property."</p>
                    <button class="btn btn--block card__btn">Button</button>
                </div>
            </div>
        </li> -->
    </ul>
    
</body>