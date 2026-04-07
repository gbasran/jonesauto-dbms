<?php
include '../config.php';

// grab the form data
$first = $_POST['first_name'];
$last = $_POST['last_name'];
$phone = $_POST['phone'];
$role = $_POST['role'];

// check if we're updating or inserting
if (isset($_POST['employee_id']) && $_POST['employee_id'] != '') {
    $id = $_POST['employee_id'];
    $sql = "UPDATE employees SET first_name='$first', last_name='$last', phone='$phone', role='$role' WHERE employee_id = $id";
    if (mysqli_query($conn, $sql)) {
        // log the update
        mysqli_query($conn, "INSERT INTO operations_log (table_name, record_id, operation) VALUES ('employees', $id, 'update')");
        header("Location: ../forms/employee_form.php?msg=updated");
    } else {
        header("Location: ../forms/employee_form.php?msg=error");
    }
} else {
    $sql = "INSERT INTO employees (first_name, last_name, phone, role) VALUES ('$first', '$last', '$phone', '$role')";
    if (mysqli_query($conn, $sql)) {
        // log the insert
        $new_id = mysqli_insert_id($conn);
        mysqli_query($conn, "INSERT INTO operations_log (table_name, record_id, operation) VALUES ('employees', $new_id, 'create')");
        header("Location: ../forms/employee_form.php?msg=success");
    } else {
        header("Location: ../forms/employee_form.php?msg=error");
    }
}
?>
