<?php
include '../config.php';

// get available cars with cost info
$sql = "SELECT v.vehicle_id, v.year, v.make, v.model, v.color, v.book_price, v.miles, v.condition_desc, COALESCE(p.price_paid, 0) as price_paid, COALESCE(SUM(r.actual_cost), 0) as total_repairs FROM vehicles v LEFT JOIN purchases p ON v.vehicle_id = p.vehicle_id LEFT JOIN repairs r ON p.purchase_id = r.purchase_id WHERE v.status = 'available' AND v.is_active = 1 GROUP BY v.vehicle_id, v.year, v.make, v.model, v.color, v.book_price, v.miles, v.condition_desc, p.price_paid ORDER BY v.make, v.model";
$vehicles = mysqli_query($conn, $sql);

// get salespeople
$sql = "SELECT employee_id, first_name, last_name FROM employees WHERE role IN ('salesperson', 'both') AND is_active = 1 ORDER BY last_name";
$salespeople = mysqli_query($conn, $sql);

// get existing customers with payment history
$sql = "SELECT customer_id, first_name, last_name, phone, num_late_payments, avg_days_late FROM customers WHERE is_active = 1 ORDER BY last_name";
$customers = mysqli_query($conn, $sql);

// check if a vehicle was pre-selected
$preselect = isset($_GET['vehicle_id']) ? $_GET['vehicle_id'] : '';
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sale Form - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php $nav_prefix = '../'; include '../nav.php'; ?>

<h2>Sell a Vehicle</h2>

<?php if ($msg == 'error') { ?>
    <div class="error">Please fill in all required fields.</div>
<?php } ?>

<form method="POST" action="../process/process_sale.php">

