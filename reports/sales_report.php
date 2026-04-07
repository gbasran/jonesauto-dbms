<?php
include '../config.php';

// date range filter
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales Report - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php $nav_prefix = '../'; include '../nav.php'; ?>

<h2>Sales Report</h2>

<!-- date range filter -->
<form method="GET">
    <label>From:</label>
    <input type="date" name="date_from" value="<?php echo $date_from; ?>">
    <label>To:</label>
    <input type="date" name="date_to" value="<?php echo $date_to; ?>">
    <input type="submit" value="Filter">
    <?php if ($date_from != '' || $date_to != '') { ?>
        <a href="sales_report.php">Clear</a>
    <?php } ?>
</form>

<?php
// grab sales with profit calculation
$sql = "SELECT s.*, v.make, v.model, v.year, CONCAT(c.first_name, ' ', c.last_name) as customer_name, CONCAT(e.first_name, ' ', e.last_name) as salesperson, p.price_paid, (SELECT COALESCE(SUM(r.actual_cost), 0) FROM repairs r WHERE r.purchase_id = p.purchase_id) as repair_costs, (s.sale_price - p.price_paid - (SELECT COALESCE(SUM(r.actual_cost), 0) FROM repairs r WHERE r.purchase_id = p.purchase_id)) as profit FROM sales s JOIN vehicles v ON s.vehicle_id = v.vehicle_id JOIN customers c ON s.customer_id = c.customer_id JOIN employees e ON s.employee_id = e.employee_id JOIN purchases p ON v.vehicle_id = p.vehicle_id WHERE s.is_active = 1";

if ($date_from != '' && $date_to != '') {
    $sql .= " AND s.sale_date BETWEEN '$date_from' AND '$date_to'";
} else if ($date_from != '') {
    $sql .= " AND s.sale_date >= '$date_from'";
} else if ($date_to != '') {
    $sql .= " AND s.sale_date <= '$date_to'";
}

$sql .= " ORDER BY s.sale_date DESC";

$result = mysqli_query($conn, $sql);

$total_sales = 0;
$total_cost = 0;
$total_repairs = 0;
$total_profit = 0;
?>

<table>
    <tr>
        <th>Date</th>
        <th>Vehicle</th>
        <th>Customer</th>
        <th>Salesperson</th>
        <th>Sale Price</th>
        <th>Cost</th>
        <th>Repairs</th>
        <th>Profit</th>
        <th>Action</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) {
        $profit = $row['profit'];
        $color = ($profit > 0) ? 'green' : 'red';

        // running totals
        $total_sales += $row['sale_price'];
        $total_cost += $row['price_paid'];
        $total_repairs += $row['repair_costs'];
        $total_profit += $profit;
    ?>
    <tr>
        <td><?php echo $row['sale_date']; ?></td>
        <td><?php echo $row['year'] . ' ' . $row['make'] . ' ' . $row['model']; ?></td>
        <td><?php echo $row['customer_name']; ?></td>
        <td><?php echo $row['salesperson']; ?></td>
        <td>$<?php echo number_format($row['sale_price'], 2); ?></td>
        <td>$<?php echo number_format($row['price_paid'], 2); ?></td>
        <td>$<?php echo number_format($row['repair_costs'], 2); ?></td>
        <td style="color: <?php echo $color; ?>; font-weight: bold;">$<?php echo number_format($profit, 2); ?></td>
        <td><a href="../forms/warranty_form.php?sale_id=<?php echo $row['sale_id']; ?>">Add Warranty</a></td>
    </tr>
    <?php } ?>
    <tr style="font-weight: bold; background-color: #ddd;">
        <td colspan="4">Totals</td>
        <td>$<?php echo number_format($total_sales, 2); ?></td>
        <td>$<?php echo number_format($total_cost, 2); ?></td>
        <td>$<?php echo number_format($total_repairs, 2); ?></td>
        <td style="color: <?php echo ($total_profit > 0) ? 'green' : 'red'; ?>; font-weight: bold;">$<?php echo number_format($total_profit, 2); ?></td>
    </tr>
</table>

</body>
</html>
