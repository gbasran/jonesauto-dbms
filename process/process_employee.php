<?php
include '../config.php';

// grab the form data
$first = $_POST['first_name'];
$last = $_POST['last_name'];
$phone = $_POST['phone'];
$role = $_POST['role'];

$sql = "INSERT INTO employees (first_name, last_name, phone, role) VALUES ('$first', '$last', '$phone', '$role')";

if (mysqli_query($conn, $sql)) {
    header("Location: ../forms/employee_form.php?msg=success");
} else {
    header("Location: ../forms/employee_form.php?msg=error");
}
?>
