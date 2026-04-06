<?php
include '../config.php';

// check for make filter
$make_filter = isset($_GET['make_filter']) ? $_GET['make_filter'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Current Inventory - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="nav">
    <a href="../index.html">Back to Home</a>
</div>

<h2>Current Inventory</h2>

<!-- filter by make -->
<form method="GET">
    <label>Filter by Make:</label>
    <input type="text" name="make_filter" value="<?php echo $make_filter; ?>">
    <input type="submit" value="Filter">
    <?php if ($make_filter != '') { ?>
        <a href="inventory.php">Clear</a>
    <?php } ?>
</form>

<p>Showing: <?php echo ($make_filter != '') ? $make_filter : 'All Makes'; ?></p>

<?php
// grab available cars with cost info
$sql = "SELECT v.*, p.price_paid, p.purchase_date, (SELECT COALESCE(SUM(r.actual_cost), 0) FROM repairs r WHERE r.purchase_id = p.purchase_id) as total_repairs FROM vehicles v JOIN purchases p ON v.vehicle_id = p.vehicle_id WHERE v.status = 'available'";

if ($make_filter != '') {
    $sql .= " AND v.make LIKE '%" . $make_filter . "%'";
}

$sql .= " ORDER BY v.make, v.model";

$result = mysqli_query($conn, $sql);
$count = 0;
?>

<table>
    <tr>
        <th>Make</th>
        <th>Model</th>
        <th>Year</th>
        <th>Color</th>
        <th>Miles</th>
        <th>Condition</th>
        <th>Book Price</th>
        <th>Price Paid</th>
        <th>Repair Costs</th>
        <th>Total Cost</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) {
        $total_cost = $row['price_paid'] + $row['total_repairs'];
        $count++;
    ?>
    <tr>
        <td><?php echo $row['make']; ?></td>
        <td><?php echo $row['model']; ?></td>
        <td><?php echo $row['year']; ?></td>
        <td><?php echo $row['color']; ?></td>
        <td><?php echo number_format($row['miles']); ?></td>
        <td><?php echo $row['condition_desc']; ?></td>
        <td>$<?php echo number_format($row['book_price'], 2); ?></td>
        <td>$<?php echo number_format($row['price_paid'], 2); ?></td>
        <td>$<?php echo number_format($row['total_repairs'], 2); ?></td>
        <td>$<?php echo number_format($total_cost, 2); ?></td>
    </tr>
    <?php } ?>
</table>

<p><?php echo $count; ?> vehicle(s) found.</p>

</body>
</html>
