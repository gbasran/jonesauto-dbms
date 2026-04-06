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

# kill any existing php server on 8000
lsof -ti:8000 | xargs kill 2>/dev/null

echo ""
echo "=== Done! Opening server at http://localhost:8000 ==="
echo "Press Ctrl+C to stop the server"
echo ""
php -S localhost:8000
