# JonesAuto DBMS

A full-stack database application for a used car dealership in Lethbridge that buys vehicles at auction, fixes them up, and sells them to customers. Built for CPSC 3660 (Database Systems) at the University of Lethbridge in April 2026.

The system handles the full lifecycle of a used car: buying it at auction, recording any repairs, selling it to a customer, setting up warranties, and tracking monthly payments. Beyond storing data, it produces reports the dealership would actually use: which cars are still on the lot, how much profit each sale made, and which customers are behind on payments.

Everything runs through a web interface. Employees fill out forms and click through reports; there is no login or role-based access control (that would be added in a real deployment but was not required for the assignment).

## Features

Six forms for data entry:

| Form | Purpose |
|---|---|
| Purchase | Record a vehicle bought at auction with any repair problems |
| Sale | Sell a vehicle to a customer and record financing |
| Warranty | Attach warranty coverage items to a completed sale |
| Payment | Record monthly payments from customers |
| Customer | Add, edit, or soft-delete customers |
| Employee | Add, edit, or soft-delete employees |

Six reports for management:

| Report | Purpose |
|---|---|
| Current Inventory | All available vehicles with cost info |
| Sales Report | All sales with profit calculations |
| Payment History | Payment records for a specific customer |
| Repair Summary | All repairs grouped by vehicle |
| Warranty Report | Warranty status and expiration dates |
| Late Payments | Customers who paid late and by how many days |

## Tech stack

PHP 7+ (procedural, mysqli), MySQL 5.7+, HTML and CSS with no frameworks. Built to run on WAMP locally.

## Database schema

Ten entities with one relationship table:

`employees`, `vehicles`, `customers`, `purchases`, `sales`, `employment_history`, `repairs`, `warranties`, `payments`, `warranty_items`.

Key design decisions:

- Every vehicle enters through a purchase at auction; no trade-ins or consignment.
- A vehicle can only be sold once; after a sale, status changes to `sold` and it disappears from inventory.
- Repairs tie to the original purchase, not to sales, since they represent prep work done before the car hits the lot.
- Warranties attach to sales rather than directly to vehicles, and a customer can add multiple warranty items to the same sale.
- Payments track both the due date and the actual paid date so the system can calculate how many days late each payment was.
- All tables use an `is_active` flag for soft-delete. Records are never physically removed so referential integrity stays intact across related tables.

The full design document with the ER diagram in Chen notation is at [`report/report.pdf`](report/report.pdf). The tutorial for using the system is at [`report/tutorial.pdf`](report/tutorial.pdf).

## Running it locally

Requires MySQL 5.7+ and PHP 7+. The start script creates the database, seeds sample data, and starts the PHP built-in server:

```bash
./startscript.sh
```

Then open http://localhost:8000 in a browser.

The script sets the local MySQL root user to password-less authentication. This is intended for a local dev sandbox only and matches the CPSC 3660 submission environment. Do not run this script on a shared or production system.

## Project structure

```
.
├── index.php                 Home page with stat cards
├── nav.php                   Shared navigation
├── style.css                 All styles
├── config.php                DB connection (local dev only)
├── db_setup.sql              Schema creation
├── db_seed.sql               Sample data
├── startscript.sh            One-command local setup
├── forms/                    Data-entry forms (six)
├── process/                  Form submission handlers (six)
├── reports/                  Reports (six)
├── queries/                  Ad-hoc query interface
└── report/                   LaTeX design document and tutorial PDFs
```

## Academic context

Course project for CPSC 3660 (Database Systems), University of Lethbridge, April 2026. The assignment specified the forms, the reports, and a subset of the schema requirements; the rest was open-ended design.

## License

MIT. See [`LICENSE`](LICENSE).
