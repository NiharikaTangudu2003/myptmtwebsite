<?php
// Start the session to access session variables
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect the user to the login page after logging out
header('Location: guide_login.php');
exit();
?>
