<?php
include '../config.php';

// check for message from redirect
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customers - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="nav">
    <a href="../index.html">Back to Home</a>
</div>

<h2>Add New Customer</h2>

<?php if ($msg == 'success') { ?>
    <div class="success">Customer added!</div>
<?php } else if ($msg == 'error') { ?>
    <div class="error">Something went wrong.</div>
<?php } ?>

<form method="POST" action="../process/process_customer.php">
<table>
    <tr>
        <td>First Name:</td>
        <td><input type="text" name="first_name" required></td>
    </tr>
    <tr>
        <td>Last Name:</td>
        <td><input type="text" name="last_name" required></td>
    </tr>
    <tr>
        <td>Phone:</td>
        <td><input type="text" name="phone"></td>
    </tr>
    <tr>
        <td>Address:</td>
        <td><input type="text" name="address"></td>
    </tr>
    <tr>
        <td>City:</td>
        <td><input type="text" name="city"></td>
    </tr>
    <tr>
        <td>State/Province:</td>
        <td><input type="text" name="state" value="AB"></td>
    </tr>
    <tr>
        <td>Zip/Postal:</td>
        <td><input type="text" name="zip"></td>
    </tr>
    <tr>
        <td>Gender:</td>
        <td>
            <select name="gender">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>Date of Birth:</td>
        <td><input type="date" name="dob"></td>
    </tr>
</table>
<br>
<input type="submit" value="Add Customer">
</form>

<hr>

<!-- show all customers -->
<h2>Existing Customers</h2>
<?php
$sql = "SELECT * FROM customers ORDER BY last_name";
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
    </tr>
    <?php } ?>
</table>

</body>
</html>
