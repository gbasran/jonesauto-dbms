<?php
include '../config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Repair Cost Summary - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="nav">
    <a href="../index.html">Back to Home</a>
</div>

<h2>Repair Cost Summary</h2>

<?php
// grab all repairs with vehicle info
$sql = "SELECT v.make, v.model, v.year, p.purchase_date, r.problem_num, r.description, r.est_cost, r.actual_cost, (r.actual_cost - r.est_cost) as difference FROM repairs r JOIN purchases p ON r.purchase_id = p.purchase_id JOIN vehicles v ON p.vehicle_id = v.vehicle_id ORDER BY p.purchase_id, r.problem_num";

$result = mysqli_query($conn, $sql);
?>

<table>
    <tr>
        <th>Vehicle</th>
        <th>Purchase Date</th>
        <th>Problem #</th>
        <th>Description</th>
        <th>Est. Cost</th>
        <th>Actual Cost</th>
        <th>Difference</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($result)) {
        // highlight if actual cost went over estimate
        $class = ($row['actual_cost'] > $row['est_cost']) ? ' class="overrun"' : '';
        $diff = $row['difference'];
        $diff_display = ($diff > 0) ? '+$' . number_format($diff, 2) : '$' . number_format($diff, 2);
    ?>
    <tr<?php echo $class; ?>>
        <td><?php echo $row['year'] . ' ' . $row['make'] . ' ' . $row['model']; ?></td>
        <td><?php echo $row['purchase_date']; ?></td>
        <td><?php echo $row['problem_num']; ?></td>
        <td><?php echo $row['description']; ?></td>
        <td>$<?php echo number_format($row['est_cost'], 2); ?></td>
        <td>$<?php echo number_format($row['actual_cost'], 2); ?></td>
        <td><?php echo $diff_display; ?></td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
