<?php
$userProfiles = array(
    array(
        'name' => 'John Doe',
        'class' => 'Class A',
        'age' => 25,
        'city' => 'New York',
        // Add more profile information here
    ),
    array(
        'name' => 'Jane Smith',
        'class' => 'Class B',
        'age' => 30,
        'city' => 'Los Angeles',
        // Add more profile information here
    ),
    array(
        'name' => 'Joe Biden',
        'class' => 'Class C',
        'age' => 22,
        'city' => 'Los Angeles',
        // Add more profile information here
    ),
    // Add more profiles as needed
);
?>

<style>
    .user-profiles {
        display: flex;
        flex-wrap: wrap;
    }

    .profile {
        width: calc(33.33% - 10px); /* Display 3 profiles per row */
        margin: 5px;
        border: 1px solid #ccc;
        padding: 10px;
    }

    @media (max-width: 800px) {
        .profile {
            width: calc(50% - 10px); /* Display 2 profiles per row when the window is smaller */
        }
    }

    @media (max-width: 480px) {
        .profile {
            width: 100%; /* Display 1 profile per row on smaller screens */
        }
    }
</style>

<div class="user-profiles">
    <?php foreach ($userProfiles as $profile): ?>
        <div class="profile">
            <h2><?= $profile['name']; ?></h2>
            <p><?= $profile['class']; ?></p>
            <!-- Add more profile information here -->
        </div>
    <?php endforeach; ?>
</div>