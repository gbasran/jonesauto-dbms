<?php
include '../config.php';

// check for message from redirect
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employees - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="nav">
    <a href="../index.html">Back to Home</a>
</div>

<h2>Add New Employee</h2>

<?php if ($msg == 'success') { ?>
    <div class="success">Employee added!</div>
<?php } else if ($msg == 'error') { ?>
    <div class="error">Something went wrong.</div>
<?php } ?>

<form method="POST" action="../process/process_employee.php">
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
        <td>Role:</td>
        <td>
            <select name="role">
                <option value="salesperson">salesperson</option>
                <option value="buyer">buyer</option>
                <option value="both">both</option>
            </select>
        </td>
    </tr>
</table>
<br>
<input type="submit" value="Add Employee">
</form>

<hr>

<!-- show all employees -->
<h2>Current Employees</h2>
<?php
// TODO: maybe add search or filter later
$sql = "SELECT * FROM employees ORDER BY role, last_name";
$result = mysqli_query($conn, $sql);
?>

<table>
    <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Phone</th>
        <th>Role</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['employee_id']; ?></td>
        <td><?php echo $row['first_name']; ?></td>
        <td><?php echo $row['last_name']; ?></td>
        <td><?php echo $row['phone']; ?></td>
        <td><?php echo $row['role']; ?></td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
