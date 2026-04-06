<?php
include '../config.php';

// grab query number from url
$query_num = isset($_GET['query_num']) ? intval($_GET['query_num']) : 0;
$make_input = isset($_GET['make_input']) ? $_GET['make_input'] : '';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Business Queries - JonesAuto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<?php $nav_prefix = '../'; include '../nav.php'; ?>

<h2>Business Queries</h2>

<form method="GET">
    <select name="query_num" onchange="this.form.submit()">
        <option value="0">-- Pick a Query --</option>
        <option value="1" <?php echo ($query_num == 1) ? 'selected' : ''; ?>>1. What cars do we have on the lot right now?</option>
        <option value="2" <?php echo ($query_num == 2) ? 'selected' : ''; ?>>2. Show me all our customers</option>
        <option value="3" <?php echo ($query_num == 3) ? 'selected' : ''; ?>>3. Do we have any [make] cars in stock?</option>
        <option value="4" <?php echo ($query_num == 4) ? 'selected' : ''; ?>>4. Who are our employees?</option>
        <option value="5" <?php echo ($query_num == 5) ? 'selected' : ''; ?>>5. How many cars did we buy this month?</option>
        <option value="6" <?php echo ($query_num == 6) ? 'selected' : ''; ?>>6. Which cars did we buy below book price? (Advanced)</option>
        <option value="7" <?php echo ($query_num == 7) ? 'selected' : ''; ?>>7. What is our profit on each sale? (Advanced)</option>
        <option value="8" <?php echo ($query_num == 8) ? 'selected' : ''; ?>>8. Which customers have late payments? (Advanced)</option>
        <option value="9" <?php echo ($query_num == 9) ? 'selected' : ''; ?>>9. Who is our top salesperson? (Advanced)</option>
        <option value="10" <?php echo ($query_num == 10) ? 'selected' : ''; ?>>10. Which warranties expire within 90 days? (Advanced)</option>
        <option value="11" <?php echo ($query_num == 11) ? 'selected' : ''; ?>>11. Where did repair costs go over budget? (Advanced)</option>
        <option value="12" <?php echo ($query_num == 12) ? 'selected' : ''; ?>>12. Which customers bought more than one car? (Advanced)</option>
    </select>

    <?php if ($query_num == 3) { ?>
        <input type="text" name="make_input" value="<?php echo $make_input; ?>" placeholder="Enter make...">
    <?php } ?>

    <input type="submit" value="Run Query">
</form>

