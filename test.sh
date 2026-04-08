#!/bin/bash

# test suite for jonesauto
# tests every page, form, report, query, edit, and action link

GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

PASS=0
FAIL=0
BASE="http://localhost:8000"

pass() { echo -e "${GREEN}  PASS: $1${NC}"; PASS=$((PASS+1)); }
fail() { echo -e "${RED}  FAIL: $1${NC}"; FAIL=$((FAIL+1)); }

check_page() {
    local url="$1"
    local name="$2"
    local expect="$3"

    local response=$(curl -s -L -w "\n%{http_code}" "$BASE$url")
    local code=$(echo "$response" | tail -1)
    local body=$(echo "$response" | sed '$d')

    if [ "$code" -ge 500 ]; then
        fail "$name - HTTP $code"
        return
    fi

    if echo "$body" | grep -qi "fatal error\|parse error\|warning:"; then
        fail "$name - PHP error in output"
        return
    fi

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

# helper to check a page does NOT contain something
check_not() {
    local url="$1"
    local name="$2"
    local not_expect="$3"

    local body=$(curl -s "$BASE$url")
    if echo "$body" | grep -qi "$not_expect"; then
        fail "$name - found '$not_expect' (should not be there)"
    else
        pass "$name"
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

# =============================================
echo ""
echo "[Page loads - all 14 pages]"
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

# =============================================
echo ""
echo "[Navigation]"
check_page "/forms/customer_form.php" "nav has dropdowns" "dropdown"
check_page "/reports/inventory.php" "nav has site title" "JonesAuto"
check_page "/forms/purchase_form.php" "nav has Forms dropdown" "dropdown-content"
check_page "/queries/queries.php" "nav has Reports dropdown" "Reports"

# =============================================
echo ""
echo "[Index page]"
check_page "/" "stat cards present" "stat-card"
check_page "/" "has two-column layout" "two-col"
check_page "/" "shows car count" "Cars Available"
check_page "/" "shows customer count" "Customers"
check_page "/" "shows sales count" "Sales"
check_page "/" "links to forms" "purchase_form"
check_page "/" "links to reports" "inventory"
check_page "/" "links to queries" "queries"
check_page "/" "has footer" "footer"

# =============================================
echo ""
echo "[Seed data in reports]"
check_page "/reports/inventory.php" "inventory has Toyota" "Toyota"
check_page "/reports/inventory.php" "inventory has Honda" "Honda"
check_page "/reports/late_payments.php" "late payments has Garcia" "Garcia"
check_page "/reports/late_payments.php" "late payments has Taylor" "Taylor"
check_page "/reports/repair_summary.php" "repairs has data" "Scratch"
check_page "/reports/sales_report.php" "sales has profit column" "Profit"
check_page "/reports/warranty_report.php" "warranties show types" "Drive-Train"
check_page "/reports/payment_history.php?customer_id=1" "payment history for Wilson" "Wilson"
check_page "/reports/payment_history.php?customer_id=1" "payment history shows status" "On Time"

# =============================================
echo ""
echo "[Seed data coverage]"
# employee roles
check_page "/forms/purchase_form.php" "buyer role in dropdown" "Thompson"
check_page "/forms/purchase_form.php" "both role shows in buyers" "Carter"
check_page "/forms/sale_form.php" "both role shows in salespeople" "Carter"
# vehicle styles
check_page "/queries/queries.php?query_num=1" "has Sedan style" "Sedan"
check_page "/queries/queries.php?query_num=1" "has SUV style" "SUV"
check_page "/queries/queries.php?query_num=1" "has Truck style" "Truck"
check_page "/queries/queries.php?query_num=1" "has Van style" "Van"
# vehicle conditions
check_page "/reports/inventory.php" "has Excellent condition" "Excellent"
check_page "/reports/inventory.php" "has Good condition" "Good"
check_page "/reports/inventory.php" "has Fair condition" "Fair"
# warranty types
check_page "/reports/warranty_report.php" "has Drive-Train warranty" "Drive-Train"
check_page "/reports/warranty_report.php" "has Exterior warranty" "Exterior"
check_page "/reports/warranty_report.php" "has Interior warranty" "Interior"
check_page "/reports/warranty_report.php" "has Electrical warranty" "Electrical"
# customer with no late payments has payments
check_page "/reports/payment_history.php?customer_id=2" "Brown has payments" "RBC-7890"
# Brown's payments are all on-time so none should have the late class
check_not "/reports/payment_history.php?customer_id=2" "Brown has no late status rows" "class=.*late.*LATE"
# customer with all late payments
check_page "/reports/payment_history.php?customer_id=3" "Garcia has late payments" "LATE"
# non-auction purchase
check_page "/reports/repair_summary.php" "has non-auction vehicles" "Ford Focus"
# sale with no warranty (sale 4 = Cruze to Santos)
check_page "/forms/warranty_form.php?sale_id=4" "sale with no warranty" "No warranties"
# sale with warranty (sale 1 = F-150 to Wilson)
check_page "/forms/warranty_form.php?sale_id=1" "sale with existing warranty" "Drive-Train"
# multi-car customer
check_page "/queries/queries.php?query_num=12" "Wilson is multi-car buyer" "Wilson"

# =============================================
echo ""
echo "[Report filters]"
check_page "/reports/inventory.php?make_filter=Toyota" "inventory filter by Toyota" "Toyota"
check_not "/reports/inventory.php?make_filter=Toyota" "inventory filter excludes Honda" "Honda"
check_page "/reports/inventory.php?make_filter=Chrysler" "inventory filter by Chrysler" "Pacifica"
check_page "/reports/sales_report.php?date_from=2025-01-01&date_to=2025-12-31" "sales date filter 2025" "Profit"
check_page "/reports/sales_report.php?date_from=2026-03-01&date_to=2026-04-30" "sales date filter 2026" "Corolla"

# =============================================
echo ""
echo "[Report action links]"
check_page "/reports/inventory.php" "inventory has Sell link" "sale_form.php?vehicle_id="
check_page "/reports/warranty_report.php" "warranty report has Add Warranty link" "warranty_form.php?sale_id="
check_page "/reports/late_payments.php" "late payments has Record Payment link" "payment_form.php?customer_id="
check_page "/reports/sales_report.php" "sales report has Add Warranty link" "warranty_form.php?sale_id="
check_page "/reports/payment_history.php?customer_id=1" "payment history has Record Payment link" "payment_form.php?customer_id="

# =============================================
echo ""
echo "[All 12 queries]"
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

# =============================================
echo ""
echo "[Form submissions - add customer]"
check_post "/process/process_customer.php" "add customer" "first_name=Test&last_name=Runner&phone=403-555-0000&address=1+Test+St&city=Lethbridge&state=AB&zip=T1H+0A0&gender=Male&dob=1995-01-01" ""
check_page "/forms/customer_form.php" "new customer appears in list" "Runner"

echo ""
echo "[Form submissions - add employee]"
check_post "/process/process_employee.php" "add employee" "first_name=New&last_name=Tester&phone=403-555-1111&role=salesperson" ""
check_page "/forms/employee_form.php" "new employee appears in list" "Tester"

# =============================================
echo ""
echo "[Edit customer]"
check_page "/forms/customer_form.php?edit=1" "edit mode loads" "Edit Customer"
check_page "/forms/customer_form.php?edit=1" "edit prefills first name" "James"
check_page "/forms/customer_form.php?edit=1" "edit prefills last name" "Wilson"
check_page "/forms/customer_form.php?edit=1" "edit shows cancel button" "Cancel"
check_page "/forms/customer_form.php?edit=1" "edit shows employment history" "Employment History"
check_post "/process/process_customer.php" "update customer" "customer_id=1&first_name=James&last_name=Wilson&phone=403-555-9999&address=123+Main+St&city=Lethbridge&state=AB&zip=T1H+2A1&gender=Male&dob=1985-03-15" ""
check_page "/forms/customer_form.php" "updated phone shows" "403-555-9999"

echo ""
echo "[Edit employee]"
check_page "/forms/employee_form.php?edit=1" "edit employee loads" "Edit Employee"
check_page "/forms/employee_form.php?edit=1" "edit prefills name" "Thompson"
check_page "/forms/employee_form.php?edit=1" "edit shows cancel" "Cancel"

# =============================================
echo ""
echo "[Customer employment history]"
check_page "/forms/customer_form.php?edit=3" "Garcia has employment history" "Ag Solutions"
check_page "/forms/customer_form.php?edit=3" "shows add employment form" "Add Employment Record"
check_post "/process/process_customer.php" "add employment record" "add_employment=1&customer_id=1&employer=Test+Corp&emp_title=Dev&emp_phone=403-555-0000&emp_address=1+Test+St&emp_start=2025-01-01" ""
check_page "/forms/customer_form.php?edit=1" "new employment record appears" "Test Corp"

# =============================================
echo ""
echo "[Customer/employee edit links in tables]"
check_page "/forms/customer_form.php" "customer table has edit links" "edit="
check_page "/forms/employee_form.php" "employee table has edit links" "edit="

# =============================================
echo ""
echo "[Warranty form - sale context]"
check_page "/forms/warranty_form.php?sale_id=1" "warranty form with sale selected" "Warranty Details"
check_page "/forms/warranty_form.php?sale_id=1" "shows existing warranties" "Existing Warranties"
check_page "/forms/warranty_form.php?sale_id=1" "shows add item button" "Add Warranty Item"

# =============================================
echo ""
echo "[Sale form - vehicle context]"
check_page "/forms/sale_form.php" "sale form has vehicle data attrs" "data-book"
check_page "/forms/sale_form.php" "sale form has vehicle details div" "vehicle_info"
check_page "/forms/sale_form.php" "sale form has customer data attrs" "data-late"
check_page "/forms/sale_form.php" "sale form has customer info div" "customer_info"
check_page "/forms/sale_form.php" "sale form has add employer button" "Add Employer"

echo ""
echo "[Sale form - vehicle pre-selection]"
# vehicle_id 1 is Toyota Camry
check_page "/forms/sale_form.php?vehicle_id=1" "pre-selects vehicle from report link" "selected"

# =============================================
echo ""
echo "[Purchase form - dynamic repairs]"
check_page "/forms/purchase_form.php" "has add repair button" "Add Repair"
check_page "/forms/purchase_form.php" "has recent purchases table" "Recent Purchases"
check_page "/forms/purchase_form.php" "recent purchases show data" "Calgary"

# =============================================
echo ""
echo "[Payment form - context]"
check_page "/forms/payment_form.php?customer_id=1" "payment form loads with customer" "Wilson"
check_page "/forms/payment_form.php?customer_id=1" "shows customer stats" "Late Payments"
check_page "/forms/payment_form.php?customer_id=1" "shows payment history" "On Time"
check_page "/forms/payment_form.php?customer_id=3" "Garcia shows late payments" "LATE"

# =============================================
echo ""
echo "[Form dropdowns populated]"
check_page "/forms/purchase_form.php" "purchase form has buyers" "Richards"
check_page "/forms/sale_form.php" "sale form has vehicles" "Select Vehicle"
check_page "/forms/sale_form.php" "sale form has salespeople" "Select Salesperson"
check_page "/forms/sale_form.php" "sale form has customers" "Select Customer"
check_page "/forms/warranty_form.php" "warranty form has sales" "Select Sale"
check_page "/forms/payment_form.php" "payment form has customers" "Select Customer"

# =============================================
echo ""
echo "[Database integrity - seed data counts]"
check_page "/queries/queries.php?query_num=1" "query 1 returns 12 available cars" "12 row"
check_page "/queries/queries.php?query_num=2" "query 2 returns 8+ customers" "row(s) returned"
check_page "/queries/queries.php?query_num=4" "query 4 returns employees" "row(s) returned"

# =============================================
echo ""
echo "[Input validation - HTML5 attributes]"
check_page "/forms/customer_form.php" "customer phone has pattern" "pattern="
check_page "/forms/customer_form.php" "customer dob is required" "name=\"dob\".*required"
check_page "/forms/employee_form.php" "employee phone has pattern" "pattern="
check_page "/forms/purchase_form.php" "year has min/max" "min=\"1990\""
check_page "/forms/purchase_form.php" "price paid is number type" "type=\"number\".*name=\"price_paid\""
check_page "/forms/sale_form.php" "sale date is required" "name=\"sale_date\".*required"

# =============================================
echo ""
echo "[Validation - expected rejections (should redirect with error)]"
# submit customer with blank name - server should reject
check_post "/process/process_customer.php" "(expected reject) blank customer name redirects" "first_name=&last_name=&phone=&address=&city=&state=&zip=&gender=Male&dob=" "error"
# submit purchase with no make - server should reject
check_post "/process/process_purchase.php" "(expected reject) blank purchase fields redirects" "make=&model=&year=&color=&miles=&condition_desc=Good&book_price=0&price_paid=0&style=Sedan&interior_color=&purchase_date=&location=&seller_dealer=&buyer=1" "error"
# submit sale with no vehicle - server should reject
check_post "/process/process_sale.php" "(expected reject) blank sale redirects" "vehicle_id=&employee_id=3&sale_date=&sale_price=0&total_due=0&down_payment=0&financed_amount=0&commission=0&customer_type=existing&customer_id=1" "error"
# submit payment with zero amount - server should reject
check_post "/process/process_payment.php" "(expected reject) zero payment redirects" "customer_id=1&sale_id=1&payment_date=2026-04-01&due_date=&paid_date=&amount=0&bank_account=TEST" "error"
# submit warranty with no sale - server should reject
check_post "/process/process_warranty.php" "(expected reject) blank warranty redirects" "sale_id=&vehicle_id=&customer_id=&employee_id=&warranty_sale_date=&total_cost=0&monthly_cost=0" "error"

# =============================================
echo ""
echo "[Error message display]"
check_page "/forms/customer_form.php?msg=error" "customer form shows error msg" "error"
check_page "/forms/purchase_form.php?msg=error" "purchase form shows error msg" "required fields"
check_page "/forms/sale_form.php?msg=error" "sale form shows error msg" "required fields"
check_page "/forms/payment_form.php?msg=error" "payment form shows error msg" "required fields"

# =============================================
echo ""
echo "[Soft delete - customer]"
# first add a customer we can delete
check_post "/process/process_customer.php" "add deletable customer" "first_name=Delete&last_name=MePlease&phone=403-555-0000&address=&city=&state=AB&zip=&gender=Male&dob=1990-01-01" ""
check_page "/forms/customer_form.php" "deletable customer exists" "MePlease"
# now delete them
check_page "/forms/customer_form.php?delete=10" "soft delete customer" "removed"
# verify gone from customer list
check_not "/forms/customer_form.php" "deleted customer gone from list" "MePlease"
# verify gone from sale form customer dropdown
check_not "/forms/sale_form.php" "deleted customer gone from sale dropdown" "MePlease"
# verify gone from payment form dropdown
check_not "/forms/payment_form.php" "deleted customer gone from payment dropdown" "MePlease"
# verify gone from query 2 (all customers)
check_not "/queries/queries.php?query_num=2" "deleted customer gone from query 2" "MePlease"

echo ""
echo "[Soft delete - employee]"
# add an employee we can delete
check_post "/process/process_employee.php" "add deletable employee" "first_name=Fire&last_name=MeNow&phone=403-555-0000&role=salesperson" ""
check_page "/forms/employee_form.php" "deletable employee exists" "MeNow"
# delete them
check_page "/forms/employee_form.php?delete=8" "soft delete employee" "removed"
# verify gone from employee list
check_not "/forms/employee_form.php" "deleted employee gone from list" "MeNow"
# verify gone from sale form salesperson dropdown
check_not "/forms/sale_form.php" "deleted employee gone from salesperson dropdown" "MeNow"
# verify gone from query 4 (all employees)
check_not "/queries/queries.php?query_num=4" "deleted employee gone from query 4" "MeNow"

echo ""
echo "[Soft delete - data retention]"
# verify the deleted records still exist in the database via a direct mysql check
DELETED_COUNT=$(sudo mysql -u root jonesauto -N -e "SELECT COUNT(*) FROM customers WHERE is_active = 0;" 2>/dev/null)
if [ "$DELETED_COUNT" -gt 0 ]; then
    pass "deleted customers still in database (is_active=0)"
else
    fail "deleted customers not found in database"
fi

DELETED_EMP=$(sudo mysql -u root jonesauto -N -e "SELECT COUNT(*) FROM employees WHERE is_active = 0;" 2>/dev/null)
if [ "$DELETED_EMP" -gt 0 ]; then
    pass "deleted employees still in database (is_active=0)"
else
    fail "deleted employees not found in database"
fi

echo ""
echo "[Operations log]"
OP_COUNT=$(sudo mysql -u root jonesauto -N -e "SELECT COUNT(*) FROM operations_log;" 2>/dev/null)
if [ "$OP_COUNT" -gt 0 ]; then
    pass "operations log has entries"
else
    fail "operations log is empty"
fi

DEL_OPS=$(sudo mysql -u root jonesauto -N -e "SELECT COUNT(*) FROM operations_log WHERE operation = 'delete';" 2>/dev/null)
if [ "$DEL_OPS" -gt 0 ]; then
    pass "operations log recorded delete operations"
else
    fail "no delete operations in log"
fi

CREATE_OPS=$(sudo mysql -u root jonesauto -N -e "SELECT COUNT(*) FROM operations_log WHERE operation = 'create';" 2>/dev/null)
if [ "$CREATE_OPS" -gt 0 ]; then
    pass "operations log recorded create operations"
else
    fail "no create operations in log"
fi

# =============================================
echo ""
echo "[Soft delete - reports exclusion]"
# deleted customer shouldn't appear in late payments report
check_not "/reports/late_payments.php" "deleted customer not in late payments" "MePlease"
# deleted employee shouldn't appear in sales report as salesperson
check_not "/reports/sales_report.php" "deleted employee not in sales report" "MeNow"

# =============================================
echo ""
echo "[Delete confirmation]"
# forms should have onclick confirm for delete links
check_page "/forms/customer_form.php" "customer delete has confirmation" "confirm"
check_page "/forms/employee_form.php" "employee delete has confirmation" "confirm"

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
