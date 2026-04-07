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

-- views for quick data checks
CREATE VIEW v_active_inventory AS
SELECT v.vehicle_id, v.make, v.model, v.year, v.color, v.miles, v.condition_desc,
       v.book_price, v.style, v.status, COALESCE(p.price_paid, 0) as price_paid,
       COALESCE(SUM(r.actual_cost), 0) as repair_costs
FROM vehicles v
LEFT JOIN purchases p ON v.vehicle_id = p.vehicle_id
LEFT JOIN repairs r ON p.purchase_id = r.purchase_id
WHERE v.status = 'available' AND v.is_active = 1
GROUP BY v.vehicle_id, v.make, v.model, v.year, v.color, v.miles,
         v.condition_desc, v.book_price, v.style, v.status, p.price_paid;

CREATE VIEW v_sales_profit AS
SELECT s.sale_id, s.sale_date, s.sale_price, v.make, v.model, v.year,
       CONCAT(c.first_name, ' ', c.last_name) as customer,
       CONCAT(e.first_name, ' ', e.last_name) as salesperson,
       p.price_paid,
       (SELECT COALESCE(SUM(r.actual_cost),0) FROM repairs r WHERE r.purchase_id = p.purchase_id) as repairs,
       (s.sale_price - p.price_paid - (SELECT COALESCE(SUM(r.actual_cost),0) FROM repairs r WHERE r.purchase_id = p.purchase_id)) as profit
FROM sales s
JOIN vehicles v ON s.vehicle_id = v.vehicle_id
JOIN customers c ON s.customer_id = c.customer_id
JOIN employees e ON s.employee_id = e.employee_id
JOIN purchases p ON v.vehicle_id = p.vehicle_id
WHERE s.is_active = 1;

CREATE VIEW v_late_customers AS
SELECT c.customer_id, CONCAT(c.first_name, ' ', c.last_name) as name,
       c.phone, c.num_late_payments, c.avg_days_late
FROM customers c WHERE c.num_late_payments > 0 AND c.is_active = 1;

CREATE VIEW v_warranty_status AS
SELECT w.warranty_id, CONCAT(c.first_name, ' ', c.last_name) as customer,
       CONCAT(v.year, ' ', v.make, ' ', v.model) as vehicle, wi.warranty_type,
       DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH) as expiry,
       DATEDIFF(DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH), CURDATE()) as days_left
FROM warranty_items wi
JOIN warranties w ON wi.warranty_id = w.warranty_id
JOIN vehicles v ON w.vehicle_id = v.vehicle_id
JOIN customers c ON w.customer_id = c.customer_id
WHERE wi.is_active = 1;

CREATE VIEW v_operations AS
SELECT * FROM operations_log ORDER BY op_date DESC;

CREATE VIEW v_deleted_records AS
SELECT 'customer' as type, customer_id as id, CONCAT(first_name, ' ', last_name) as name
FROM customers WHERE is_active = 0
UNION ALL
SELECT 'employee', employee_id, CONCAT(first_name, ' ', last_name)
FROM employees WHERE is_active = 0
UNION ALL
SELECT 'vehicle', vehicle_id, CONCAT(year, ' ', make, ' ', model)
FROM vehicles WHERE is_active = 0;
