<?php
include '../config.php';

// grab the form data
$make = $_POST['make'];
$model = $_POST['model'];
$year = $_POST['year'];
$color = $_POST['color'];
$miles = $_POST['miles'] != '' ? $_POST['miles'] : 0;
$condition = $_POST['condition_desc'];
$book_price = $_POST['book_price'] != '' ? $_POST['book_price'] : 0;
$price_paid = $_POST['price_paid'] != '' ? $_POST['price_paid'] : 0;
$style = $_POST['style'];
$interior_color = $_POST['interior_color'];
$purchase_date = $_POST['purchase_date'];
$location = $_POST['location'];
$seller = $_POST['seller_dealer'];
$is_auction = isset($_POST['is_auction']) ? 1 : 0;
$buyer = $_POST['buyer'];

// add the vehicle first
$sql = "INSERT INTO vehicles (make, model, year, color, miles, condition_desc, book_price, style, interior_color, status) VALUES ('$make', '$model', $year, '$color', $miles, '$condition', $book_price, '$style', '$interior_color', 'available')";
$ok = mysqli_query($conn, $sql);
$vehicle_id = mysqli_insert_id($conn);

if ($ok) {
    // now the purchase record
    $sql = "INSERT INTO purchases (vehicle_id, employee_id, purchase_date, location, seller_dealer, is_auction, price_paid) VALUES ($vehicle_id, $buyer, '$purchase_date', '$location', '$seller', $is_auction, $price_paid)";
    mysqli_query($conn, $sql);
    $purchase_id = mysqli_insert_id($conn);

    // add any repairs they listed
    for ($i = 1; $i <= 5; $i++) {
        $desc = $_POST['repair_desc_' . $i];
        $est = $_POST['repair_est_' . $i];
        $actual = $_POST['repair_actual_' . $i];

        // skip empty rows
        if ($desc != '') {
            $est = $est != '' ? $est : 0;
            $actual = $actual != '' ? $actual : 0;
            $sql = "INSERT INTO repairs (purchase_id, problem_num, description, est_cost, actual_cost) VALUES ($purchase_id, $i, '$desc', $est, $actual)";
            mysqli_query($conn, $sql);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Purchase Result - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php $nav_prefix = '../'; include '../nav.php'; ?>

<?php if ($ok) { ?>
    <div class="success">Purchase recorded!</div>
    <p>Vehicle ID: <?php echo $vehicle_id; ?></p>
<?php } else { ?>
    <div class="error">Something went wrong: <?php echo mysqli_error($conn); ?></div>
<?php } ?>

<p><a href="../forms/purchase_form.php">Add another purchase</a></p>
<p><a href="../index.html">Back to Home</a></p>

</body>
</html>
