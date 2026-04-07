<?php
include '../config.php';

// grab the form data
$sale_id = $_POST['sale_id'];
$vehicle_id = $_POST['vehicle_id'];
$customer_id = $_POST['customer_id'];
$employee_id = $_POST['employee_id'];
$warranty_date = $_POST['warranty_sale_date'];
$total_cost = $_POST['total_cost'] != '' ? $_POST['total_cost'] : 0;
$monthly_cost = $_POST['monthly_cost'] != '' ? $_POST['monthly_cost'] : 0;

// make sure we have a sale
if ($sale_id == '') {
    header("Location: ../forms/warranty_form.php?msg=error");
    exit;
}

// create the warranty record
$sql = "INSERT INTO warranties (sale_id, vehicle_id, customer_id, employee_id, warranty_sale_date, total_cost, monthly_cost) VALUES ($sale_id, $vehicle_id, $customer_id, $employee_id, '$warranty_date', $total_cost, $monthly_cost)";
mysqli_query($conn, $sql);
$warranty_id = mysqli_insert_id($conn);

// add the warranty items (loop until we run out of rows)
for ($i = 1; isset($_POST['warranty_type_' . $i]); $i++) {
    $type = $_POST['warranty_type_' . $i];
    $start = $_POST['item_start_' . $i];
    $months = $_POST['item_months_' . $i];
    $cost = $_POST['item_cost_' . $i];
    $deductible = $_POST['item_deductible_' . $i];
    $covered = $_POST['items_covered_' . $i];

    // handle empty numbers
    $months = $months != '' ? $months : 0;
    $cost = $cost != '' ? $cost : 0;
    $deductible = $deductible != '' ? $deductible : 0;

    if ($type != '') {
        $sql = "INSERT INTO warranty_items (warranty_id, warranty_type, start_date, length_months, cost, deductible, items_covered) VALUES ($warranty_id, '$type', '$start', $months, $cost, $deductible, '$covered')";
        mysqli_query($conn, $sql);
    }
}

header("Location: ../forms/warranty_form.php?sale_id=$sale_id&msg=success");
?>
