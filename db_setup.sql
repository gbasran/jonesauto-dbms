-- Lethbridge JonesAuto Database
-- CPSC 3660 Course Project

-- employees table (buyers and salespeople)
CREATE TABLE employees (
    employee_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    role VARCHAR(20) DEFAULT 'salesperson'
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
    status VARCHAR(20) DEFAULT 'available'
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
    avg_days_late DECIMAL(5,1) DEFAULT 0.0
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
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id)
);
