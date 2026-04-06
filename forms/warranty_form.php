<?php
include '../config.php';

// get all sales with customer and vehicle info
$sql = "SELECT s.sale_id, s.sale_date, s.vehicle_id, s.customer_id, s.employee_id, c.first_name, c.last_name, v.year, v.make, v.model FROM sales s JOIN customers c ON s.customer_id = c.customer_id JOIN vehicles v ON s.vehicle_id = v.vehicle_id ORDER BY s.sale_date DESC";
$sales = mysqli_query($conn, $sql);

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

<div class="nav">
    <a href="../index.html">Back to Home</a>
</div>

<h2>Add Warranty</h2>

<?php if ($msg == 'success') { ?>
    <div class="success">Warranty added!</div>
<?php } ?>

<form method="POST" action="../process/process_warranty.php">

<!-- sale selection -->
<table>
    <tr>
        <td>Sale:</td>
        <td>
            <select name="sale_id" id="sale_select" onchange="fillSaleInfo()" required>
                <option value="">-- Select Sale --</option>
                <?php while ($row = mysqli_fetch_assoc($sales)) { ?>
                    <option value="<?php echo $row['sale_id']; ?>"
                            data-vehicle="<?php echo $row['vehicle_id']; ?>"
                            data-customer="<?php echo $row['customer_id']; ?>"
                            data-employee="<?php echo $row['employee_id']; ?>">
                        <?php echo $row['first_name'] . ' ' . $row['last_name'] . ' - ' . $row['year'] . ' ' . $row['make'] . ' ' . $row['model'] . ' - ' . $row['sale_date']; ?>
                    </option>
                <?php } ?>
            </select>
        </td>
    </tr>
</table>

<input type="hidden" name="vehicle_id" id="vehicle_id">
<input type="hidden" name="customer_id" id="customer_id">
<input type="hidden" name="employee_id" id="employee_id">

<!-- warranty info -->
<h3>Warranty Details</h3>
<table>
    <tr>
        <td>Warranty Date:</td>
        <td><input type="date" name="warranty_sale_date"></td>
    </tr>
    <tr>
        <td>Total Cost:</td>
        <td><input type="text" name="total_cost"></td>
    </tr>
    <tr>
        <td>Monthly Cost:</td>
        <td><input type="text" name="monthly_cost"></td>
    </tr>
</table>

<!-- warranty items -->
<h3>Warranty Items</h3>
<table>
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
    <tr>
        <td>2</td>
        <td>
            <select name="warranty_type_2">
                <option value="">--</option>
                <option value="Drive-Train">Drive-Train</option>
                <option value="Exterior">Exterior</option>
                <option value="Interior">Interior</option>
                <option value="Electrical">Electrical</option>
            </select>
        </td>
        <td><input type="date" name="item_start_2"></td>
        <td><input type="number" name="item_months_2"></td>
        <td><input type="text" name="item_cost_2"></td>
        <td><input type="text" name="item_deductible_2"></td>
        <td><textarea name="items_covered_2" rows="2" cols="20"></textarea></td>
    </tr>
    <tr>
        <td>3</td>
        <td>
            <select name="warranty_type_3">
                <option value="">--</option>
                <option value="Drive-Train">Drive-Train</option>
                <option value="Exterior">Exterior</option>
                <option value="Interior">Interior</option>
                <option value="Electrical">Electrical</option>
            </select>
        </td>
        <td><input type="date" name="item_start_3"></td>
        <td><input type="number" name="item_months_3"></td>
        <td><input type="text" name="item_cost_3"></td>
        <td><input type="text" name="item_deductible_3"></td>
        <td><textarea name="items_covered_3" rows="2" cols="20"></textarea></td>
    </tr>
</table>

<br>
<input type="submit" value="Add Warranty">

</form>

<script>
// fill in the hidden fields when sale is picked
function fillSaleInfo() {
    var sel = document.getElementById('sale_select');
    var opt = sel.options[sel.selectedIndex];
    document.getElementById('vehicle_id').value = opt.getAttribute('data-vehicle') || '';
    document.getElementById('customer_id').value = opt.getAttribute('data-customer') || '';
    document.getElementById('employee_id').value = opt.getAttribute('data-employee') || '';
}
</script>

</body>
</html>
