<?php
include '../config.php';

// get all customers for dropdown
$sql = "SELECT customer_id, first_name, last_name FROM customers ORDER BY last_name";
$customers = mysqli_query($conn, $sql);

// check if a customer was selected
$selected_customer = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';

// check for success message
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Form - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php $nav_prefix = '../'; include '../nav.php'; ?>

<h2>Record a Payment</h2>

<?php if ($msg == 'success') { ?>
    <div class="success">Payment recorded!</div>
<?php } ?>

<table class="form-table">
    <tr>
        <td>Customer:</td>
        <td>
            <select onchange="window.location='payment_form.php?customer_id='+this.value">
                <option value="">-- Select Customer --</option>
                <?php while ($row = mysqli_fetch_assoc($customers)) { ?>
                    <option value="<?php echo $row['customer_id']; ?>" <?php echo ($selected_customer == $row['customer_id']) ? 'selected' : ''; ?>>
                        <?php echo $row['first_name'] . ' ' . $row['last_name']; ?>
                    </option>
                <?php } ?>
            </select>
        </td>
    </tr>
</table>

<?php if ($selected_customer != '') {
    // show customer info
    $sql = "SELECT * FROM customers WHERE customer_id = $selected_customer";
    $cust = mysqli_fetch_assoc(mysqli_query($conn, $sql));
?>
    <p><strong>Gender:</strong> <?php echo $cust['gender']; ?> |
       <strong>DOB:</strong> <?php echo $cust['dob']; ?> |
       <strong>Late Payments:</strong> <?php echo $cust['num_late_payments']; ?> |
       <strong>Avg Days Late:</strong> <?php echo $cust['avg_days_late']; ?></p>

<form method="POST" action="../process/process_payment.php">
    <input type="hidden" name="customer_id" value="<?php echo $selected_customer; ?>">

    <table class="form-table">
        <tr>
            <td>Sale:</td>
            <td>
                <?php
                    // get sales for this customer
                    $sql = "SELECT s.sale_id, s.sale_date, v.year, v.make, v.model FROM sales s JOIN vehicles v ON s.vehicle_id = v.vehicle_id WHERE s.customer_id = $selected_customer";
                    $sales = mysqli_query($conn, $sql);
                ?>
                <select name="sale_id" required>
                    <option value="">-- Select Sale --</option>
                    <?php while ($row = mysqli_fetch_assoc($sales)) { ?>
                        <option value="<?php echo $row['sale_id']; ?>"><?php echo $row['sale_date'] . ' - ' . $row['year'] . ' ' . $row['make'] . ' ' . $row['model']; ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Payment Date:</td>
            <td><input type="date" name="payment_date" required></td>
        </tr>
        <tr>
            <td>Due Date:</td>
            <td><input type="date" name="due_date" required></td>
        </tr>
        <tr>
            <td>Paid Date:</td>
            <td><input type="date" name="paid_date" required></td>
        </tr>
        <tr>
            <td>Amount:</td>
            <td><input type="text" name="amount"></td>
        </tr>
        <tr>
            <td>Bank Account:</td>
            <td><input type="text" name="bank_account"></td>
        </tr>
    </table>

    <br>
    <input type="submit" value="Record Payment">
</form>
<?php } ?>

<?php if ($selected_customer != '') {
    // show existing payments for this customer
    $sql = "SELECT p.*, v.year, v.make, v.model FROM payments p JOIN sales s ON p.sale_id = s.sale_id JOIN vehicles v ON s.vehicle_id = v.vehicle_id WHERE p.customer_id = $selected_customer ORDER BY p.due_date";
    $payments = mysqli_query($conn, $sql);
?>
<h3>Payment History</h3>
<table>
    <tr>
        <th>Vehicle</th>
        <th>Due Date</th>
        <th>Paid Date</th>
        <th>Amount</th>
        <th>Bank</th>
        <th>Status</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($payments)) {
        $late = ($row['paid_date'] > $row['due_date']) ? true : false;
        $style = $late ? 'style="color: red;"' : '';
    ?>
    <tr <?php echo $style; ?>>
        <td><?php echo $row['year'] . ' ' . $row['make'] . ' ' . $row['model']; ?></td>
        <td><?php echo $row['due_date']; ?></td>
        <td><?php echo $row['paid_date']; ?></td>
        <td>$<?php echo number_format($row['amount'], 2); ?></td>
        <td><?php echo $row['bank_account']; ?></td>
        <td><?php echo $late ? 'LATE' : 'On Time'; ?></td>
    </tr>
    <?php } ?>
</table>
<?php } ?>

</body>
</html>
