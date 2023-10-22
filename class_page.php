<?php
require_once 'vendor/autoload.php'; // Include Composer's autoloader

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Create a Monolog logger instance
$log = new Logger('my_http_logger');
$log->pushHandler(new StreamHandler('logs/http.log', Logger::INFO));

// Check if the request URL matches the endpoint you want to log
if ($_SERVER['REQUEST_URI'] === '/your-endpoint') {
    // Log a message
    $log->info('HTTP request received for /your-endpoint');
    
    // Add your code to handle the HTTP request
    // For example, you can send an HTTP response here
    header('Content-Type: text/plain');
    echo 'Hello, this is your endpoint!';
} else {
    // Include the HTML code for the web page
    include 'sidebar.html'; // Include the sidebar here

    // Rest of your HTML code
    ?>
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
                margin-left: 100px; /* Adjust the left margin to avoid overlap */
             }
        </style>
    </head>
    <body>
        <h1>Class Page</h1>
        <div class="profile-container">
            <?php
            // Include the HTML code for the first profile card
            include 'class_card.html';
            include 'class_card.html';
            //include 'class_card.html';
            ?>
        </div>
        <div class="profile-container">
            <?php
            // Include the HTML code for the second profile card
            include 'class_card.html';
            ?>
        </div>
    </body>
    </html>
    <?php
}
?>
