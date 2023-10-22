<?php
    $userProfiles = array(
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
    );           
?>

<!--<link rel="stylesheet" type="text/css" href="style.css">-->

<style>
    <?php include "style.css"?>
</style>

<body translate="no">
    <h1 style="color:black;">My Classes</h1>
    <ul class="cards">
        <?php foreach ($userProfiles as $profile): ?>
            <li class="cards__item">
                <a href="http://www.google.com" class="card" outline=none>
                    <div class="card__image card__image--fence"></div>
                    <div class="card__content">
                        <div class="card__title"><?=$profile['name']?></div>
                        <p class="card__text"><?=$profile['description']?></p>
                        <!--<button class="btn btn--block card__btn">Button</button>-->
                    </div>
                </a>
            </li>
        <?php endforeach; ?>
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