#!/bin/bash

# test suite for jonesauto
# checks that all pages load, forms submit, and reports show data

PASS=0
FAIL=0
BASE="http://localhost:8000"

pass() { echo "  PASS: $1"; PASS=$((PASS+1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL+1)); }

check_page() {
    local url="$1"
    local name="$2"
    local expect="$3"

    local body=$(curl -s "$BASE$url")
    local code=$(curl -s -o /dev/null -w "%{http_code}" "$BASE$url")

    # check for http errors
    if [ "$code" -ge 500 ]; then
        fail "$name - HTTP $code"
        return
    fi

    # check for php errors
    if echo "$body" | grep -qi "fatal error\|parse error\|warning:"; then
        fail "$name - PHP error in output"
        return
    fi

    # check for expected content if provided
    if [ -n "$expect" ]; then
        if echo "$body" | grep -qi "$expect"; then
            pass "$name"
        else
            fail "$name - missing '$expect'"
        fi
    else
        pass "$name"
    fi
}

check_post() {
    local url="$1"
    local name="$2"
    local data="$3"
    local expect="$4"

    local body=$(curl -s -L -d "$data" "$BASE$url")

    if echo "$body" | grep -qi "fatal error\|parse error"; then
        fail "$name - PHP error"
        return
    fi

    if [ -n "$expect" ] && echo "$body" | grep -qi "$expect"; then
        pass "$name"
    elif [ -z "$expect" ]; then
        pass "$name"
    else
        fail "$name - missing '$expect'"
    fi
}

echo "=== JonesAuto Test Suite ==="

# setup
sudo -v
sudo service mysql start 2>/dev/null
sleep 1

echo ""
echo "Resetting database..."
sudo mysql -e "DROP DATABASE IF EXISTS jonesauto;" 2>/dev/null
sudo mysql -e "CREATE DATABASE jonesauto; ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY ''; FLUSH PRIVILEGES;" 2>/dev/null
sudo mysql -u root jonesauto < db_setup.sql 2>/dev/null
sudo mysql -u root jonesauto < db_seed.sql 2>/dev/null

# start server
lsof -ti:8000 | xargs kill 2>/dev/null
php -S localhost:8000 > /dev/null 2>&1 &
PHP_PID=$!
sleep 2

# -- page load tests --
echo ""
echo "[Page loads]"
check_page "/" "index page" "JonesAuto"
check_page "/forms/purchase_form.php" "purchase form" "Record a Vehicle"
check_page "/forms/sale_form.php" "sale form" "Sell a Vehicle"
check_page "/forms/warranty_form.php" "warranty form" "Add Warranty"
check_page "/forms/payment_form.php" "payment form" "Record a Payment"
check_page "/forms/customer_form.php" "customer form" "Add New Customer"
check_page "/forms/employee_form.php" "employee form" "Add New Employee"
check_page "/reports/inventory.php" "inventory report" "Current Inventory"
check_page "/reports/sales_report.php" "sales report" "Sales Report"
check_page "/reports/payment_history.php" "payment history" "Payment History"
check_page "/reports/repair_summary.php" "repair summary" "Repair Cost"
check_page "/reports/warranty_report.php" "warranty report" "Active Warranties"
check_page "/reports/late_payments.php" "late payments" "Late Payment"
check_page "/queries/queries.php" "queries page" "Business Queries"

# -- nav bar tests --
echo ""
echo "[Navigation]"
check_page "/forms/customer_form.php" "nav has dropdowns" "dropdown"
check_page "/reports/inventory.php" "nav has site title" "JonesAuto"

# -- seed data tests --
echo ""
echo "[Seed data in reports]"
check_page "/reports/inventory.php" "inventory has vehicles" "Toyota"
check_page "/reports/late_payments.php" "late payments has data" "Garcia"
check_page "/reports/repair_summary.php" "repairs has data" "Scratch"
check_page "/reports/sales_report.php" "sales has profit data" "Profit"

# -- query tests --
echo ""
echo "[Queries]"
check_page "/queries/queries.php?query_num=1" "query 1: available cars" "row(s) returned"
check_page "/queries/queries.php?query_num=2" "query 2: all customers" "Wilson"
check_page "/queries/queries.php?query_num=3&make_input=Toyota" "query 3: search by make" "Toyota"
check_page "/queries/queries.php?query_num=4" "query 4: employees" "Thompson"
check_page "/queries/queries.php?query_num=5" "query 5: bought this month" "row(s) returned"
check_page "/queries/queries.php?query_num=6" "query 6: below book price" "savings"
check_page "/queries/queries.php?query_num=7" "query 7: profit per sale" "profit"
check_page "/queries/queries.php?query_num=8" "query 8: late payments" "days_late"
check_page "/queries/queries.php?query_num=9" "query 9: top salesperson" "num_sales"
check_page "/queries/queries.php?query_num=10" "query 10: expiring warranties" "days_remaining"
check_page "/queries/queries.php?query_num=11" "query 11: over budget repairs" "over_budget"
check_page "/queries/queries.php?query_num=12" "query 12: multi-car customers" "cars_bought"

# -- form submission tests --
echo ""
echo "[Form submissions]"
check_post "/process/process_customer.php" "add customer" "first_name=Test&last_name=Runner&phone=403-555-0000&address=1+Test+St&city=Lethbridge&state=AB&zip=T1H+0A0&gender=Male&dob=1995-01-01" ""
check_page "/forms/customer_form.php" "new customer appears" "Runner"

check_post "/process/process_employee.php" "add employee" "first_name=New&last_name=Tester&phone=403-555-1111&role=salesperson" ""
check_page "/forms/employee_form.php" "new employee appears" "Tester"

# -- index stats test --
echo ""
echo "[Index page]"
check_page "/" "stat cards present" "stat-card"
check_page "/" "has two-column layout" "two-col"

# cleanup
kill $PHP_PID 2>/dev/null

echo ""
echo "================================"
echo "  PASSED: $PASS"
echo "  FAILED: $FAIL"
echo "  TOTAL:  $((PASS+FAIL))"
echo "================================"

if [ $FAIL -eq 0 ]; then
    echo "  ALL TESTS PASSED"
else
    echo "  SOME TESTS FAILED"
fi
echo ""
exit $FAIL