<!-- vehicle -->
<h3>Vehicle</h3>
<table class="form-table">
    <tr>
        <td>Vehicle:</td>
        <td>
            <select name="vehicle_id" id="vehicle_select" onchange="showVehicleDetails()" required>
                <option value="">-- Select Vehicle --</option>
                <?php while ($row = mysqli_fetch_assoc($vehicles)) {
                    $sel = ($row['vehicle_id'] == $preselect) ? 'selected' : '';
                    $paid = $row['price_paid'] != '' ? $row['price_paid'] : 0;
                    $repairs = $row['total_repairs'];
                    $book = $row['book_price'] != '' ? $row['book_price'] : 0;
                    $miles = $row['miles'] != '' ? $row['miles'] : 0;
                    $cond = $row['condition_desc'] != '' ? $row['condition_desc'] : 'N/A';
                ?>
                    <option value="<?php echo $row['vehicle_id']; ?>" data-book="<?php echo $book; ?>" data-miles="<?php echo $miles; ?>" data-condition="<?php echo htmlspecialchars($cond); ?>" data-paid="<?php echo $paid; ?>" data-repairs="<?php echo $repairs; ?>" <?php echo $sel; ?>><?php echo $row['year'] . ' ' . $row['make'] . ' ' . $row['model'] . ' - ' . $row['color']; ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
</table>

<div id="vehicle_info" style="display:none; background:#d4edda; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:3px;">
    <strong>Vehicle Details:</strong><br>
    Book Price: $<span id="v_book"></span> | Miles: <span id="v_miles"></span> | Condition: <span id="v_condition"></span><br>
    We Paid: $<span id="v_paid"></span> | Repairs: $<span id="v_repairs"></span> | Total Investment: $<span id="v_total"></span>
</div>

<!-- sale details -->
<h3>Sale Details</h3>
<table class="form-table">
    <tr>
        <td>Sale Date:</td>
        <td><input type="date" name="sale_date" required></td>
    </tr>
    <tr>
        <td>Sale Price:</td>
        <td><input type="number" name="sale_price" step="0.01" min="0"></td>
    </tr>
    <tr>
        <td>Total Due:</td>
        <td><input type="number" name="total_due" step="0.01" min="0"></td>
    </tr>
    <tr>
        <td>Down Payment:</td>
        <td><input type="number" name="down_payment" step="0.01" min="0"></td>
    </tr>
    <tr>
        <td>Financed Amount:</td>
        <td><input type="number" name="financed_amount" step="0.01" min="0"></td>
    </tr>
</table>

<!-- salesperson -->
<h3>Salesperson</h3>
<table class="form-table">
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
        <td><input type="number" name="commission" step="0.01" min="0"></td>
    </tr>
</table>

<!-- customer -->
<h3>Customer</h3>
<input type="radio" name="customer_type" value="existing" id="existing_radio" checked onchange="toggleCustomer()">
<label for="existing_radio">Existing Customer</label>
<input type="radio" name="customer_type" value="new" id="new_radio" onchange="toggleCustomer()">
<label for="new_radio">New Customer</label>

<div id="existing_customer">
<table class="form-table">
    <tr>
        <td>Customer:</td>
        <td>
            <select name="customer_id" id="customer_select" onchange="showCustomerInfo()">
                <option value="">-- Select Customer --</option>
                <?php while ($row = mysqli_fetch_assoc($customers)) {
                    $phone = $row['phone'] != '' ? $row['phone'] : '';
                    $late = $row['num_late_payments'] != '' ? $row['num_late_payments'] : 0;
                    $avglate = $row['avg_days_late'] != '' ? $row['avg_days_late'] : 0;
                ?>
                    <option value="<?php echo $row['customer_id']; ?>" data-phone="<?php echo htmlspecialchars($phone); ?>" data-late="<?php echo $late; ?>" data-avglate="<?php echo $avglate; ?>"><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
</table>
</div>

<div id="customer_info" style="display:none; background:#fff3cd; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:3px;">
    <strong>Customer Info:</strong> Phone: <span id="c_phone"></span> | Late Payments: <span id="c_late" style="color:red;"></span> | Avg Days Late: <span id="c_avglate"></span>
</div>

<div id="new_customer" style="display:none">
<table class="form-table">
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
<table id="emp_table">
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
</table>
<button type="button" onclick="addEmployer()">Add Employer</button>

<br><br>
<input type="submit" value="Record Sale">

</form>

<script>
// toggle between existing and new customer
function toggleCustomer() {
    var existing = document.getElementById('existing_customer');
    var newCust = document.getElementById('new_customer');
    var info = document.getElementById('customer_info');
    if (document.getElementById('existing_radio').checked) {
        existing.style.display = 'block';
        newCust.style.display = 'none';
    } else {
        existing.style.display = 'none';
        newCust.style.display = 'block';
        info.style.display = 'none';
    }
}

// show vehicle details when one is picked
function showVehicleDetails() {
    var sel = document.getElementById('vehicle_select');
    var opt = sel.options[sel.selectedIndex];
    var box = document.getElementById('vehicle_info');
    if (sel.value == '') {
        box.style.display = 'none';
        return;
    }
    var paid = parseFloat(opt.getAttribute('data-paid')) || 0;
    var repairs = parseFloat(opt.getAttribute('data-repairs')) || 0;
    document.getElementById('v_book').textContent = opt.getAttribute('data-book');
    document.getElementById('v_miles').textContent = opt.getAttribute('data-miles');
    document.getElementById('v_condition').textContent = opt.getAttribute('data-condition');
    document.getElementById('v_paid').textContent = paid.toFixed(2);
    document.getElementById('v_repairs').textContent = repairs.toFixed(2);
    document.getElementById('v_total').textContent = (paid + repairs).toFixed(2);
    box.style.display = 'block';
}

// show customer payment history
function showCustomerInfo() {
    var sel = document.getElementById('customer_select');
    var opt = sel.options[sel.selectedIndex];
    var box = document.getElementById('customer_info');
    if (sel.value == '') {
        box.style.display = 'none';
        return;
    }
    document.getElementById('c_phone').textContent = opt.getAttribute('data-phone');
    document.getElementById('c_late').textContent = opt.getAttribute('data-late');
    document.getElementById('c_avglate').textContent = opt.getAttribute('data-avglate');
    box.style.display = 'block';
}

// add another employer row
var empCount = 1;
function addEmployer() {
    empCount++;
    var table = document.getElementById('emp_table');
    var row = table.insertRow(-1);
    row.innerHTML = '<td>' + empCount + '</td>' +
        '<td><input type="text" name="employer_' + empCount + '"></td>' +
        '<td><input type="text" name="emp_title_' + empCount + '"></td>' +
        '<td><input type="text" name="emp_phone_' + empCount + '"></td>' +
        '<td><input type="text" name="emp_address_' + empCount + '"></td>' +
        '<td><input type="date" name="emp_start_' + empCount + '"></td>';
}

// if vehicle was pre-selected, show its details
window.onload = function() {
    if (document.getElementById('vehicle_select').value != '') {
        showVehicleDetails();
    }
}
</script>

</body>
</html>
