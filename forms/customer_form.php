<?php
include '../config.php';

// check for message from redirect
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

// handle delete
if (isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    mysqli_query($conn, "UPDATE customers SET is_active = 0 WHERE customer_id = $del_id");
    mysqli_query($conn, "INSERT INTO operations_log (table_name, record_id, operation) VALUES ('customers', $del_id, 'delete')");
    header("Location: customer_form.php?msg=deleted");
    exit;
}

// check if we're editing
$edit_id = isset($_GET['edit']) ? $_GET['edit'] : '';
$edit_data = null;
if ($edit_id != '') {
    $sql = "SELECT * FROM customers WHERE customer_id = $edit_id";
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, $sql));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customers - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php $nav_prefix = '../'; include '../nav.php'; ?>

<h2><?php echo $edit_data ? 'Edit Customer' : 'Add New Customer'; ?></h2>

<?php if ($msg == 'success') { ?>
    <div class="success">Customer added!</div>
<?php } else if ($msg == 'updated') { ?>
    <div class="success">Customer updated!</div>
<?php } else if ($msg == 'deleted') { ?>
    <div class="success">Customer removed.</div>
<?php } else if ($msg == 'error') { ?>
    <div class="error">Something went wrong.</div>
<?php } ?>

<form method="POST" action="../process/process_customer.php">
<?php if ($edit_data) { ?>
    <input type="hidden" name="customer_id" value="<?php echo $edit_data['customer_id']; ?>">
<?php } ?>
<table class="form-table">
    <tr>
        <td>First Name:</td>
        <td><input type="text" name="first_name" value="<?php echo $edit_data ? $edit_data['first_name'] : ''; ?>" required></td>
    </tr>
    <tr>
        <td>Last Name:</td>
        <td><input type="text" name="last_name" value="<?php echo $edit_data ? $edit_data['last_name'] : ''; ?>" required></td>
    </tr>
    <tr>
        <td>Phone:</td>
        <td><input type="text" name="phone" value="<?php echo $edit_data ? $edit_data['phone'] : ''; ?>" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" placeholder="403-555-1234"></td>
    </tr>
    <tr>
        <td>Address:</td>
        <td><input type="text" name="address" value="<?php echo $edit_data ? $edit_data['address'] : ''; ?>"></td>
    </tr>
    <tr>
        <td>City:</td>
        <td><input type="text" name="city" value="<?php echo $edit_data ? $edit_data['city'] : ''; ?>"></td>
    </tr>
    <tr>
        <td>State/Province:</td>
        <td><input type="text" name="state" value="<?php echo $edit_data ? $edit_data['state'] : 'AB'; ?>"></td>
    </tr>
    <tr>
        <td>Zip/Postal:</td>
        <td><input type="text" name="zip" value="<?php echo $edit_data ? $edit_data['zip'] : ''; ?>"></td>
    </tr>
    <tr>
        <td>Gender:</td>
        <td>
            <select name="gender">
                <option value="Male" <?php echo ($edit_data && $edit_data['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($edit_data && $edit_data['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>Date of Birth:</td>
        <td><input type="date" name="dob" value="<?php echo $edit_data ? $edit_data['dob'] : ''; ?>" required></td>
    </tr>
</table>
<br>
<input type="submit" value="<?php echo $edit_data ? 'Update Customer' : 'Add Customer'; ?>">
<?php if ($edit_data) { ?>
    <a href="customer_form.php" style="margin-left:10px;">Cancel</a>
<?php } ?>
</form>

<?php if ($edit_data) { ?>
<hr>
<h3>Employment History</h3>
<?php
$sql = "SELECT * FROM employment_history WHERE customer_id = $edit_id ORDER BY start_date DESC";
$emp_hist = mysqli_query($conn, $sql);
$has_history = mysqli_num_rows($emp_hist) > 0;
?>

<?php if ($has_history) { ?>
<table>
    <tr>
        <th>Employer</th>
        <th>Title</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Start Date</th>
    </tr>
    <?php while ($eh = mysqli_fetch_assoc($emp_hist)) { ?>
    <tr>
        <td><?php echo $eh['employer']; ?></td>
        <td><?php echo $eh['title']; ?></td>
        <td><?php echo $eh['supervisor_phone']; ?></td>
        <td><?php echo $eh['employer_address']; ?></td>
        <td><?php echo $eh['start_date']; ?></td>
    </tr>
    <?php } ?>
</table>
<?php } else { ?>
<p>No employment history recorded for this customer.</p>
<?php } ?>

<!-- add new employment record -->
<h4>Add Employment Record</h4>
<form method="POST" action="../process/process_customer.php">
    <input type="hidden" name="customer_id" value="<?php echo $edit_id; ?>">
    <input type="hidden" name="add_employment" value="1">
    <table class="form-table">
        <tr>
            <td>Employer:</td>
            <td><input type="text" name="employer" required></td>
        </tr>
        <tr>
            <td>Title:</td>
            <td><input type="text" name="emp_title"></td>
        </tr>
        <tr>
            <td>Supervisor Phone:</td>
            <td><input type="text" name="emp_phone"></td>
        </tr>
        <tr>
            <td>Address:</td>
            <td><input type="text" name="emp_address"></td>
        </tr>
        <tr>
            <td>Start Date:</td>
            <td><input type="date" name="emp_start"></td>
        </tr>
    </table>
    <br>
    <input type="submit" value="Add Employment Record">
</form>
<?php } ?>

<hr>

<!-- show all customers -->
<h2>Existing Customers</h2>
<?php
$sql = "SELECT * FROM customers WHERE is_active = 1 ORDER BY last_name";
$result = mysqli_query($conn, $sql);
?>

<table>
    <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Phone</th>
        <th>City</th>
        <th>State</th>
        <th>Late Payments</th>
        <th>Avg Days Late</th>
        <th>Action</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['customer_id']; ?></td>
        <td><?php echo $row['first_name']; ?></td>
        <td><?php echo $row['last_name']; ?></td>
        <td><?php echo $row['phone']; ?></td>
        <td><?php echo $row['city']; ?></td>
        <td><?php echo $row['state']; ?></td>
        <td><?php echo $row['num_late_payments']; ?></td>
        <td><?php echo $row['avg_days_late']; ?></td>
        <td>
            <a href="customer_form.php?edit=<?php echo $row['customer_id']; ?>">Edit</a> |
            <a href="customer_form.php?delete=<?php echo $row['customer_id']; ?>" onclick="return confirm('Delete this customer?')">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
