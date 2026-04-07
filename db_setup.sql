-- Lethbridge JonesAuto Database
-- CPSC 3660 Course Project

-- employees table (buyers and salespeople)
CREATE TABLE employees (
    employee_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    role VARCHAR(20) DEFAULT 'salesperson',
    is_active TINYINT(1) DEFAULT 1
);

-- vehicles table
CREATE TABLE vehicles (
    vehicle_id INT AUTO_INCREMENT PRIMARY KEY,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    color VARCHAR(30),
    miles INT,
    condition_desc VARCHAR(50),
    book_price DECIMAL(10,2),
    style VARCHAR(30),
    interior_color VARCHAR(30),
    status VARCHAR(20) DEFAULT 'available',
    is_active TINYINT(1) DEFAULT 1
);

-- purchases table (when we buy a car at auction)
CREATE TABLE purchases (
    purchase_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    employee_id INT NOT NULL,
    purchase_date DATE NOT NULL,
    location VARCHAR(100),
    seller_dealer VARCHAR(100),
    is_auction TINYINT(1) DEFAULT 1,
    price_paid DECIMAL(10,2) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(vehicle_id),
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
);

-- repairs needed on purchased cars
CREATE TABLE repairs (
    repair_id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    problem_num INT,
    description VARCHAR(200),
    est_cost DECIMAL(10,2),
    actual_cost DECIMAL(10,2),
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (purchase_id) REFERENCES purchases(purchase_id)
);

-- customers
CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    address VARCHAR(100),
    city VARCHAR(50),
    state VARCHAR(30),
    zip VARCHAR(10),
    gender VARCHAR(10),
    dob DATE,
    num_late_payments INT DEFAULT 0,
    avg_days_late DECIMAL(5,1) DEFAULT 0.0,
    is_active TINYINT(1) DEFAULT 1
);

-- customer employment history (for credit checks)
CREATE TABLE employment_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    employer VARCHAR(100),
    title VARCHAR(50),
    supervisor_phone VARCHAR(20),
    employer_address VARCHAR(150),
    start_date DATE,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

-- sales
CREATE TABLE sales (
    sale_id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    customer_id INT NOT NULL,
    employee_id INT NOT NULL,
    sale_date DATE NOT NULL,
    total_due DECIMAL(10,2),
    down_payment DECIMAL(10,2),
    financed_amount DECIMAL(10,2),
    sale_price DECIMAL(10,2),
    commission DECIMAL(10,2),
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(vehicle_id),
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
);

-- warranties
CREATE TABLE warranties (
    warranty_id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    customer_id INT NOT NULL,
    employee_id INT NOT NULL,
    warranty_sale_date DATE,
    total_cost DECIMAL(10,2),
    monthly_cost DECIMAL(10,2),
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(vehicle_id),
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
);

-- individual warranty coverage items
CREATE TABLE warranty_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    warranty_id INT NOT NULL,
    warranty_type VARCHAR(50),
    start_date DATE,
    length_months INT,
    cost DECIMAL(10,2),
    deductible DECIMAL(10,2),
    items_covered TEXT,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (warranty_id) REFERENCES warranties(warranty_id)
);

-- payment records
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    sale_id INT NOT NULL,
    payment_date DATE,
    due_date DATE,
    paid_date DATE,
    amount DECIMAL(10,2),
    bank_account VARCHAR(50),
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id)
);

-- tracks what happened to records
CREATE TABLE operations_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(50),
    record_id INT,
    operation VARCHAR(20),
    op_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- VIEWS: quick ways to check data without writing big queries
-- ============================================================

-- all customers including deleted ones, with status shown
CREATE VIEW v_customers AS
SELECT customer_id, first_name, last_name, phone, address, city, state, zip,
       gender, dob, num_late_payments, avg_days_late, is_active,
       CASE WHEN is_active = 1 THEN 'Active' ELSE 'Deleted' END as status
FROM customers
ORDER BY is_active DESC, last_name;

