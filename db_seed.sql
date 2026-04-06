-- employees
INSERT INTO employees (first_name, last_name, phone, role) VALUES
('Mike', 'Thompson', '403-555-0101', 'buyer'),
('Dave', 'Richards', '403-555-0102', 'buyer'),
('Sarah', 'Chen', '403-555-0201', 'salesperson'),
('Jake', 'Morrison', '403-555-0202', 'salesperson'),
('Lisa', 'Blackwood', '403-555-0203', 'salesperson');

-- vehicles (mix of available and sold)
INSERT INTO vehicles (make, model, year, color, miles, condition_desc, book_price, style, interior_color, status) VALUES
('Toyota', 'Camry', 2019, 'Silver', 78000, 'Good', 18500.00, 'Sedan', 'Black', 'available'),
('Honda', 'Civic', 2020, 'Blue', 45000, 'Good', 17200.00, 'Sedan', 'Grey', 'available'),
('Ford', 'F-150', 2018, 'White', 95000, 'Fair', 24000.00, 'Truck', 'Brown', 'sold'),
('Chevrolet', 'Malibu', 2017, 'Black', 110000, 'Fair', 12500.00, 'Sedan', 'Black', 'available'),
('Toyota', 'RAV4', 2021, 'Red', 32000, 'Excellent', 26800.00, 'SUV', 'Black', 'sold'),
('Nissan', 'Altima', 2019, 'Grey', 67000, 'Good', 15900.00, 'Sedan', 'Grey', 'available'),
('Ford', 'Escape', 2020, 'Green', 52000, 'Good', 20100.00, 'SUV', 'Black', 'sold'),
('Hyundai', 'Elantra', 2018, 'White', 88000, 'Fair', 11800.00, 'Sedan', 'Grey', 'available'),
('Honda', 'CR-V', 2019, 'Black', 71000, 'Good', 22500.00, 'SUV', 'Brown', 'available'),
('Chevrolet', 'Cruze', 2017, 'Red', 120000, 'Poor', 8500.00, 'Sedan', 'Black', 'sold'),
('Toyota', 'Corolla', 2020, 'White', 38000, 'Excellent', 17800.00, 'Sedan', 'Grey', 'sold'),
('Ford', 'Focus', 2016, 'Blue', 135000, 'Fair', 7200.00, 'Sedan', 'Black', 'available'),
('Dodge', 'Ram 1500', 2019, 'Black', 82000, 'Good', 28500.00, 'Truck', 'Brown', 'sold'),
('Kia', 'Forte', 2021, 'Silver', 29000, 'Excellent', 16200.00, 'Sedan', 'Black', 'available'),
('Subaru', 'Outback', 2018, 'Green', 93000, 'Good', 19800.00, 'SUV', 'Grey', 'available'),
('Honda', 'Accord', 2017, 'Grey', 105000, 'Fair', 14200.00, 'Sedan', 'Black', 'sold'),
('Toyota', 'Tacoma', 2020, 'White', 47000, 'Good', 30200.00, 'Truck', 'Grey', 'available'),
('Mazda', '3', 2019, 'Red', 58000, 'Good', 16500.00, 'Sedan', 'Black', 'available');

