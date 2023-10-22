<?php
session_start();
?>
<!DOCTYPE html>
<html>
<body>
<b> You are now logged out. </b>
<p> You will be redirected in 3 seconds. </p>

<script>
    setTimeout(redirect, 3000);
      function redirect () {
        document.location.href = "/login";
         //var result = document.getElementById("result");
         //result.innerHTML = "";
      }
</script>

<?php
// remove all session variables
session_unset();

// destroy the session
session_destroy();
?>

</body>
</html>