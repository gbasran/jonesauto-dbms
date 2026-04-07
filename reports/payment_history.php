<?php
include '../config.php';

// check if customer selected
$selected = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment History - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php $nav_prefix = '../'; include '../nav.php'; ?>

<h2>Payment History</h2>

<!-- customer dropdown -->
<form method="GET">
    <label>Customer:</label>
    <select name="customer_id" onchange="this.form.submit()">
        <option value="">-- Select Customer --</option>
        <?php
        $cust_sql = "SELECT customer_id, first_name, last_name FROM customers WHERE is_active = 1 ORDER BY last_name";
        $cust_result = mysqli_query($conn, $cust_sql);
        while ($c = mysqli_fetch_assoc($cust_result)) {
            $sel = ($selected == $c['customer_id']) ? ' selected' : '';
            echo '<option value="' . $c['customer_id'] . '"' . $sel . '>' . $c['last_name'] . ', ' . $c['first_name'] . '</option>';
        }
        ?>
    </select>
</form>

<?php if ($selected != '') {
    // grab payments for this customer
    $sql = "SELECT p.*, DATEDIFF(p.paid_date, p.due_date) as days_late, v.make, v.model, v.year FROM payments p JOIN sales s ON p.sale_id = s.sale_id JOIN vehicles v ON s.vehicle_id = v.vehicle_id WHERE p.customer_id = " . $selected . " AND p.is_active = 1 ORDER BY p.due_date";

    $result = mysqli_query($conn, $sql);
?>

<table>
    <tr>
        <th>Vehicle</th>
        <th>Due Date</th>
        <th>Paid Date</th>
        <th>Amount</th>
        <th>Bank Account</th>
        <th>Days Late</th>
        <th>Status</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) {
        $late = ($row['days_late'] > 0);
        $class = $late ? ' class="late"' : '';
        $status = $late ? 'LATE' : 'On Time';
    ?>
    <tr<?php echo $class; ?>>
        <td><?php echo $row['year'] . ' ' . $row['make'] . ' ' . $row['model']; ?></td>
        <td><?php echo $row['due_date']; ?></td>
        <td><?php echo $row['paid_date']; ?></td>
        <td>$<?php echo number_format($row['amount'], 2); ?></td>
        <td><?php echo $row['bank_account']; ?></td>
        <td><?php echo $row['days_late']; ?></td>
        <td><?php echo $status; ?></td>
    </tr>
    <?php } ?>
</table>

<p><a href="../forms/payment_form.php?customer_id=<?php echo $selected; ?>">Record Payment for this Customer</a></p>

<?php } else { ?>
    <p>Select a customer to view their payment history.</p>
<?php } ?>

</body>
</html>
