<?php
include '../config.php';

// grab the form data
$first = $_POST['first_name'];
$last = $_POST['last_name'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$city = $_POST['city'];
$state = $_POST['state'];
$zip = $_POST['zip'];
$gender = $_POST['gender'];
$dob = $_POST['dob'];

$sql = "INSERT INTO customers (first_name, last_name, phone, address, city, state, zip, gender, dob) VALUES ('$first', '$last', '$phone', '$address', '$city', '$state', '$zip', '$gender', '$dob')";

if (mysqli_query($conn, $sql)) {
    header("Location: ../forms/customer_form.php?msg=success");
} else {
    header("Location: ../forms/customer_form.php?msg=error");
}
?>
