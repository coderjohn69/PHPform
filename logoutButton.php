<?php
session_start();
if(isset($_SESSION['email'])) {
    echo '<a href="logout.php">Logout</a>';
} else {
    echo '<a href="login.php">Login</a>';
}
?>
