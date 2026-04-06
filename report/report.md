# JonesAuto Database Management System

## CPSC 3660 - Introduction to Database Systems
## Winter 2026

[INSERT STUDENT NAME HERE]
[INSERT STUDENT ID HERE]

Date: April 8, 2026

Professor: [INSERT PROFESSOR NAME HERE]

---

## Introduction

JonesAuto is a used car dealership based in Lethbridge. They buy cars at auctions, fix them up, and resell them to customers. A lot of their sales are financed, so they've got to keep track of monthly payments on top of everything else. Right now they're doing all of this on paper, which honestly gets pretty messy once you're dealing with dozens of cars and customers at the same time.

I built a database system to replace that whole paper-based workflow. It covers the full lifecycle of a car: buying it at auction, recording what repairs it needs, selling it to a customer, setting up warranty coverage, and tracking monthly payments. We learned in class why a DBMS beats flat files, things like data redundancy, consistency problems, and not being able to query anything easily. This project is pretty much that idea applied to a real business. The system runs on PHP and MySQL on Linux, using PHP's built-in development server.

The system has three main parts: input forms for entering data, output reports for pulling summaries, and a queries page for answering specific business questions. There are 6 forms, 6 reports, and 12 queries total. Seven of those queries use advanced SQL features like JOINs, subqueries, and GROUP BY with HAVING. I tried to cover a good range of what we learned in class throughout the semester.

## Stage 1: Overall Design

### System Goals

The main goal was to replace JonesAuto's paper records with a proper database system. The system needs to track the full car lifecycle from purchase at auction all the way through to customer payments. It also needs to give employees quick access to customer info, vehicle history, and financial summaries without digging through filing cabinets.

### Input Forms

| Form | Purpose |
|------|---------|
| Purchase Form | Records a car bought at auction: vehicle details, purchase price, buyer employee, and up to 5 repair problems found during inspection |
| Sale Form | Records a car sale to a customer: sale price, down payment, financing, salesperson commission, and customer employment history |
| Warranty Form | Adds warranty coverage to an existing sale: type (Drive-Train, Exterior, Interior, Electrical), length, cost, deductible |
| Payment Form | Records monthly payments from customers, detects late payments, updates customer credit stats |
| Customer Form | Adds new customers and shows existing customer records |
| Employee Form | Adds new employees (buyers/salespeople) and shows existing employee records |

### Output Reports

| Report | Purpose |
|--------|---------|
| Current Inventory | Shows all available cars on the lot, filterable by make, includes repair cost totals |
| Sales Summary | Shows all completed sales with profit calculation, filterable by date range |
| Payment History | Shows payment records for a selected customer, highlights late payments in red |
| Repair Costs | Compares estimated vs actual repair costs, highlights overruns |
| Active Warranties | Lists all active warranty coverage with color-coded expiry urgency |
| Late Payments | Shows customers with late payments, sorted worst-first |

### System Users

There are three types of users: buyer employees who go to auctions and fill out purchase forms, salesperson employees who handle sales and warranties and payments, and office staff who run reports and queries. The system doesn't have a login since it's a small dealership and everyone pretty much shares the same machine.

### Initial Design Assumptions

