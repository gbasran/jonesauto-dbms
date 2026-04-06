<?php
include 'config.php';

// grab some stats
$cars = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM vehicles WHERE status = 'available'");
$car_count = mysqli_fetch_assoc($cars)['cnt'];

$custs = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM customers");
$cust_count = mysqli_fetch_assoc($custs)['cnt'];

$sls = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM sales");
$sale_count = mysqli_fetch_assoc($sls)['cnt'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>JonesAuto DBMS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php $nav_prefix = ''; include 'nav.php'; ?>

<div class="container">

<h1 style="text-align: center;">Lethbridge JonesAuto</h1>
<p style="text-align: center;">Used Car Dealership Management System</p>

<div class="stats-row">
    <div class="stat-card">
        <h3><?php echo $car_count; ?></h3>
        <p>Cars Available</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $cust_count; ?></h3>
        <p>Customers</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $sale_count; ?></h3>
        <p>Sales</p>
    </div>
</div>

<div class="two-col">
    <div>
        <h2>Input Forms</h2>
        <table>
            <tr>
                <th>Form</th>
                <th>Description</th>
            </tr>
            <tr>
                <td><a href="forms/purchase_form.php">Purchase Form</a></td>
                <td>Record a car bought at auction</td>
            </tr>
            <tr>
                <td><a href="forms/sale_form.php">Sale Form</a></td>
                <td>Sell a car to a customer</td>
            </tr>
            <tr>
                <td><a href="forms/warranty_form.php">Warranty Form</a></td>
                <td>Add warranties to a sale</td>
            </tr>
            <tr>
                <td><a href="forms/payment_form.php">Payment Form</a></td>
                <td>Record customer payments</td>
            </tr>
            <tr>
                <td><a href="forms/customer_form.php">Customer Form</a></td>
                <td>Add or view customers</td>
            </tr>
            <tr>
                <td><a href="forms/employee_form.php">Employee Form</a></td>
                <td>Add or view employees</td>
            </tr>
        </table>
    </div>

    <div>
        <h2>Reports</h2>
        <table>
            <tr>
                <th>Report</th>
                <th>Description</th>
            </tr>
            <tr>
                <td><a href="reports/inventory.php">Current Inventory</a></td>
                <td>Cars available on the lot</td>
            </tr>
            <tr>
                <td><a href="reports/sales_report.php">Sales Summary</a></td>
                <td>Sales and profit info</td>
            </tr>
            <tr>
                <td><a href="reports/payment_history.php">Payment History</a></td>
                <td>Customer payment records</td>
            </tr>
            <tr>
                <td><a href="reports/repair_summary.php">Repair Costs</a></td>
                <td>Estimated vs actual repair costs</td>
            </tr>
            <tr>
                <td><a href="reports/warranty_report.php">Active Warranties</a></td>
                <td>Warranties and expiry dates</td>
            </tr>
            <tr>
                <td><a href="reports/late_payments.php">Late Payments</a></td>
                <td>Customers with overdue payments</td>
            </tr>
        </table>
    </div>
</div>

<h2>Queries</h2>
<p><a href="queries/queries.php">Business Queries</a> - Run predefined queries on the database</p>

<div class="footer">CPSC 3660 Course Project</div>

</div>

</body>
</html>
