<?php
include '../config.php';

// adding employment history record
if (isset($_POST['add_employment'])) {
    $id = $_POST['customer_id'];
    $employer = $_POST['employer'];
    $title = $_POST['emp_title'];
    $phone = $_POST['emp_phone'];
    $addr = $_POST['emp_address'];
    $start = $_POST['emp_start'];

    $sql = "INSERT INTO employment_history (customer_id, employer, title, supervisor_phone, employer_address, start_date) VALUES ($id, '$employer', '$title', '$phone', '$addr', '$start')";
    mysqli_query($conn, $sql);
    header("Location: ../forms/customer_form.php?edit=$id&msg=updated");
    exit;
}

// grab the form data
$first = $_POST['first_name'];
$last = $_POST['last_name'];

// make sure name isn't blank
if ($first == '' || $last == '') {
    header("Location: ../forms/customer_form.php?msg=error");
    exit;
}
$phone = $_POST['phone'];
$address = $_POST['address'];
$city = $_POST['city'];
$state = $_POST['state'];
$zip = $_POST['zip'];
$gender = $_POST['gender'];
$dob = $_POST['dob'];

// check if we're updating or inserting
if (isset($_POST['customer_id']) && $_POST['customer_id'] != '') {
    $id = $_POST['customer_id'];
    $sql = "UPDATE customers SET first_name='$first', last_name='$last', phone='$phone', address='$address', city='$city', state='$state', zip='$zip', gender='$gender', dob='$dob' WHERE customer_id = $id";
    if (mysqli_query($conn, $sql)) {
        // log the update
        mysqli_query($conn, "INSERT INTO operations_log (table_name, record_id, operation) VALUES ('customers', $id, 'update')");
        header("Location: ../forms/customer_form.php?msg=updated");
    } else {
        header("Location: ../forms/customer_form.php?msg=error");
    }
} else {
    $sql = "INSERT INTO customers (first_name, last_name, phone, address, city, state, zip, gender, dob) VALUES ('$first', '$last', '$phone', '$address', '$city', '$state', '$zip', '$gender', '$dob')";
    if (mysqli_query($conn, $sql)) {
        // log the insert
        $new_id = mysqli_insert_id($conn);
        mysqli_query($conn, "INSERT INTO operations_log (table_name, record_id, operation) VALUES ('customers', $new_id, 'create')");
        header("Location: ../forms/customer_form.php?msg=success");
    } else {
        header("Location: ../forms/customer_form.php?msg=error");
    }
}
?>
