<?php
include '../config.php';

// check for message from redirect
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

// handle delete
if (isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    mysqli_query($conn, "UPDATE employees SET is_active = 0 WHERE employee_id = $del_id");
    mysqli_query($conn, "INSERT INTO operations_log (table_name, record_id, operation) VALUES ('employees', $del_id, 'delete')");
    header("Location: employee_form.php?msg=deleted");
    exit;
}

// check if we're editing
$edit_id = isset($_GET['edit']) ? $_GET['edit'] : '';
$edit_data = null;
if ($edit_id != '') {
    $sql = "SELECT * FROM employees WHERE employee_id = $edit_id";
    $edit_data = mysqli_fetch_assoc(mysqli_query($conn, $sql));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employees - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php $nav_prefix = '../'; include '../nav.php'; ?>

<h2><?php echo $edit_data ? 'Edit Employee' : 'Add New Employee'; ?></h2>

<?php if ($msg == 'success') { ?>
    <div class="success">Employee added!</div>
<?php } else if ($msg == 'updated') { ?>
    <div class="success">Employee updated!</div>
<?php } else if ($msg == 'deleted') { ?>
    <div class="success">Employee removed.</div>
<?php } else if ($msg == 'error') { ?>
    <div class="error">Something went wrong.</div>
<?php } ?>

<form method="POST" action="../process/process_employee.php">
<?php if ($edit_data) { ?>
    <input type="hidden" name="employee_id" value="<?php echo $edit_data['employee_id']; ?>">
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
        <td>Role:</td>
        <td>
            <select name="role">
                <option value="salesperson" <?php echo ($edit_data && $edit_data['role'] == 'salesperson') ? 'selected' : ''; ?>>salesperson</option>
                <option value="buyer" <?php echo ($edit_data && $edit_data['role'] == 'buyer') ? 'selected' : ''; ?>>buyer</option>
                <option value="both" <?php echo ($edit_data && $edit_data['role'] == 'both') ? 'selected' : ''; ?>>both</option>
            </select>
        </td>
    </tr>
</table>
<br>
<input type="submit" value="<?php echo $edit_data ? 'Update Employee' : 'Add Employee'; ?>">
<?php if ($edit_data) { ?>
    <a href="employee_form.php" style="margin-left:10px;">Cancel</a>
<?php } ?>
</form>

<hr>

<!-- show all employees -->
<h2>Current Employees</h2>
<?php
// TODO: maybe add search or filter later
$sql = "SELECT * FROM employees WHERE is_active = 1 ORDER BY role, last_name";
$result = mysqli_query($conn, $sql);
?>

<table>
    <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Phone</th>
        <th>Role</th>
        <th>Action</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?php echo $row['employee_id']; ?></td>
        <td><?php echo $row['first_name']; ?></td>
        <td><?php echo $row['last_name']; ?></td>
        <td><?php echo $row['phone']; ?></td>
        <td><?php echo $row['role']; ?></td>
        <td>
            <a href="employee_form.php?edit=<?php echo $row['employee_id']; ?>">Edit</a> |
            <a href="employee_form.php?delete=<?php echo $row['employee_id']; ?>" onclick="return confirm('Delete this employee?')">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
