<?php
// figure out path prefix based on where we are
$nav_prefix = isset($nav_prefix) ? $nav_prefix : '';
?>
<div class="nav">
    <span class="site-title">JonesAuto</span>
    <a href="<?php echo $nav_prefix; ?>index.php">Home</a>
    <div class="dropdown">
        <a class="dropdown-btn">Forms</a>
        <div class="dropdown-content">
            <a href="<?php echo $nav_prefix; ?>forms/purchase_form.php">Purchase</a>
            <a href="<?php echo $nav_prefix; ?>forms/sale_form.php">Sale</a>
            <a href="<?php echo $nav_prefix; ?>forms/warranty_form.php">Warranty</a>
            <a href="<?php echo $nav_prefix; ?>forms/payment_form.php">Payment</a>
            <a href="<?php echo $nav_prefix; ?>forms/customer_form.php">Customer</a>
            <a href="<?php echo $nav_prefix; ?>forms/employee_form.php">Employee</a>
        </div>
    </div>
    <div class="dropdown">
        <a class="dropdown-btn">Reports</a>
        <div class="dropdown-content">
            <a href="<?php echo $nav_prefix; ?>reports/inventory.php">Inventory</a>
            <a href="<?php echo $nav_prefix; ?>reports/sales_report.php">Sales</a>
            <a href="<?php echo $nav_prefix; ?>reports/payment_history.php">Payment History</a>
            <a href="<?php echo $nav_prefix; ?>reports/repair_summary.php">Repair Costs</a>
            <a href="<?php echo $nav_prefix; ?>reports/warranty_report.php">Warranties</a>
            <a href="<?php echo $nav_prefix; ?>reports/late_payments.php">Late Payments</a>
        </div>
    </div>
    <a href="<?php echo $nav_prefix; ?>queries/queries.php">Queries</a>
</div>
