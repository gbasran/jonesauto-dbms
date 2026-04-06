# JonesAuto DBMS - User Tutorial

## Setting Up WAMP

Download and install WAMP Server from wampserver.com. Once it's installed, launch it from the Start menu or the desktop shortcut. You'll see a tray icon in the bottom right of your screen. When it turns green, that means Apache and MySQL are both running and you're good to go. If it's orange, one of the services didn't start; try right-clicking the icon and restarting all services.

You can verify it's working by opening your browser and going to `http://localhost/`. You should see the WAMP default page.

## Importing the Database

1. Open phpMyAdmin by going to `http://localhost/phpmyadmin/` in your browser. The default login is username `root` with a blank password.

2. Create the database: click "New" in the left sidebar, type `jonesauto` as the database name, and click "Create".

3. With the `jonesauto` database selected, click the "Import" tab at the top. Click "Choose File", select `db_setup.sql` from the project folder, and click "Go". This creates all 10 tables.

4. Run the import again with `db_seed.sql`. This loads the sample data (employees, vehicles, customers, purchases, sales, payments, etc.) so you have something to work with right away.

## Opening the System

Copy the project folder to `C:\wamp64\www\` (or `C:\wamp\www\` depending on your install). Then open `http://localhost/CPSC3660_Project/` in your browser. You'll see the home page with links to all the forms, reports, and the queries page.

## Using the Forms

There are 6 input forms for entering data. Here's a quick overview of each one.

**Purchase Form** (`forms/purchase_form.php`): Select a buyer employee from the dropdown. Fill in the purchase details (date, location, seller/dealer) and vehicle info (make, model, year, color, miles, etc.). You can add up to 5 repair problems at the bottom. Click Submit and it saves the vehicle, purchase, and repair records to the database.

**Sale Form** (`forms/sale_form.php`): Pick whether the customer already exists or is new. If they're new, fill in their info (name, phone, address, etc.). Select an available vehicle from the dropdown, enter the sale details (price, down payment, commission), and add up to 3 employment history entries for the customer. Submitting records the sale and marks the vehicle as sold.

**Warranty Form** (`forms/warranty_form.php`): Select an existing sale from the dropdown; the vehicle and customer info auto-fills. Enter the warranty date, total cost, and monthly cost. Add up to 3 warranty items with type, dates, and costs. Click Submit to save.

**Payment Form** (`forms/payment_form.php`): Select a customer from the dropdown (the page reloads to show their existing payments below). Enter the payment details: due date, paid date, amount, and bank account. If the payment is late (paid date after due date), the system automatically updates the customer's late payment stats.

**Customer Form** (`forms/customer_form.php`): Fill in the customer's name, phone, and address info. Existing customers are shown in a table below the form.

**Employee Form** (`forms/employee_form.php`): Enter the employee's name, phone, and role (buyer, salesperson, or both). Existing employees are listed below.

## Viewing Reports and Running Queries

### Reports

The reports section has 6 different views. Each one pulls live data from the database and most have a filter option at the top. Navigate to any report from the home page.

- **Current Inventory** (`reports/inventory.php`): Shows all cars currently available on the lot. You can filter by make using the text box at the top. Each row includes the vehicle info, purchase cost, repair costs, and total investment.

- **Sales Summary** (`reports/sales_report.php`): Lists all completed sales with profit calculations. There's a date range filter if you want to narrow it down. Profit is color-coded green for positive and red for negative.

- **Payment History** (`reports/payment_history.php`): Pick a customer from the dropdown to see all their payments. Late payments show up highlighted in red.

- **Repair Costs** (`reports/repair_summary.php`): Compares estimated vs actual repair costs for every repair in the system. Rows where the actual cost went over the estimate are highlighted.

- **Active Warranties** (`reports/warranty_report.php`): Shows all warranty items with their expiry dates. Rows are color-coded by urgency: red for expired, yellow for expiring within 30 days.

- **Late Payments** (`reports/late_payments.php`): Lists customers with late payments, sorted worst-first. Shows late payment count and average days late for each customer.

### Queries

The queries page (`queries/queries.php`) has a dropdown with 12 business queries. Select one, click "Run Query", and the results appear in a table below. The SQL for each query is also displayed on the page so you can see exactly what's running. For Query 3 (car search by make), there's an extra text input where you type the make you're looking for.

That's the full system. If something isn't working, make sure WAMP is running (green tray icon) and that both SQL files were imported in the right order (db_setup.sql first, then db_seed.sql).
