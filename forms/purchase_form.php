<?php
include '../config.php';

// grab the buyers from db
$sql = "SELECT employee_id, first_name, last_name FROM employees WHERE role IN ('buyer', 'both') AND is_active = 1 ORDER BY last_name";
$buyers = mysqli_query($conn, $sql);

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Purchase Form - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php $nav_prefix = '../'; include '../nav.php'; ?>

<h2>Record a Vehicle Purchase</h2>

<?php if ($msg == 'error') { ?>
    <div class="error">Please fill in all required fields.</div>
<?php } ?>

<form method="POST" action="../process/process_purchase.php">

<!-- purchase info -->
<h3>Purchase Info</h3>
<table class="form-table">
    <tr>
        <td>Purchase Date:</td>
        <td><input type="date" name="purchase_date" required></td>
    </tr>
    <tr>
        <td>Location:</td>
        <td><input type="text" name="location"></td>
    </tr>
    <tr>
        <td>Seller/Dealer:</td>
        <td><input type="text" name="seller_dealer"></td>
    </tr>
    <tr>
        <td>Auction?</td>
        <td><input type="checkbox" name="is_auction" value="1" checked></td>
    </tr>
    <tr>
        <td>Buyer:</td>
        <td>
            <select name="buyer">
                <?php while ($row = mysqli_fetch_assoc($buyers)) { ?>
                    <option value="<?php echo $row['employee_id']; ?>"><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
</table>

<!-- vehicle info -->
<h3>Vehicle Info</h3>
<table class="form-table">
    <tr>
        <td>Make:</td>
        <td><input type="text" name="make" required></td>
    </tr>
    <tr>
        <td>Model:</td>
        <td><input type="text" name="model" required></td>
    </tr>
    <tr>
        <td>Year:</td>
        <td><input type="number" name="year" required min="1990" max="2027"></td>
    </tr>
    <tr>
        <td>Color:</td>
        <td><input type="text" name="color"></td>
    </tr>
    <tr>
        <td>Miles:</td>
        <td><input type="number" name="miles" min="0"></td>
    </tr>
    <tr>
        <td>Condition:</td>
        <td>
            <select name="condition_desc">
                <option value="Excellent">Excellent</option>
                <option value="Good" selected>Good</option>
                <option value="Fair">Fair</option>
                <option value="Poor">Poor</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>Book Price:</td>
        <td><input type="number" name="book_price" step="0.01" min="0"></td>
    </tr>
    <tr>
        <td>Price Paid:</td>
        <td><input type="number" name="price_paid" step="0.01" min="0" required></td>
    </tr>
    <tr>
        <td>Style:</td>
        <td>
            <select name="style">
                <option value="Sedan">Sedan</option>
                <option value="SUV">SUV</option>
                <option value="Truck">Truck</option>
                <option value="Van">Van</option>
                <option value="Coupe">Coupe</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>Interior Color:</td>
        <td><input type="text" name="interior_color"></td>
    </tr>
</table>

<!-- repair rows -->
<h3>Repair Problems</h3>
<table id="repair_table">
    <tr>
        <th>#</th>
        <th>Description</th>
        <th>Est. Cost</th>
        <th>Actual Cost</th>
    </tr>
    <tr>
        <td>1</td>
        <td><input type="text" name="repair_desc_1"></td>
        <td><input type="text" name="repair_est_1"></td>
        <td><input type="text" name="repair_actual_1"></td>
    </tr>
</table>
<button type="button" onclick="addRepair()">+ Add Repair</button>

<br><br>
<input type="submit" value="Record Purchase">

</form>

<hr>

<!-- recent purchases for reference -->
<h3>Recent Purchases</h3>
<?php
$sql = "SELECT v.year, v.make, v.model, v.color, v.status, p.purchase_date, p.price_paid, p.location FROM purchases p JOIN vehicles v ON p.vehicle_id = v.vehicle_id ORDER BY p.purchase_date DESC LIMIT 10";
$recent = mysqli_query($conn, $sql);
?>
<table>
    <tr>
        <th>Date</th>
        <th>Vehicle</th>
        <th>Color</th>
        <th>Price Paid</th>
        <th>Location</th>
        <th>Status</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($recent)) { ?>
    <tr>
        <td><?php echo $row['purchase_date']; ?></td>
        <td><?php echo $row['year'] . ' ' . $row['make'] . ' ' . $row['model']; ?></td>
        <td><?php echo $row['color']; ?></td>
        <td>$<?php echo number_format($row['price_paid'], 2); ?></td>
        <td><?php echo $row['location']; ?></td>
        <td><?php echo $row['status']; ?></td>
    </tr>
    <?php } ?>
</table>

<script>
var repairCount = 1;
function addRepair() {
    repairCount++;
    var table = document.getElementById('repair_table');
    var row = table.insertRow(-1);
    row.innerHTML = '<td>' + repairCount + '</td>' +
        '<td><input type="text" name="repair_desc_' + repairCount + '"></td>' +
        '<td><input type="text" name="repair_est_' + repairCount + '"></td>' +
        '<td><input type="text" name="repair_actual_' + repairCount + '"></td>';
}
</script>

</body>
</html>
