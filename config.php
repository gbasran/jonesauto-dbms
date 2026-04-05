<?php
// db connection stuff
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jonesauto";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// check if it worked
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