-- purchases
INSERT INTO purchases (vehicle_id, employee_id, purchase_date, location, seller_dealer, is_auction, price_paid) VALUES
(1, 1, '2025-08-15', 'Calgary Auto Auction', 'Manheim Calgary', 1, 14200.00),
(2, 1, '2025-09-02', 'Lethbridge', 'Private Seller', 0, 13500.00),
(3, 2, '2025-07-20', 'Medicine Hat Auction', 'Adesa Medicine Hat', 1, 18500.00),
(4, 1, '2025-10-10', 'Calgary Auto Auction', 'Manheim Calgary', 1, 8200.00),
(5, 2, '2025-09-25', 'Red Deer Auction', 'Adesa Red Deer', 1, 21500.00),
(6, 1, '2025-11-05', 'Lethbridge', 'Trade-In Motors', 0, 11800.00),
(7, 2, '2025-08-30', 'Calgary Auto Auction', 'Manheim Calgary', 1, 15200.00),
(8, 1, '2025-10-28', 'Medicine Hat Auction', 'Adesa Medicine Hat', 1, 7500.00),
(9, 2, '2025-11-15', 'Calgary Auto Auction', 'Manheim Calgary', 1, 17800.00),
(10, 1, '2025-07-05', 'Lethbridge', 'Private Seller', 0, 5200.00),
(11, 2, '2025-12-01', 'Red Deer Auction', 'Adesa Red Deer', 1, 14200.00),
(12, 1, '2025-11-20', 'Calgary Auto Auction', 'Manheim Calgary', 1, 4500.00),
(13, 2, '2025-09-18', 'Medicine Hat Auction', 'Adesa Medicine Hat', 1, 22000.00),
(14, 1, '2025-12-15', 'Calgary Auto Auction', 'Manheim Calgary', 1, 12800.00),
(15, 2, '2025-10-05', 'Lethbridge', 'Private Seller', 0, 15500.00),
(16, 1, '2025-08-12', 'Calgary Auto Auction', 'Manheim Calgary', 1, 10200.00),
(17, 2, '2026-04-01', 'Red Deer Auction', 'Adesa Red Deer', 1, 24800.00),
(18, 1, '2026-04-03', 'Calgary Auto Auction', 'Manheim Calgary', 1, 12500.00);

-- repairs
INSERT INTO repairs (purchase_id, problem_num, description, est_cost, actual_cost) VALUES
(1, 1, 'Scratch on rear bumper', 150.00, 120.00),
(1, 2, 'Dent on driver door', 300.00, 350.00),
(3, 1, 'Cracked windshield', 400.00, 380.00),
(3, 2, 'Rust spot on wheel well', 200.00, 250.00),
(3, 3, 'Interior stain on back seat', 100.00, 80.00),
(4, 1, 'Paint chip on hood', 150.00, 160.00),
(4, 2, 'Broken side mirror', 120.00, 110.00),
(5, 1, 'Minor scratch on fender', 100.00, 90.00),
(7, 1, 'Dent on rear quarter panel', 250.00, 280.00),
(7, 2, 'Worn brake pads', 200.00, 190.00),
(8, 1, 'Broken tail light', 80.00, 75.00),
(8, 2, 'Seat tear on passenger side', 150.00, 200.00),
(8, 3, 'AC not blowing cold', 300.00, 350.00),
(9, 1, 'Scratch on passenger door', 180.00, 170.00),
(10, 1, 'Multiple body dents', 500.00, 620.00),
(10, 2, 'Headlight foggy', 60.00, 55.00),
(12, 1, 'Bumper crack', 200.00, 180.00),
(12, 2, 'Power window not working', 150.00, 200.00),
(13, 1, 'Rust on undercarriage', 300.00, 280.00),
(15, 1, 'Paint fading on roof', 250.00, 240.00),
(15, 2, 'Door handle loose', 50.00, 40.00),
(16, 1, 'Dent on trunk', 200.00, 190.00),
(17, 1, 'Chip in windshield', 100.00, 95.00),
(18, 1, 'Scratch on hood', 120.00, 130.00),
(18, 2, 'Interior needs detailing', 200.00, 180.00);

-- customers
INSERT INTO customers (first_name, last_name, phone, address, city, state, zip, gender, dob, num_late_payments, avg_days_late) VALUES
('James', 'Wilson', '403-555-1001', '123 Main St', 'Lethbridge', 'AB', 'T1H 2A1', 'Male', '1985-03-15', 2, 8.5),
('Emily', 'Brown', '403-555-1002', '456 Park Ave', 'Lethbridge', 'AB', 'T1J 3B2', 'Female', '1990-07-22', 0, 0.0),
('Robert', 'Garcia', '403-555-1003', '789 River Rd', 'Coaldale', 'AB', 'T1M 1L4', 'Male', '1978-11-08', 5, 15.2),
('Maria', 'Santos', '403-555-1004', '321 College Dr', 'Lethbridge', 'AB', 'T1K 4R7', 'Female', '1992-01-30', 1, 3.0),
('Kevin', 'Nguyen', '403-555-1005', '654 Mayor Blvd', 'Lethbridge', 'AB', 'T1H 5C3', 'Male', '1988-09-14', 0, 0.0),
('Ashley', 'Taylor', '403-555-1006', '987 Scenic Dr', 'Picture Butte', 'AB', 'T0K 1V0', 'Female', '1995-04-19', 3, 12.0),
('Daniel', 'Lee', '403-555-1007', '147 Industrial Ave', 'Lethbridge', 'AB', 'T1J 4P2', 'Male', '1982-12-05', 0, 0.0),
('Jennifer', 'Patel', '403-555-1008', '258 Whoop-Up Dr', 'Lethbridge', 'AB', 'T1K 7S8', 'Female', '1993-06-28', 4, 18.5);

