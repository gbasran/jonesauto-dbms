<?php
include '../config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Warranty Report - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="nav">
    <a href="../index.html">Back to Home</a>
</div>

<h2>Active Warranties</h2>

<p><span style="color: red;">Red</span> = Expired | <span style="color: #856404;">Yellow</span> = Expiring within 30 days</p>

<?php
// grab warranties with expiry info
$sql = "SELECT w.*, v.make, v.model, v.year, CONCAT(c.first_name, ' ', c.last_name) as customer_name, wi.warranty_type, wi.start_date, wi.length_months, wi.cost, wi.deductible, wi.items_covered, DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH) as expiry_date, DATEDIFF(DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH), CURDATE()) as days_remaining FROM warranties w JOIN warranty_items wi ON w.warranty_id = wi.warranty_id JOIN vehicles v ON w.vehicle_id = v.vehicle_id JOIN customers c ON w.customer_id = c.customer_id ORDER BY expiry_date";

$result = mysqli_query($conn, $sql);
?>

<table>
    <tr>
        <th>Customer</th>
        <th>Vehicle</th>
        <th>Type</th>
        <th>Start Date</th>
        <th>Length</th>
        <th>Expiry Date</th>
        <th>Days Remaining</th>
        <th>Cost</th>
        <th>Deductible</th>
        <th>Items Covered</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) {
        // color code based on expiry
        $days = $row['days_remaining'];
        $style = '';
        if ($days < 0) {
            $style = ' style="background-color: #f8d7da;"';
        } else if ($days <= 30) {
            $style = ' style="background-color: #fff3cd;"';
        }
    ?>
    <tr<?php echo $style; ?>>
        <td><?php echo $row['customer_name']; ?></td>
        <td><?php echo $row['year'] . ' ' . $row['make'] . ' ' . $row['model']; ?></td>
        <td><?php echo $row['warranty_type']; ?></td>
        <td><?php echo $row['start_date']; ?></td>
        <td><?php echo $row['length_months']; ?> months</td>
        <td><?php echo $row['expiry_date']; ?></td>
        <td><?php echo $days; ?></td>
        <td>$<?php echo number_format($row['cost'], 2); ?></td>
        <td>$<?php echo number_format($row['deductible'], 2); ?></td>
        <td><?php echo $row['items_covered']; ?></td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