<?php if ($query_num > 0) {

    $question = "";
    $sql_display = "";
    $sql = "";

    switch($query_num) {
        case 1:
            $question = "What cars do we have on the lot right now?";
            $sql_display = "SELECT * FROM vehicles WHERE status = 'available' ORDER BY make, model";
            $sql = $sql_display;
            break;

        case 2:
            $question = "Show me all our customers";
            $sql_display = "SELECT customer_id, first_name, last_name, phone, city, state FROM customers ORDER BY last_name";
            $sql = $sql_display;
            break;

        case 3:
            $question = "Do we have any " . $make_input . " cars in stock?";
            $sql_display = "SELECT * FROM vehicles WHERE make = '[make]' AND status = 'available'";
            $sql = "SELECT * FROM vehicles WHERE make = '" . $make_input . "' AND status = 'available'";
            break;

        case 4:
            $question = "Who are our employees?";
            $sql_display = "SELECT * FROM employees ORDER BY role, last_name";
            $sql = $sql_display;
            break;

        case 5:
            $question = "How many cars did we buy this month?";
            $sql_display = "SELECT COUNT(*) as cars_bought, SUM(price_paid) as total_spent\nFROM purchases\nWHERE MONTH(purchase_date) = MONTH(CURDATE())\nAND YEAR(purchase_date) = YEAR(CURDATE())";
            $sql = "SELECT COUNT(*) as cars_bought, SUM(price_paid) as total_spent FROM purchases WHERE MONTH(purchase_date) = MONTH(CURDATE()) AND YEAR(purchase_date) = YEAR(CURDATE())";
            break;

        case 6:
            $question = "Which cars did we buy below book price?";
            $sql_display = "SELECT v.make, v.model, v.year, v.book_price, p.price_paid,\n       (v.book_price - p.price_paid) as savings\nFROM vehicles v\nJOIN purchases p ON v.vehicle_id = p.vehicle_id\nWHERE p.price_paid < v.book_price\nORDER BY savings DESC";
            $sql = "SELECT v.make, v.model, v.year, v.book_price, p.price_paid, (v.book_price - p.price_paid) as savings FROM vehicles v JOIN purchases p ON v.vehicle_id = p.vehicle_id WHERE p.price_paid < v.book_price ORDER BY savings DESC";
            break;

        case 7:
            $question = "What is our profit on each sale?";
            $sql_display = "SELECT v.make, v.model, v.year, s.sale_price, p.price_paid,\n       (SELECT COALESCE(SUM(r.actual_cost),0) FROM repairs r\n        WHERE r.purchase_id = p.purchase_id) as repair_costs,\n       (s.sale_price - p.price_paid -\n        (SELECT COALESCE(SUM(r.actual_cost),0) FROM repairs r\n         WHERE r.purchase_id = p.purchase_id)) as profit\nFROM sales s\nJOIN vehicles v ON s.vehicle_id = v.vehicle_id\nJOIN purchases p ON v.vehicle_id = p.vehicle_id\nORDER BY profit DESC";
            $sql = "SELECT v.make, v.model, v.year, s.sale_price, p.price_paid, (SELECT COALESCE(SUM(r.actual_cost),0) FROM repairs r WHERE r.purchase_id = p.purchase_id) as repair_costs, (s.sale_price - p.price_paid - (SELECT COALESCE(SUM(r.actual_cost),0) FROM repairs r WHERE r.purchase_id = p.purchase_id)) as profit FROM sales s JOIN vehicles v ON s.vehicle_id = v.vehicle_id JOIN purchases p ON v.vehicle_id = p.vehicle_id ORDER BY profit DESC";
            break;

        case 8:
            $question = "Which customers have late payments?";
            $sql_display = "SELECT DISTINCT CONCAT(c.first_name, ' ', c.last_name) as customer_name,\n       c.phone, v.make, v.model, v.year,\n       DATEDIFF(pay.paid_date, pay.due_date) as days_late\nFROM payments pay\nJOIN customers c ON pay.customer_id = c.customer_id\nJOIN sales s ON pay.sale_id = s.sale_id\nJOIN vehicles v ON s.vehicle_id = v.vehicle_id\nWHERE pay.paid_date > pay.due_date\nORDER BY days_late DESC";
            $sql = "SELECT DISTINCT CONCAT(c.first_name, ' ', c.last_name) as customer_name, c.phone, v.make, v.model, v.year, DATEDIFF(pay.paid_date, pay.due_date) as days_late FROM payments pay JOIN customers c ON pay.customer_id = c.customer_id JOIN sales s ON pay.sale_id = s.sale_id JOIN vehicles v ON s.vehicle_id = v.vehicle_id WHERE pay.paid_date > pay.due_date ORDER BY days_late DESC";
            break;

        case 9:
            $question = "Who is our top salesperson?";
            $sql_display = "SELECT CONCAT(e.first_name, ' ', e.last_name) as salesperson,\n       COUNT(s.sale_id) as num_sales,\n       SUM(s.sale_price) as total_revenue,\n       SUM(s.commission) as total_commission\nFROM sales s\nJOIN employees e ON s.employee_id = e.employee_id\nGROUP BY e.employee_id\nORDER BY num_sales DESC";
            $sql = "SELECT CONCAT(e.first_name, ' ', e.last_name) as salesperson, COUNT(s.sale_id) as num_sales, SUM(s.sale_price) as total_revenue, SUM(s.commission) as total_commission FROM sales s JOIN employees e ON s.employee_id = e.employee_id GROUP BY e.employee_id ORDER BY num_sales DESC";
            break;

        case 10:
            $question = "Which warranties expire within 90 days?";
            $sql_display = "SELECT CONCAT(c.first_name, ' ', c.last_name) as customer_name,\n       v.make, v.model, v.year,\n       wi.warranty_type, wi.start_date,\n       DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH) as expiry_date,\n       DATEDIFF(DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH),\n               CURDATE()) as days_remaining\nFROM warranty_items wi\nJOIN warranties w ON wi.warranty_id = w.warranty_id\nJOIN vehicles v ON w.vehicle_id = v.vehicle_id\nJOIN customers c ON w.customer_id = c.customer_id\nWHERE DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH)\n      BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY)\nORDER BY expiry_date";
            $sql = "SELECT CONCAT(c.first_name, ' ', c.last_name) as customer_name, v.make, v.model, v.year, wi.warranty_type, wi.start_date, DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH) as expiry_date, DATEDIFF(DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH), CURDATE()) as days_remaining FROM warranty_items wi JOIN warranties w ON wi.warranty_id = w.warranty_id JOIN vehicles v ON w.vehicle_id = v.vehicle_id JOIN customers c ON w.customer_id = c.customer_id WHERE DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY) ORDER BY expiry_date";
            break;

        case 11:
            $question = "Where did repair costs go over budget?";
            $sql_display = "SELECT v.make, v.model, v.year,\n       SUM(r.est_cost) as estimated_total,\n       SUM(r.actual_cost) as actual_total,\n       (SUM(r.actual_cost) - SUM(r.est_cost)) as over_budget\nFROM repairs r\nJOIN purchases p ON r.purchase_id = p.purchase_id\nJOIN vehicles v ON p.vehicle_id = v.vehicle_id\nGROUP BY p.purchase_id\nHAVING SUM(r.actual_cost) > SUM(r.est_cost)\nORDER BY over_budget DESC";
            $sql = "SELECT v.make, v.model, v.year, SUM(r.est_cost) as estimated_total, SUM(r.actual_cost) as actual_total, (SUM(r.actual_cost) - SUM(r.est_cost)) as over_budget FROM repairs r JOIN purchases p ON r.purchase_id = p.purchase_id JOIN vehicles v ON p.vehicle_id = v.vehicle_id GROUP BY p.purchase_id HAVING SUM(r.actual_cost) > SUM(r.est_cost) ORDER BY over_budget DESC";
            break;

        case 12:
            $question = "Which customers bought more than one car?";
            $sql_display = "SELECT CONCAT(c.first_name, ' ', c.last_name) as customer_name,\n       c.phone,\n       COUNT(s.sale_id) as cars_bought,\n       SUM(s.sale_price) as total_spent\nFROM customers c\nJOIN sales s ON c.customer_id = s.customer_id\nGROUP BY c.customer_id\nHAVING COUNT(s.sale_id) > 1\nORDER BY cars_bought DESC";
            $sql = "SELECT CONCAT(c.first_name, ' ', c.last_name) as customer_name, c.phone, COUNT(s.sale_id) as cars_bought, SUM(s.sale_price) as total_spent FROM customers c JOIN sales s ON c.customer_id = s.customer_id GROUP BY c.customer_id HAVING COUNT(s.sale_id) > 1 ORDER BY cars_bought DESC";
            break;
    }

    // show the question
    echo "<h3>" . $question . "</h3>";
    echo "<h4>SQL:</h4>";
    echo "<pre>" . $sql_display . "</pre>";

    // run it
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo "<p class='error'>Query failed: " . mysqli_error($conn) . "</p>";
    } else {
        $num_rows = mysqli_num_rows($result);
        echo "<p>" . $num_rows . " row(s) returned</p>";

        // TODO: maybe add pagination for big results
        if ($num_rows > 0) {
            echo "<table>";
            // headers from field names
            $fields = mysqli_fetch_fields($result);
            echo "<tr>";
            foreach ($fields as $field) {
                echo "<th>" . $field->name . "</th>";
            }
            echo "</tr>";
            // data rows
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                foreach ($row as $val) {
                    echo "<td>" . $val . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }

} ?>

<?php mysqli_close($conn); ?>
</body>
</html>