-- employment history
INSERT INTO employment_history (customer_id, employer, title, supervisor_phone, employer_address, start_date) VALUES
(1, 'Lethbridge Construction Ltd', 'Foreman', '403-555-8001', '100 Industrial Rd, Lethbridge', '2020-03-01'),
(1, 'AB Builders Inc', 'Laborer', '403-555-8002', '200 Commerce Way, Calgary', '2017-06-15'),
(2, 'University of Lethbridge', 'Admin Assistant', '403-555-8003', '4401 University Dr, Lethbridge', '2019-09-01'),
(3, 'Ag Solutions', 'Equipment Operator', '403-555-8004', '55 Highway 3, Coaldale', '2015-04-10'),
(3, 'Southern AB Farms', 'Farm Hand', '403-555-8005', '300 Rural Route, Taber', '2010-05-20'),
(3, 'City of Lethbridge', 'Maintenance Worker', '403-555-8006', '910 4th Ave S, Lethbridge', '2008-01-15'),
(4, 'Shoppers Drug Mart', 'Pharmacy Tech', '403-555-8007', '501 Mayor Magrath Dr, Lethbridge', '2021-02-01'),
(5, 'ATCO Gas', 'Technician', '403-555-8008', '400 2nd Ave S, Lethbridge', '2018-07-01'),
(5, 'Enbridge', 'Junior Tech', '403-555-8009', '1000 Energy Way, Calgary', '2015-03-15'),
(6, 'Dairy Queen', 'Shift Lead', '403-555-8010', '100 Mayor Magrath Dr, Lethbridge', '2022-06-01'),
(6, 'McDonalds', 'Crew Member', '403-555-8011', '250 Scenic Dr, Lethbridge', '2020-09-15'),
(7, 'Shaw Communications', 'Installer', '403-555-8012', '600 3rd Ave S, Lethbridge', '2016-11-01'),
(8, 'Walmart', 'Cashier', '403-555-8013', '3710 Mayor Magrath Dr, Lethbridge', '2021-08-01'),
(8, 'Save-On-Foods', 'Stocker', '403-555-8014', '2000 Mayor Magrath Dr, Lethbridge', '2019-03-15');

-- sales
INSERT INTO sales (vehicle_id, customer_id, employee_id, sale_date, total_due, down_payment, financed_amount, sale_price, commission) VALUES
(3, 1, 3, '2025-08-25', 26500.00, 19200.00, 7300.00, 26500.00, 800.00),
(5, 2, 4, '2025-10-15', 29500.00, 22000.00, 7500.00, 29500.00, 900.00),
(7, 3, 3, '2025-09-20', 22800.00, 16000.00, 6800.00, 22800.00, 700.00),
(10, 4, 5, '2025-08-10', 11200.00, 6100.00, 5100.00, 11200.00, 350.00),
(13, 5, 4, '2025-10-25', 32500.00, 23000.00, 9500.00, 32500.00, 1000.00),
(16, 6, 3, '2025-09-30', 16800.00, 11000.00, 5800.00, 16800.00, 500.00),
(11, 1, 4, '2026-03-15', 19500.00, 14000.00, 5500.00, 19500.00, 600.00);

-- warranties
INSERT INTO warranties (sale_id, vehicle_id, customer_id, employee_id, warranty_sale_date, total_cost, monthly_cost) VALUES
(1, 3, 1, 3, '2025-08-25', 1200.00, 108.00),
(2, 5, 2, 4, '2025-10-15', 1800.00, 162.00),
(3, 7, 3, 3, '2025-09-20', 900.00, 81.00),
(5, 13, 5, 4, '2025-10-25', 2400.00, 216.00);

