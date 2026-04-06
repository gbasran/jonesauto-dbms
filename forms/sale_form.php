<?php
include '../config.php';

// get available cars
$sql = "SELECT vehicle_id, year, make, model, color FROM vehicles WHERE status = 'available' ORDER BY make, model";
$vehicles = mysqli_query($conn, $sql);

// get salespeople
$sql = "SELECT employee_id, first_name, last_name FROM employees WHERE role IN ('salesperson', 'both') ORDER BY last_name";
$salespeople = mysqli_query($conn, $sql);

// get existing customers
$sql = "SELECT customer_id, first_name, last_name FROM customers ORDER BY last_name";
$customers = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sale Form - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="nav">
    <a href="../index.html">Back to Home</a>
</div>

<h2>Sell a Vehicle</h2>

<form method="POST" action="../process/process_sale.php">

<!-- vehicle -->
<h3>Vehicle</h3>
<table>
    <tr>
        <td>Vehicle:</td>
        <td>
            <select name="vehicle_id" required>
                <option value="">-- Select Vehicle --</option>
                <?php while ($row = mysqli_fetch_assoc($vehicles)) { ?>
                    <option value="<?php echo $row['vehicle_id']; ?>"><?php echo $row['year'] . ' ' . $row['make'] . ' ' . $row['model'] . ' - ' . $row['color']; ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
</table>

<!-- sale details -->
<h3>Sale Details</h3>
<table>
    <tr>
        <td>Sale Date:</td>
        <td><input type="date" name="sale_date" required></td>
    </tr>
    <tr>
        <td>Sale Price:</td>
        <td><input type="text" name="sale_price"></td>
    </tr>
    <tr>
        <td>Total Due:</td>
        <td><input type="text" name="total_due"></td>
    </tr>
    <tr>
        <td>Down Payment:</td>
        <td><input type="text" name="down_payment"></td>
    </tr>
    <tr>
        <td>Financed Amount:</td>
        <td><input type="text" name="financed_amount"></td>
    </tr>
</table>

<!-- salesperson -->
<h3>Salesperson</h3>
<table>
    <tr>
        <td>Salesperson:</td>
        <td>
            <select name="employee_id" required>
                <option value="">-- Select Salesperson --</option>
                <?php while ($row = mysqli_fetch_assoc($salespeople)) { ?>
                    <option value="<?php echo $row['employee_id']; ?>"><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>Commission:</td>
        <td><input type="text" name="commission"></td>
    </tr>
</table>

<!-- customer -->
<h3>Customer</h3>
<input type="radio" name="customer_type" value="existing" id="existing_radio" checked onchange="toggleCustomer()">
<label for="existing_radio">Existing Customer</label>
<input type="radio" name="customer_type" value="new" id="new_radio" onchange="toggleCustomer()">
<label for="new_radio">New Customer</label>

<div id="existing_customer">
<table>
    <tr>
        <td>Customer:</td>
        <td>
            <select name="customer_id">
                <option value="">-- Select Customer --</option>
                <?php while ($row = mysqli_fetch_assoc($customers)) { ?>
                    <option value="<?php echo $row['customer_id']; ?>"><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
</table>
</div>

<div id="new_customer" style="display:none">
<table>
    <tr>
        <td>First Name:</td>
        <td><input type="text" name="first_name"></td>
    </tr>
    <tr>
        <td>Last Name:</td>
        <td><input type="text" name="last_name"></td>
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
        <td>State:</td>
        <td><input type="text" name="state" value="AB"></td>
    </tr>
    <tr>
        <td>Zip:</td>
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
</div>

<!-- employment history -->
<h3>Employment History</h3>
<table>
    <tr>
        <th>#</th>
        <th>Employer</th>
        <th>Title</th>
        <th>Supervisor Phone</th>
        <th>Address</th>
        <th>Start Date</th>
    </tr>
    <tr>
        <td>1</td>
        <td><input type="text" name="employer_1"></td>
        <td><input type="text" name="emp_title_1"></td>
        <td><input type="text" name="emp_phone_1"></td>
        <td><input type="text" name="emp_address_1"></td>
        <td><input type="date" name="emp_start_1"></td>
    </tr>
    <tr>
        <td>2</td>
        <td><input type="text" name="employer_2"></td>
        <td><input type="text" name="emp_title_2"></td>
        <td><input type="text" name="emp_phone_2"></td>
        <td><input type="text" name="emp_address_2"></td>
        <td><input type="date" name="emp_start_2"></td>
    </tr>
    <tr>
        <td>3</td>
        <td><input type="text" name="employer_3"></td>
        <td><input type="text" name="emp_title_3"></td>
        <td><input type="text" name="emp_phone_3"></td>
        <td><input type="text" name="emp_address_3"></td>
        <td><input type="date" name="emp_start_3"></td>
    </tr>
</table>

<br>
<input type="submit" value="Record Sale">

</form>

<script>
// toggle between existing and new customer
function toggleCustomer() {
    var existing = document.getElementById('existing_customer');
    var newCust = document.getElementById('new_customer');
    if (document.getElementById('existing_radio').checked) {
        existing.style.display = 'block';
        newCust.style.display = 'none';
    } else {
        existing.style.display = 'none';
        newCust.style.display = 'block';
    }
}
</script>

</body>
</html>
