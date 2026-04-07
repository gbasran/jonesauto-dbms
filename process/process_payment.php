<?php
include '../config.php';

// grab the form data
$customer_id = $_POST['customer_id'];
$sale_id = $_POST['sale_id'];
$payment_date = $_POST['payment_date'];
$due_date = $_POST['due_date'];
$paid_date = $_POST['paid_date'];
$amount = $_POST['amount'] != '' ? $_POST['amount'] : 0;
$bank = $_POST['bank_account'];

// check required fields
if ($amount == 0 || $due_date == '' || $paid_date == '') {
    header("Location: ../forms/payment_form.php?customer_id=$customer_id&msg=error");
    exit;
}

// record the payment
$sql = "INSERT INTO payments (customer_id, sale_id, payment_date, due_date, paid_date, amount, bank_account) VALUES ($customer_id, $sale_id, '$payment_date', '$due_date', '$paid_date', $amount, '$bank')";
mysqli_query($conn, $sql);

// check if this was a late payment and update customer stats
if ($paid_date > $due_date) {
    // count all late payments for this customer
    $sql = "SELECT COUNT(*) as late_count, AVG(DATEDIFF(paid_date, due_date)) as avg_late FROM payments WHERE customer_id = $customer_id AND paid_date > due_date";
    $result = mysqli_query($conn, $sql);
    $stats = mysqli_fetch_assoc($result);

    $late_count = $stats['late_count'];
    $avg_late = round($stats['avg_late'], 1);

    // update the customer record
    $sql = "UPDATE customers SET num_late_payments = $late_count, avg_days_late = $avg_late WHERE customer_id = $customer_id";
    mysqli_query($conn, $sql);
}

header("Location: ../forms/payment_form.php?customer_id=$customer_id&msg=success");
?>
