<?php
session_start();
?>
<!DOCTYPE html>
<html>
<body>
You are now logged out.
<?php
// remove all session variables
session_unset();

// destroy the session
session_destroy();
?>

</body>
</html>