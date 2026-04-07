#!/bin/bash

echo "=== JonesAuto Setup ==="

# get sudo password upfront
sudo -v

# only install if not already there
if ! command -v php &> /dev/null || ! command -v mysql &> /dev/null; then
    echo "Installing PHP and MySQL..."
    sudo apt update && sudo apt install php mysql-server php-mysqli -y
else
    echo "PHP and MySQL already installed, skipping..."
fi

# start mysql if not running
echo "Starting MySQL..."
sudo service mysql start
sleep 1

# check it actually started
if ! sudo mysqladmin ping --silent 2>/dev/null; then
    echo "ERROR: MySQL didn't start"
    exit 1
fi

# drop and recreate fresh
echo "Setting up database..."
sudo mysql -e "DROP DATABASE IF EXISTS jonesauto;"
sudo mysql -e "CREATE DATABASE jonesauto; ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY ''; FLUSH PRIVILEGES;"

if [ $? -ne 0 ]; then
    echo "ERROR: Database setup failed"
    exit 1
fi

echo "Importing tables..."
sudo mysql -u root jonesauto < db_setup.sql

if [ $? -ne 0 ]; then
    echo "ERROR: Table import failed"
    exit 1
fi

echo "Importing seed data..."
sudo mysql -u root jonesauto < db_seed.sql

if [ $? -ne 0 ]; then
    echo "ERROR: Seed data import failed"
    exit 1
fi

# allow external DB connections (for DBeaver etc)
sudo sed -i 's/bind-address.*=.*/bind-address = 0.0.0.0/' /etc/mysql/mysql.conf.d/mysqld.cnf 2>/dev/null
sudo service mysql restart 2>/dev/null
sleep 1

# kill any existing php server on 8000
lsof -ti:8000 | xargs kill 2>/dev/null

echo ""
echo "=== Database ready ==="
echo "  Connect with DBeaver or MySQL Workbench:"
echo "    Host: localhost  Port: 3306"
echo "    User: root  Password: (blank)"
echo "    Database: jonesauto"
echo ""
echo "  Views (run these in DBeaver):"
echo "    SELECT * FROM v_customers;        -- all active customers"
echo "    SELECT * FROM v_employees;        -- all active employees"
echo "    SELECT * FROM v_inventory;        -- cars on the lot with costs"
echo "    SELECT * FROM v_purchases;        -- purchase history"
echo "    SELECT * FROM v_repairs;          -- all repairs with over/under budget"
echo "    SELECT * FROM v_sales;            -- sales with profit"
echo "    SELECT * FROM v_payments;         -- payments with late/on-time status"
echo "    SELECT * FROM v_late_customers;   -- customers with late payments"
echo "    SELECT * FROM v_warranties;       -- warranty items with expiry status"
echo "    SELECT * FROM v_employment;       -- customer employment history"
echo "    SELECT * FROM v_operations;       -- audit log"
echo "    SELECT * FROM v_deleted;          -- soft-deleted records"
echo ""
echo "=== Starting web server at http://localhost:8000 ==="
echo "  Press Ctrl+C to stop"
echo ""
php -S localhost:8000
