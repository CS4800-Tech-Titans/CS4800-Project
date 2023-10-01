<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Cards</title>
    <style>
        <?php
        // Include the CSS code as-is
        include 'styles.css';
        ?>

        /* Add custom CSS for arranging the profile cards side by side */
        .profile-container {
            display: flex;
            justify-content: space-between;
            margin: 20px; /* Adjust the margin as needed */
        }

         /* Add CSS to change the text color of the title to white */
         h1 {
            color: white;
        }
    </style>
</head>
<body>
    <h1>Profile Page</h1>
    <div class="profile-container">
        <?php
        // Include the HTML code for the first profile card
        include 'profile.html';
        include 'profile.html';
        include 'profile.html';
        ?>
    </div>
    <div class="profile-container">
        <?php
        // Include the HTML code for the second profile card
        include 'profile.html';
        ?>
    </div>
</body>
</html>