-- all employees including deleted ones, with status shown
CREATE VIEW v_employees AS
SELECT employee_id, first_name, last_name, phone, role, is_active,
       CASE WHEN is_active = 1 THEN 'Active' ELSE 'Deleted' END as status
FROM employees
ORDER BY is_active DESC, role, last_name;

-- cars on the lot with what we paid and repair costs
CREATE VIEW v_inventory AS
SELECT v.vehicle_id, v.make, v.model, v.year, v.color, v.miles,
       v.condition_desc, v.book_price, v.style, v.interior_color,
       COALESCE(p.price_paid, 0) as price_paid,
       COALESCE((SELECT SUM(r.actual_cost) FROM repairs r WHERE r.purchase_id = p.purchase_id), 0) as repair_costs,
       COALESCE(p.price_paid, 0) + COALESCE((SELECT SUM(r.actual_cost) FROM repairs r WHERE r.purchase_id = p.purchase_id), 0) as total_cost
FROM vehicles v
LEFT JOIN purchases p ON v.vehicle_id = p.vehicle_id
WHERE v.status = 'available' AND v.is_active = 1
ORDER BY v.make, v.model;

-- all purchases with vehicle info
CREATE VIEW v_purchases AS
SELECT p.purchase_id, p.purchase_date, p.location, p.seller_dealer, p.is_auction, p.price_paid,
       v.make, v.model, v.year, v.color,
       CONCAT(e.first_name, ' ', e.last_name) as buyer
FROM purchases p
JOIN vehicles v ON p.vehicle_id = v.vehicle_id
JOIN employees e ON p.employee_id = e.employee_id
WHERE p.is_active = 1
ORDER BY p.purchase_date DESC;

-- all repairs with vehicle info and over/under budget
CREATE VIEW v_repairs AS
SELECT r.repair_id, v.make, v.model, v.year, p.purchase_date,
       r.problem_num, r.description, r.est_cost, r.actual_cost,
       (r.actual_cost - r.est_cost) as difference
FROM repairs r
JOIN purchases p ON r.purchase_id = p.purchase_id
JOIN vehicles v ON p.vehicle_id = v.vehicle_id
WHERE r.is_active = 1
ORDER BY p.purchase_date DESC, r.problem_num;

-- sales with profit calculation
CREATE VIEW v_sales AS
SELECT s.sale_id, s.sale_date, s.sale_price, s.total_due, s.down_payment,
       s.financed_amount, s.commission,
       CONCAT(v.year, ' ', v.make, ' ', v.model) as vehicle,
       CONCAT(c.first_name, ' ', c.last_name) as customer,
       CONCAT(e.first_name, ' ', e.last_name) as salesperson,
       p.price_paid as cost,
       (SELECT COALESCE(SUM(r.actual_cost),0) FROM repairs r WHERE r.purchase_id = p.purchase_id) as repair_costs,
       (s.sale_price - p.price_paid - (SELECT COALESCE(SUM(r.actual_cost),0) FROM repairs r WHERE r.purchase_id = p.purchase_id)) as profit
FROM sales s
JOIN vehicles v ON s.vehicle_id = v.vehicle_id
JOIN customers c ON s.customer_id = c.customer_id
JOIN employees e ON s.employee_id = e.employee_id
JOIN purchases p ON v.vehicle_id = p.vehicle_id
WHERE s.is_active = 1
ORDER BY s.sale_date DESC;

-- all payments with late/on-time status
CREATE VIEW v_payments AS
SELECT pay.payment_id, CONCAT(c.first_name, ' ', c.last_name) as customer,
       CONCAT(v.year, ' ', v.make, ' ', v.model) as vehicle,
       pay.due_date, pay.paid_date, pay.amount, pay.bank_account,
       DATEDIFF(pay.paid_date, pay.due_date) as days_late,
       CASE WHEN pay.paid_date > pay.due_date THEN 'LATE' ELSE 'On Time' END as status
FROM payments pay
JOIN customers c ON pay.customer_id = c.customer_id
JOIN sales s ON pay.sale_id = s.sale_id
JOIN vehicles v ON s.vehicle_id = v.vehicle_id
WHERE pay.is_active = 1
ORDER BY pay.due_date DESC;

