<?php
include '../config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Late Payments - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php $nav_prefix = '../'; include '../nav.php'; ?>

<h2>Late Payment Customers</h2>

<?php
// only shows customers who have at least one late payment
$sql = "SELECT c.customer_id, CONCAT(c.first_name, ' ', c.last_name) as customer_name, c.phone, c.num_late_payments, c.avg_days_late, COUNT(p.payment_id) as total_payments, SUM(CASE WHEN p.paid_date > p.due_date THEN 1 ELSE 0 END) as late_count FROM customers c JOIN payments p ON c.customer_id = p.customer_id WHERE p.is_active = 1 GROUP BY c.customer_id HAVING late_count > 0 ORDER BY late_count DESC";

$result = mysqli_query($conn, $sql);
?>

<table>
    <tr>
        <th>Customer</th>
        <th>Phone</th>
        <th>Total Payments</th>
        <th>Late Payments</th>
        <th>Avg Days Late</th>
        <th>Action</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr class="late">
        <td><?php echo $row['customer_name']; ?></td>
        <td><?php echo $row['phone']; ?></td>
        <td><?php echo $row['total_payments']; ?></td>
        <td><?php echo $row['late_count']; ?></td>
        <td><?php echo $row['avg_days_late']; ?></td>
        <td><a href="../forms/payment_form.php?customer_id=<?php echo $row['customer_id']; ?>">Record Payment</a></td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
