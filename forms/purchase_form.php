<?php
include '../config.php';

// grab the buyers from db
$sql = "SELECT employee_id, first_name, last_name FROM employees WHERE role IN ('buyer', 'both') ORDER BY last_name";
$buyers = mysqli_query($conn, $sql);
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
        <td><input type="number" name="year" required></td>
    </tr>
    <tr>
        <td>Color:</td>
        <td><input type="text" name="color"></td>
    </tr>
    <tr>
        <td>Miles:</td>
        <td><input type="number" name="miles"></td>
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
        <td><input type="text" name="book_price"></td>
    </tr>
    <tr>
        <td>Price Paid:</td>
        <td><input type="text" name="price_paid" required></td>
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
<h3>Repair Problems (up to 5)</h3>
<table>
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
    <tr>
        <td>2</td>
        <td><input type="text" name="repair_desc_2"></td>
        <td><input type="text" name="repair_est_2"></td>
        <td><input type="text" name="repair_actual_2"></td>
    </tr>
    <tr>
        <td>3</td>
        <td><input type="text" name="repair_desc_3"></td>
        <td><input type="text" name="repair_est_3"></td>
        <td><input type="text" name="repair_actual_3"></td>
    </tr>
    <tr>
        <td>4</td>
        <td><input type="text" name="repair_desc_4"></td>
        <td><input type="text" name="repair_est_4"></td>
        <td><input type="text" name="repair_actual_4"></td>
    </tr>
    <tr>
        <td>5</td>
        <td><input type="text" name="repair_desc_5"></td>
        <td><input type="text" name="repair_est_5"></td>
        <td><input type="text" name="repair_actual_5"></td>
    </tr>
</table>

<br>
<input type="submit" value="Record Purchase">

</form>

</body>
</html>