-- warranty items
INSERT INTO warranty_items (warranty_id, warranty_type, start_date, length_months, cost, deductible, items_covered) VALUES
(1, 'Drive-Train', '2025-08-25', 8, 800.00, 100.00, 'Engine, transmission, differential, drive shaft'),
(1, 'Exterior', '2025-08-25', 9, 400.00, 50.00, 'Paint, body panels, bumpers'),
(2, 'Drive-Train', '2025-10-15', 24, 1200.00, 75.00, 'Engine, transmission, differential, drive shaft, CV joints'),
(2, 'Exterior', '2025-10-15', 12, 600.00, 50.00, 'Paint, body panels, bumpers, mirrors'),
(3, 'Drive-Train', '2025-09-20', 7, 900.00, 100.00, 'Engine, transmission, differential'),
(4, 'Drive-Train', '2025-10-25', 24, 1500.00, 50.00, 'Engine, transmission, differential, drive shaft, transfer case'),
(4, 'Exterior', '2025-10-25', 24, 500.00, 75.00, 'Paint, body panels'),
(4, 'Interior', '2025-10-25', 12, 400.00, 100.00, 'Seats, dashboard, carpet, headliner');

-- payments (mix of on-time and late)
INSERT INTO payments (customer_id, sale_id, payment_date, due_date, paid_date, amount, bank_account) VALUES
(1, 1, '2025-09-01', '2025-09-25', '2025-09-23', 350.00, 'TD-4521'),
(1, 1, '2025-10-01', '2025-10-25', '2025-10-28', 350.00, 'TD-4521'),
(1, 1, '2025-11-01', '2025-11-25', '2025-11-24', 350.00, 'TD-4521'),
(1, 1, '2025-12-01', '2025-12-25', '2025-12-30', 350.00, 'TD-4521'),
(1, 1, '2026-01-01', '2026-01-25', '2026-01-22', 350.00, 'TD-4521'),
(2, 2, '2025-11-01', '2025-11-15', '2025-11-14', 400.00, 'RBC-7890'),
(2, 2, '2025-12-01', '2025-12-15', '2025-12-13', 400.00, 'RBC-7890'),
(2, 2, '2026-01-01', '2026-01-15', '2026-01-15', 400.00, 'RBC-7890'),
(3, 3, '2025-10-01', '2025-10-20', '2025-10-30', 340.00, 'BMO-3344'),
(3, 3, '2025-11-01', '2025-11-20', '2025-12-05', 340.00, 'BMO-3344'),
(3, 3, '2025-12-01', '2025-12-20', '2026-01-02', 340.00, 'BMO-3344'),
(3, 3, '2026-01-01', '2026-01-20', '2026-02-01', 340.00, 'BMO-3344'),
(3, 3, '2026-02-01', '2026-02-20', '2026-03-08', 340.00, 'BMO-3344'),
(4, 4, '2025-09-01', '2025-09-15', '2025-09-14', 280.00, 'CIBC-5566'),
(4, 4, '2025-10-01', '2025-10-15', '2025-10-18', 280.00, 'CIBC-5566'),
(4, 4, '2025-11-01', '2025-11-15', '2025-11-14', 280.00, 'CIBC-5566'),
(4, 4, '2025-12-01', '2025-12-15', '2025-12-15', 280.00, 'CIBC-5566'),
(5, 5, '2025-11-01', '2025-11-25', '2025-11-24', 500.00, 'TD-9012'),
(5, 5, '2025-12-01', '2025-12-25', '2025-12-23', 500.00, 'TD-9012'),
(5, 5, '2026-01-01', '2026-01-25', '2026-01-25', 500.00, 'TD-9012'),
(6, 6, '2025-10-15', '2025-10-30', '2025-11-05', 320.00, 'ATB-7788'),
(6, 6, '2025-11-15', '2025-11-30', '2025-12-08', 320.00, 'ATB-7788'),
(6, 6, '2025-12-15', '2025-12-30', '2026-01-10', 320.00, 'ATB-7788'),
(6, 6, '2026-01-15', '2026-01-30', '2026-01-29', 320.00, 'ATB-7788'),
(1, 7, '2026-04-01', '2026-04-15', '2026-04-13', 300.00, 'TD-4521');
