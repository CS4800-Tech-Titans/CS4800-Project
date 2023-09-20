<?php
echo "<html>\n<body>";
echo "<p>Your ip address is: " . $_SERVER['REMOTE_ADDR']."</p>";
date_default_timezone_set('America/Los_Angeles');
$currentDateTime = date('g:ia, F j, Y');
echo "<p>This request was made at $currentDateTime.</p></body></html>";