- Cars are only acquired through auctions (though there's an is_auction flag for flexibility)
- Each car has exactly one purchase record and at most one sale
- Customers can buy multiple cars over time
- Warranties are tied to a specific sale, not directly to a customer
- The dealership is in Lethbridge, Alberta (all sample data uses Alberta locations)
- Payments are monthly, and "late" means paid_date is after due_date

I made these assumptions based on the business description in the project spec. Some of them I had to decide on my own since the spec didn't cover every edge case.

## Stage 2: E-R Diagram and Database Schema

We covered E-R diagrams in class using Chen notation, where rectangles represent entities, diamonds represent relationships, and ovals represent attributes. I designed the E-R diagram before writing any SQL since I could map the E-R model directly to relational tables, giving a more structured approach than jumping straight to CREATE TABLE statements.

[INSERT E-R DIAGRAM HERE]

### Entities and Attributes

| Entity | Attributes | Primary Key |
|--------|-----------|-------------|
| employees | employee_id, first_name, last_name, phone, role | employee_id |
| vehicles | vehicle_id, make, model, year, color, miles, condition_desc, book_price, style, interior_color, status | vehicle_id |
| purchases | purchase_id, vehicle_id, employee_id, purchase_date, location, seller_dealer, is_auction, price_paid | purchase_id |
| repairs | repair_id, purchase_id, problem_num, description, est_cost, actual_cost | repair_id |
| customers | customer_id, first_name, last_name, phone, address, city, state, zip, gender, dob, num_late_payments, avg_days_late | customer_id |
| employment_history | history_id, customer_id, employer, title, supervisor_phone, employer_address, start_date | history_id |
| sales | sale_id, vehicle_id, customer_id, employee_id, sale_date, total_due, down_payment, financed_amount, sale_price, commission | sale_id |
| warranties | warranty_id, sale_id, vehicle_id, customer_id, employee_id, warranty_sale_date, total_cost, monthly_cost | warranty_id |
| warranty_items | item_id, warranty_id, warranty_type, start_date, length_months, cost, deductible, items_covered | item_id |
| payments | payment_id, customer_id, sale_id, payment_date, due_date, paid_date, amount, bank_account | payment_id |

### Relationships

1. employees to purchases (one-to-many): one buyer employee can make many purchases
2. vehicles to purchases (one-to-one): each vehicle has exactly one purchase record
3. purchases to repairs (one-to-many): one purchase can have multiple repair problems
4. customers to employment_history (one-to-many): each customer can have multiple employer records
5. vehicles to sales (one-to-one): each vehicle can be sold once
6. customers to sales (one-to-many): one customer can buy multiple cars
7. employees to sales (one-to-many): one salesperson can handle many sales
8. sales to warranties (one-to-many): one sale can have multiple warranty contracts
9. warranties to warranty_items (one-to-many): each warranty can cover multiple items
10. sales to payments (one-to-many): one sale generates multiple monthly payments
11. customers to payments (one-to-many): denormalized link for quick customer payment lookup

Total: 14 foreign key constraints across the schema.

### Normalization

We went through normalization in class covering 1NF, 2NF, 3NF, and BCNF. All of my tables are in 3NF. There aren't any partial dependencies since every primary key is a single-column AUTO_INCREMENT integer, which means 2NF is satisfied automatically. There aren't any transitive dependencies either: each non-key attribute depends directly on the primary key of its table, not on some other non-key column.

That said, I did make two deliberate denormalizations. The first is `num_late_payments` and `avg_days_late` on the customers table. Technically I could calculate these from the payments table every time, but the payment form spec required showing them. Recalculating with an aggregate query on every page load seemed slow and unnecessary, so I stored them directly on the customer record. It's a trade-off between strict normalization and performance.

The second is `customer_id` on the payments table. That info already exists on the sales table (which payments link to through sale_id), so it's redundant. But having it directly on payments means I don't need to JOIN through sales every time I want to look up a customer's payment history. It's another deliberate choice for query simplicity.

BCNF is stricter than 3NF, but for this project 3NF was the right target. The two denormalizations are documented and intentional.

[INSERT RELATIONAL SCHEMA DIAGRAM HERE]

## Stage 3: Forms and Reports

Stage 3 was where the bulk of the coding happened. I built 6 input forms and 6 output reports, all in PHP with mysqli for the database calls. The forms follow the two-file pattern from the class tutorials: an HTML form collects the input, and a PHP processor script handles the INSERT.

### Input Forms

**Purchase Form (purchase_form.php)**

This form records a car bought at auction. The top section captures purchase info: date, location, seller/dealer name, and a checkbox for whether it was an auction purchase. The buyer employee gets selected from a dropdown that's populated from the employees table, filtered to only show people with the buyer or both role. The middle section is all the vehicle details: make, model, year, color, miles, condition (dropdown with Excellent/Good/Fair/Poor), book price, style (Sedan/SUV/Truck/Van/Coupe), interior color, and price paid. The bottom section has up to 5 repair rows where you enter a problem description, estimated cost, and actual cost. Submitting the form inserts into three tables: vehicles, purchases, and repairs.

[INSERT SCREENSHOT HERE]

**Sale Form (sale_form.php)**

This one records a car sale. It's got a toggle at the top for choosing between an existing customer and a new one. If you pick "New Customer," a whole set of fields appears for entering their info: name, phone, address, city, state, zip, gender, and date of birth. If you pick "Existing Customer," you just select from a dropdown. The vehicle is selected from a list of available cars. Then there's the sale details: date, sale price, total due, down payment, financed amount, and commission. At the bottom there's space for up to 3 employment history entries for the customer. The tricky part was the existing/new customer toggle since it changes which fields show up on the page. Submitting inserts into sales and employment_history, creates a new customer record if needed, and updates the vehicle's status to 'sold'.

[INSERT SCREENSHOT HERE]

**Warranty Form (warranty_form.php)**

This form adds warranty coverage to an existing sale. You pick a sale from a dropdown that shows the customer name, vehicle info, and sale date. When you select a sale, it auto-fills the vehicle, customer, and employee IDs using hidden fields and a bit of JavaScript. Then you enter the overall warranty details: date, total cost, and monthly cost. Below that there are up to 3 warranty item rows, each with a type dropdown (Drive-Train, Exterior, Interior, Electrical), start date, length in months, cost, deductible, and a text area for items covered. Submitting inserts into the warranties and warranty_items tables.

[INSERT SCREENSHOT HERE]

**Payment Form (payment_form.php)**

This form records a customer payment. There's a customer dropdown at the top that reloads the page when you select someone, so it can show that customer's info and existing payments below. Once a customer is selected, you see their gender, date of birth, number of late payments, and average days late. Then you pick which sale the payment is for, and enter the payment date, due date, paid date, amount, and bank account. If the paid date is after the due date, it gets flagged as late and the customer's num_late_payments and avg_days_late fields get recalculated. Below the form there's a table showing all existing payments for that customer, with late ones highlighted in red.

[INSERT SCREENSHOT HERE]

**Customer Form (customer_form.php)**

This is a simple add-and-view form. The top half has input fields for first name, last name, phone, address, city, state/province (defaults to AB), zip/postal code, gender, and date of birth. When you submit, it inserts into the customers table. Below the form there's a table showing all existing customers with their contact info, late payment count, and average days late.

[INSERT SCREENSHOT HERE]

**Employee Form (employee_form.php)**

Same pattern as the customer form. The input section has fields for first name, last name, phone, and a role dropdown (salesperson, buyer, or both). Existing employees are shown in a table below with their ID, name, phone, and role. Inserts into the employees table on submit.

[INSERT SCREENSHOT HERE]

### Output Reports

The reports are read-only pages that pull data from the database and display it in HTML tables. Most of them have some kind of filter so you can narrow down what you're looking at.

**Current Inventory (inventory.php)**

Shows all vehicles with status 'available'. There's a text input at the top for filtering by make. For each car it shows make, model, year, color, miles, condition, book price, price paid, and total repair costs summed from the repairs table. It also calculates a total cost column (price paid plus repairs). The query uses a JOIN between vehicles and purchases, plus a subquery on repairs.

[INSERT SCREENSHOT HERE]

**Sales Summary (sales_report.php)**

Shows all completed sales. You can filter by date range using a from/to date picker. For each sale it shows the date, vehicle info, customer name, salesperson, sale price, purchase cost, repair costs, and profit. The profit calculation was one of the trickier queries since it pulls from vehicles, purchases, repairs, sales, customers, and employees. Profit numbers are color-coded green for positive and red for negative. There's a totals row at the bottom.

[INSERT SCREENSHOT HERE]

**Payment History (payment_history.php)**

You select a customer from a dropdown, and it shows all their payments in a table. The dropdown auto-submits when you change the selection. Each row shows the vehicle, due date, paid date, amount, bank account, days late, and status. Late payments (where paid_date is after due_date) get a red highlight using a CSS class. The query JOINs payments, sales, and vehicles.

[INSERT SCREENSHOT HERE]

**Repair Costs (repair_summary.php)**

Shows estimated vs actual repair costs for every repair in the system. Each row has the vehicle info, purchase date, problem number, description, estimated cost, actual cost, and the difference. Rows where the actual cost exceeded the estimate get highlighted with the overrun CSS class. This helps the dealership see which purchases ended up costing more to fix than they expected.

[INSERT SCREENSHOT HERE]

**Active Warranties (warranty_report.php)**

Lists all warranty items with their expiry info. The page color-codes rows by urgency: red background for expired warranties, yellow background for ones expiring within 30 days, and no color for ones with more time left. Each row shows customer name, vehicle, warranty type, start date, length in months, expiry date, days remaining, cost, deductible, and items covered. The query JOINs warranties, warranty_items, vehicles, and customers.

[INSERT SCREENSHOT HERE]

**Late Payments (late_payments.php)**

Shows all customers who have at least one late payment, sorted worst-first by late count. Each row has the customer name, phone, total payments, number of late payments, and average days late. It uses a GROUP BY on customer with a HAVING clause to only show customers where late_count is greater than zero. This is basically a credit risk report for the dealership.

[INSERT SCREENSHOT HERE]

## Stage 4: Business Queries

The project spec required at least 10 queries with at least 5 being advanced (multi-table JOINs or subqueries). I ended up with 12 total: 5 simple and 7 advanced. They're all on a single queries page with a dropdown selector. You pick a query, hit submit, and the results show up in a table below.

For Query 3 there's also a text input so you can search by make. The SQL for each query is shown on the page along with the results, which is helpful for debugging and for the demo.

### Query 1: What cars do we have on the lot right now? (Simple)

This one pulls up every vehicle that's still available for sale. It's the quickest way for a salesperson to see what's on the lot without walking outside.

**SQL:**
```sql
SELECT * FROM vehicles WHERE status = 'available' ORDER BY make, model
```

**SQL features used:** basic SELECT with WHERE, ORDER BY

[INSERT QUERY RESULT SCREENSHOT HERE]

### Query 2: Show me all our customers (Simple)

Lists every customer in the system with their contact info. Useful for the office staff when they need to look someone up or check a phone number.

**SQL:**
```sql
SELECT customer_id, first_name, last_name, phone, city, state FROM customers ORDER BY last_name
```

**SQL features used:** basic SELECT with ORDER BY

[INSERT QUERY RESULT SCREENSHOT HERE]

### Query 3: Do we have any [make] cars in stock? (Simple)

This one takes a text input for the make, so the actual query uses the value the user types in. It's for when a customer calls and asks "do you have any Toyotas?" and you need a quick answer.

**SQL:**
```sql
SELECT * FROM vehicles WHERE make = '[make]' AND status = 'available'
```

**SQL features used:** WHERE with parameter, AND condition

[INSERT QUERY RESULT SCREENSHOT HERE]

### Query 4: Who are our employees? (Simple)

Shows all employees sorted by their role and last name. Pretty straightforward, just a quick reference for who works here and what they do.

**SQL:**
```sql
SELECT * FROM employees ORDER BY role, last_name
```

**SQL features used:** basic SELECT with ORDER BY

[INSERT QUERY RESULT SCREENSHOT HERE]

### Query 5: How many cars did we buy this month? (Simple)

Gives a count of purchases made in the current month along with the total amount spent. The MONTH() and YEAR() functions with CURDATE() make it automatically filter to the current month without needing to type in dates.

**SQL:**
```sql
SELECT COUNT(*) as cars_bought, SUM(price_paid) as total_spent
FROM purchases
WHERE MONTH(purchase_date) = MONTH(CURDATE())
AND YEAR(purchase_date) = YEAR(CURDATE())
```

**SQL features used:** COUNT, SUM aggregates, MONTH/YEAR functions with CURDATE()

[INSERT QUERY RESULT SCREENSHOT HERE]

### Query 6: Which cars did we buy below book price? (Advanced)

This one's useful for the buyers to see which deals paid off. It joins vehicles and purchases to compare what was paid against the book price, then calculates the savings.

**SQL:**
```sql
SELECT v.make, v.model, v.year, v.book_price, p.price_paid,
       (v.book_price - p.price_paid) as savings
FROM vehicles v
JOIN purchases p ON v.vehicle_id = p.vehicle_id
WHERE p.price_paid < v.book_price
ORDER BY savings DESC
```

**SQL features used:** JOIN between vehicles and purchases, calculated column (savings)

[INSERT QUERY RESULT SCREENSHOT HERE]

### Query 7: What is our profit on each sale? (Advanced)

This was the hardest query to write. The profit calculation needs to factor in the purchase price AND all repair costs, so there's a correlated subquery that sums repairs for each purchase. The COALESCE handles cases where a vehicle had no repairs so the sum doesn't come back as NULL.

**SQL:**
```sql
SELECT v.make, v.model, v.year, s.sale_price, p.price_paid,
       (SELECT COALESCE(SUM(r.actual_cost),0) FROM repairs r
        WHERE r.purchase_id = p.purchase_id) as repair_costs,
       (s.sale_price - p.price_paid -
        (SELECT COALESCE(SUM(r.actual_cost),0) FROM repairs r
         WHERE r.purchase_id = p.purchase_id)) as profit
FROM sales s
JOIN vehicles v ON s.vehicle_id = v.vehicle_id
JOIN purchases p ON v.vehicle_id = p.vehicle_id
ORDER BY profit DESC
```

**SQL features used:** correlated subquery for repair costs, multiple JOINs (sales + vehicles + purchases), COALESCE, calculated profit

[INSERT QUERY RESULT SCREENSHOT HERE]

### Query 8: Which customers have late payments? (Advanced)

Shows every late payment along with the customer name, vehicle info, and how many days late it was. The DATEDIFF function calculates how many days late each payment was. 

**SQL:**
```sql
SELECT DISTINCT CONCAT(c.first_name, ' ', c.last_name) as customer_name,
       c.phone, v.make, v.model, v.year,
       DATEDIFF(pay.paid_date, pay.due_date) as days_late
FROM payments pay
JOIN customers c ON pay.customer_id = c.customer_id
JOIN sales s ON pay.sale_id = s.sale_id
JOIN vehicles v ON s.vehicle_id = v.vehicle_id
WHERE pay.paid_date > pay.due_date
ORDER BY days_late DESC
```

**SQL features used:** 4-table JOIN (payments + customers + sales + vehicles), DATEDIFF, DISTINCT, WHERE on date comparison

[INSERT QUERY RESULT SCREENSHOT HERE]

### Query 9: Who is our top salesperson? (Advanced)

Groups sales by employee and shows their total revenue and commission. The COUNT gives number of sales and SUM gives the dollar totals. Sorted by number of sales so the top performer shows up first.

**SQL:**
```sql
SELECT CONCAT(e.first_name, ' ', e.last_name) as salesperson,
       COUNT(s.sale_id) as num_sales,
       SUM(s.sale_price) as total_revenue,
       SUM(s.commission) as total_commission
FROM sales s
JOIN employees e ON s.employee_id = e.employee_id
GROUP BY e.employee_id
ORDER BY num_sales DESC
```

**SQL features used:** JOIN, GROUP BY, COUNT, SUM aggregates

[INSERT QUERY RESULT SCREENSHOT HERE]

### Query 10: Which warranties expire within 90 days? (Advanced)

This uses DATE_ADD to calculate the expiry date from start_date + length_months, then BETWEEN to check if it falls within the next 90 days. The DATEDIFF gives the exact number of days remaining so the dealership can follow up with customers before their coverage runs out.

**SQL:**
```sql
SELECT CONCAT(c.first_name, ' ', c.last_name) as customer_name,
       v.make, v.model, v.year,
       wi.warranty_type, wi.start_date,
       DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH) as expiry_date,
       DATEDIFF(DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH),
               CURDATE()) as days_remaining
FROM warranty_items wi
JOIN warranties w ON wi.warranty_id = w.warranty_id
JOIN vehicles v ON w.vehicle_id = v.vehicle_id
JOIN customers c ON w.customer_id = c.customer_id
WHERE DATE_ADD(wi.start_date, INTERVAL wi.length_months MONTH)
      BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 90 DAY)
ORDER BY expiry_date
```

**SQL features used:** DATE_ADD, DATEDIFF, BETWEEN with date range, 4-table JOIN

[INSERT QUERY RESULT SCREENSHOT HERE]

### Query 11: Where did repair costs go over budget? (Advanced)

The HAVING clause filters groups to only show vehicles where actual repair costs exceeded estimates. The difference between WHERE and HAVING is that WHERE filters individual rows before grouping, HAVING filters groups after. This query groups repairs by purchase and compares the totals.

**SQL:**
```sql
SELECT v.make, v.model, v.year,
       SUM(r.est_cost) as estimated_total,
       SUM(r.actual_cost) as actual_total,
       (SUM(r.actual_cost) - SUM(r.est_cost)) as over_budget
FROM repairs r
JOIN purchases p ON r.purchase_id = p.purchase_id
JOIN vehicles v ON p.vehicle_id = v.vehicle_id
GROUP BY p.purchase_id
HAVING SUM(r.actual_cost) > SUM(r.est_cost)
ORDER BY over_budget DESC
```

**SQL features used:** GROUP BY with HAVING, SUM aggregates, calculated column

[INSERT QUERY RESULT SCREENSHOT HERE]

### Query 12: Which customers bought more than one car? (Advanced)

Simple but useful. HAVING COUNT(s.sale_id) > 1 keeps only repeat buyers. This is good for identifying loyal customers who might be interested in future deals or referral bonuses.

**SQL:**
```sql
SELECT CONCAT(c.first_name, ' ', c.last_name) as customer_name,
       c.phone,
       COUNT(s.sale_id) as cars_bought,
       SUM(s.sale_price) as total_spent
FROM customers c
JOIN sales s ON c.customer_id = s.customer_id
GROUP BY c.customer_id
HAVING COUNT(s.sale_id) > 1
ORDER BY cars_bought DESC
```

**SQL features used:** GROUP BY with HAVING COUNT, aggregate functions

[INSERT QUERY RESULT SCREENSHOT HERE]

## Assumptions and Design Decisions

Here are the main assumptions I made and the reasoning behind the bigger design decisions.

1. **Used `condition_desc` instead of `condition`**: MySQL has `condition` as a reserved word, so I couldn't use it as a column name without backticks everywhere. Renaming to `condition_desc` was the simplest fix. It's not ideal but it's clear enough.

2. **Denormalized late payment stats on customers table**: The `num_late_payments` and `avg_days_late` columns on the customers table are technically redundant since they could be calculated from the payments table each time. But the payment form spec required showing these values, and recalculating on every page load with an aggregate query would be slower. I chose to store them directly and update them whenever a late payment is recorded. Normalization trades some redundancy for query performance, and this is one of those trade-offs.

3. **customer_id duplicated on payments**: The payments table has customer_id even though you could get it by joining through sales. It's there so I can quickly look up all payments for a customer without an extra JOIN. Another deliberate denormalization.

4. **AUTO_INCREMENT surrogate keys everywhere**: Every table uses an INT AUTO_INCREMENT primary key instead of composite or natural keys. It's simpler for foreign key references and avoids issues if natural values change. I went with surrogate keys because they're straightforward.

5. **Tables created in FK dependency order**: The db_setup.sql file creates tables in the right order (employees and vehicles first, then purchases, etc.) so the foreign key constraints don't fail. This way the script runs straight through without needing deferred constraint checks.

6. **Alberta-specific sample data**: All the seed data uses Lethbridge, Calgary, and Medicine Hat locations with Alberta postal codes. Keeps it realistic for the course context.

7. **Deliberate late payment data in seed**: I included some payment records where paid_date is after due_date on purpose, so the late payment reports and queries actually have data to show during testing and the demo.

## What I Learned

The biggest thing I took away from this project is how normalization actually works in practice. In class we walked through functional dependencies and decomposition on paper, but actually deciding where to denormalize for a real application was a different kind of challenge. It's one thing to identify a transitive dependency; it's another to decide whether the performance trade-off is worth it.

I'd never used PHP before this course. The procedural mysqli pattern from the tutorials turned out to be pretty straightforward once I got the hang of it. The hardest part was debugging SQL errors since PHP just gives you a generic error string and you have to figure out which part of the query is wrong.

Drawing the E-R diagram first and then mapping it to relational tables made the schema design way more structured than if I'd just started creating tables. It forced me to think about relationships and cardinalities before writing any SQL.

Setting up the foreign keys caught several data integrity issues during testing. I tried inserting a sale for a vehicle_id that didn't exist and MySQL blocked it. That's exactly what referential integrity is supposed to do, and it's way better than having orphaned records.

Writing the advanced queries (especially Query 7 with the correlated subquery) pushed me to actually understand how JOINs and subqueries work together. The examples from class used the banking database, but applying those patterns to a different domain helped it click.

## Future Improvements

If I had more time or was building this for a real dealership, here's what I'd add:

1. **User login system**: Right now anyone can access everything. A real system would need separate logins for buyers, salespeople, and managers, with different permissions for each. 

2. **Search and sorting on reports**: The reports have basic filters but you can't sort by clicking column headers or search within results. Adding JavaScript for client-side sorting would make the reports much more usable.

3. **Mobile/responsive layout**: The forms use HTML tables for layout, which doesn't work great on phones. The buyers at auctions mentioned wanting a portable version, so a responsive design would be a big improvement.

4. **Credit bureau integration**: The spec mentions that really bad customers get reported to the credit bureau. Right now there's no integration for that. An API connection to a credit reporting service would automate that process.

5. **Inventory alerts**: The dealership tries to keep no more than 50 cars on the lot. An alert system that warns when inventory is getting high (or when popular makes are running low) would help the buyers make better decisions at auctions.

Overall I'm pretty happy with how the project turned out. It covers the full lifecycle from buying cars to collecting payments, and the queries answer real business questions that a dealership would actually care about.
