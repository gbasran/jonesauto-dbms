<?php
include '../config.php';

// grab the form data
$customer_type = $_POST['customer_type'];
$vehicle_id = $_POST['vehicle_id'];
$employee_id = $_POST['employee_id'];
$sale_date = $_POST['sale_date'];
$sale_price = $_POST['sale_price'] != '' ? $_POST['sale_price'] : 0;
$total_due = $_POST['total_due'] != '' ? $_POST['total_due'] : 0;
$down_payment = $_POST['down_payment'] != '' ? $_POST['down_payment'] : 0;
$financed = $_POST['financed_amount'] != '' ? $_POST['financed_amount'] : 0;
$commission = $_POST['commission'] != '' ? $_POST['commission'] : 0;

// make sure we have a vehicle and date
if ($vehicle_id == '' || $sale_date == '') {
    header("Location: ../forms/sale_form.php?msg=error");
    exit;
}

// figure out the customer
if ($customer_type == 'new') {
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
    mysqli_query($conn, $sql);
    $customer_id = mysqli_insert_id($conn);
} else {
    $customer_id = $_POST['customer_id'];
}

// record the sale
$sql = "INSERT INTO sales (vehicle_id, customer_id, employee_id, sale_date, total_due, down_payment, financed_amount, sale_price, commission) VALUES ($vehicle_id, $customer_id, $employee_id, '$sale_date', $total_due, $down_payment, $financed, $sale_price, $commission)";
$ok = mysqli_query($conn, $sql);

// add employment history
for ($i = 1; isset($_POST['employer_' . $i]); $i++) {
    $employer = $_POST['employer_' . $i];
    $title = $_POST['emp_title_' . $i];
    $phone = $_POST['emp_phone_' . $i];
    $addr = $_POST['emp_address_' . $i];
    $start = $_POST['emp_start_' . $i];

    if ($employer != '') {
        $sql = "INSERT INTO employment_history (customer_id, employer, title, supervisor_phone, employer_address, start_date) VALUES ($customer_id, '$employer', '$title', '$phone', '$addr', '$start')";
        mysqli_query($conn, $sql);
    }
}

// mark the car as sold
$sql = "UPDATE vehicles SET status = 'sold' WHERE vehicle_id = $vehicle_id";
mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sale Result - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php $nav_prefix = '../'; include '../nav.php'; ?>

<?php if ($ok) { ?>
    <div class="success">Sale recorded!</div>
<?php } else { ?>
    <div class="error">Something went wrong: <?php echo mysqli_error($conn); ?></div>
<?php } ?>

<p><a href="../forms/sale_form.php">Record another sale</a></p>
<p><a href="../index.html">Back to Home</a></p>

</body>
</html>