-- customers with late payments sorted worst first
CREATE VIEW v_late_customers AS
SELECT c.customer_id, CONCAT(c.first_name, ' ', c.last_name) as customer,
       c.phone, COUNT(pay.payment_id) as total_payments,
       SUM(CASE WHEN pay.paid_date > pay.due_date THEN 1 ELSE 0 END) as late_count,
       c.avg_days_late
FROM customers c
JOIN payments pay ON c.customer_id = pay.customer_id
WHERE c.is_active = 1 AND pay.is_active = 1
GROUP BY c.customer_id, c.first_name, c.last_name, c.phone, c.avg_days_late
HAVING late_count > 0
ORDER BY late_count DESC;

-- warranty items with expiry status
CREATE VIEW v_warranties AS
SELECT w.warranty_id, CONCAT(c.first_name, ' ', c.last_name) as customer,
       CONCAT(v.year, ' ', v.make, ' ', v.model) as vehicle,
       wi.warranty_type, wi.start_date, wi.length_months, wi.cost, wi.deductible,
       wi.items_covered,
       DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH) as expiry_date,
       DATEDIFF(DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH), CURDATE()) as days_left,
       CASE
           WHEN DATEDIFF(DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH), CURDATE()) < 0 THEN 'EXPIRED'
           WHEN DATEDIFF(DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH), CURDATE()) <= 30 THEN 'EXPIRING SOON'
           ELSE 'ACTIVE'
       END as warranty_status
FROM warranty_items wi
JOIN warranties w ON wi.warranty_id = w.warranty_id
JOIN vehicles v ON w.vehicle_id = v.vehicle_id
JOIN customers c ON w.customer_id = c.customer_id
WHERE wi.is_active = 1 AND w.is_active = 1
ORDER BY expiry_date;

-- employment history for all active customers
CREATE VIEW v_employment AS
SELECT CONCAT(c.first_name, ' ', c.last_name) as customer,
       eh.employer, eh.title, eh.supervisor_phone, eh.employer_address, eh.start_date
FROM employment_history eh
JOIN customers c ON eh.customer_id = c.customer_id
WHERE eh.is_active = 1 AND c.is_active = 1
ORDER BY c.last_name, eh.start_date DESC;

-- operations log (most recent first)
CREATE VIEW v_operations AS
SELECT log_id, table_name, record_id, operation, op_date
FROM operations_log ORDER BY op_date DESC;

-- full customer audit trail: every operation done on each customer
CREATE VIEW v_customer_history AS
SELECT c.customer_id, CONCAT(c.first_name, ' ', c.last_name) as customer,
       c.phone, c.city,
       CASE WHEN c.is_active = 1 THEN 'Active' ELSE 'Deleted' END as current_status,
       o.operation, o.op_date
FROM customers c
LEFT JOIN operations_log o ON o.table_name = 'customers' AND o.record_id = c.customer_id
ORDER BY c.customer_id, o.op_date;

-- full employee audit trail
CREATE VIEW v_employee_history AS
SELECT e.employee_id, CONCAT(e.first_name, ' ', e.last_name) as employee,
       e.phone, e.role,
       CASE WHEN e.is_active = 1 THEN 'Active' ELSE 'Deleted' END as current_status,
       o.operation, o.op_date
FROM employees e
LEFT JOIN operations_log o ON o.table_name = 'employees' AND o.record_id = e.employee_id
ORDER BY e.employee_id, o.op_date;

-- soft-deleted records across all tables
CREATE VIEW v_deleted AS
SELECT 'customer' as type, customer_id as id, CONCAT(first_name, ' ', last_name) as name FROM customers WHERE is_active = 0
UNION ALL
SELECT 'employee', employee_id, CONCAT(first_name, ' ', last_name) FROM employees WHERE is_active = 0
UNION ALL
SELECT 'vehicle', vehicle_id, CONCAT(year, ' ', make, ' ', model) FROM vehicles WHERE is_active = 0;
