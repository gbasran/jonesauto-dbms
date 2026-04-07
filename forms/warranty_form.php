<?php
include '../config.php';

// get all sales with customer and vehicle info
$sql = "SELECT s.sale_id, s.sale_date, s.vehicle_id, s.customer_id, s.employee_id, c.first_name, c.last_name, v.year, v.make, v.model FROM sales s JOIN customers c ON s.customer_id = c.customer_id JOIN vehicles v ON s.vehicle_id = v.vehicle_id WHERE s.is_active = 1 ORDER BY s.sale_date DESC";
$sales = mysqli_query($conn, $sql);

// check if a sale was selected
$selected_sale = isset($_GET['sale_id']) ? $_GET['sale_id'] : '';

// check for success message
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Warranty Form - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php $nav_prefix = '../'; include '../nav.php'; ?>

<h2>Add Warranty</h2>

<?php if ($msg == 'success') { ?>
    <div class="success">Warranty added!</div>
<?php } else if ($msg == 'error') { ?>
    <div class="error">Please fill in all required fields.</div>
<?php } ?>

<!-- sale selection -->
<table class="form-table">
    <tr>
        <td>Sale:</td>
        <td>
            <select onchange="window.location='warranty_form.php?sale_id='+this.value">
                <option value="">-- Select Sale --</option>
                <?php while ($row = mysqli_fetch_assoc($sales)) { ?>
                    <option value="<?php echo $row['sale_id']; ?>" <?php echo ($selected_sale == $row['sale_id']) ? 'selected' : ''; ?>>
                        <?php echo $row['first_name'] . ' ' . $row['last_name'] . ' - ' . $row['year'] . ' ' . $row['make'] . ' ' . $row['model'] . ' - ' . $row['sale_date']; ?>
                    </option>
                <?php } ?>
            </select>
        </td>
    </tr>
</table>

<?php if ($selected_sale != '') {
    // grab sale info for hidden fields
    $sql = "SELECT vehicle_id, customer_id, employee_id FROM sales WHERE sale_id = $selected_sale";
    $sale_info = mysqli_fetch_assoc(mysqli_query($conn, $sql));
?>

<form method="POST" action="../process/process_warranty.php">

<input type="hidden" name="sale_id" value="<?php echo $selected_sale; ?>">
<input type="hidden" name="vehicle_id" value="<?php echo $sale_info['vehicle_id']; ?>">
<input type="hidden" name="customer_id" value="<?php echo $sale_info['customer_id']; ?>">
<input type="hidden" name="employee_id" value="<?php echo $sale_info['employee_id']; ?>">

<!-- warranty info -->
<h3>Warranty Details</h3>
<table class="form-table">
    <tr>
        <td>Warranty Date:</td>
        <td><input type="date" name="warranty_sale_date" required></td>
    </tr>
    <tr>
        <td>Total Cost:</td>
        <td><input type="number" name="total_cost" step="0.01" min="0"></td>
    </tr>
    <tr>
        <td>Monthly Cost:</td>
        <td><input type="number" name="monthly_cost" step="0.01" min="0"></td>
    </tr>
</table>

<!-- warranty items -->
<h3>Warranty Items</h3>
<table id="items_table">
    <tr>
        <th>#</th>
        <th>Type</th>
        <th>Start Date</th>
        <th>Length (months)</th>
        <th>Cost</th>
        <th>Deductible</th>
        <th>Items Covered</th>
    </tr>
    <tr>
        <td>1</td>
        <td>
            <select name="warranty_type_1">
                <option value="">--</option>
                <option value="Drive-Train">Drive-Train</option>
                <option value="Exterior">Exterior</option>
                <option value="Interior">Interior</option>
                <option value="Electrical">Electrical</option>
            </select>
        </td>
        <td><input type="date" name="item_start_1"></td>
        <td><input type="number" name="item_months_1"></td>
        <td><input type="text" name="item_cost_1"></td>
        <td><input type="text" name="item_deductible_1"></td>
        <td><textarea name="items_covered_1" rows="2" cols="20"></textarea></td>
    </tr>
</table>

<br>
<button type="button" onclick="addItem()">Add Warranty Item</button>
<br><br>
<input type="submit" value="Add Warranty">

</form>

<?php
    // show existing warranties for this sale
    $sql = "SELECT w.warranty_id, w.warranty_sale_date, w.total_cost, w.monthly_cost FROM warranties w WHERE w.sale_id = $selected_sale ORDER BY w.warranty_sale_date";
    $warranties = mysqli_query($conn, $sql);
?>

<h3>Existing Warranties</h3>

<?php if (mysqli_num_rows($warranties) == 0) { ?>
    <p>No warranties for this sale yet.</p>
<?php } else {
    while ($w = mysqli_fetch_assoc($warranties)) {
        // get items for this warranty
        $wid = $w['warranty_id'];
        $sql = "SELECT * FROM warranty_items WHERE warranty_id = $wid";
        $items = mysqli_query($conn, $sql);
?>
    <p><strong>Warranty #<?php echo $wid; ?></strong> - Date: <?php echo $w['warranty_sale_date']; ?> | Total: $<?php echo number_format($w['total_cost'], 2); ?> | Monthly: $<?php echo number_format($w['monthly_cost'], 2); ?></p>
    <table>
        <tr>
            <th>Type</th>
            <th>Start Date</th>
            <th>Length</th>
            <th>Cost</th>
            <th>Deductible</th>
            <th>Items Covered</th>
        </tr>
        <?php while ($item = mysqli_fetch_assoc($items)) { ?>
        <tr>
            <td><?php echo $item['warranty_type']; ?></td>
            <td><?php echo $item['start_date']; ?></td>
            <td><?php echo $item['length_months']; ?> months</td>
            <td>$<?php echo number_format($item['cost'], 2); ?></td>
            <td>$<?php echo number_format($item['deductible'], 2); ?></td>
            <td><?php echo $item['items_covered']; ?></td>
        </tr>
        <?php } ?>
    </table>
<?php }
} ?>

<?php } ?>

<script>
// track how many item rows we have
var itemCount = 1;

function addItem() {
    itemCount++;
    var table = document.getElementById('items_table');
    var row = table.insertRow(-1);

    row.innerHTML = '<td>' + itemCount + '</td>' +
        '<td><select name="warranty_type_' + itemCount + '">' +
            '<option value="">--</option>' +
            '<option value="Drive-Train">Drive-Train</option>' +
            '<option value="Exterior">Exterior</option>' +
            '<option value="Interior">Interior</option>' +
            '<option value="Electrical">Electrical</option>' +
        '</select></td>' +
        '<td><input type="date" name="item_start_' + itemCount + '"></td>' +
        '<td><input type="number" name="item_months_' + itemCount + '"></td>' +
        '<td><input type="text" name="item_cost_' + itemCount + '"></td>' +
        '<td><input type="text" name="item_deductible_' + itemCount + '"></td>' +
        '<td><textarea name="items_covered_' + itemCount + '" rows="2" cols="20"></textarea></td>';
}
</script>

</body>
</html>
