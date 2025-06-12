<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'shoe_store';

// Step 1: Connect to MySQL server (no DB selected yet)
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Step 2: Create database if not exists
$dbExists = $conn->query("SHOW DATABASES LIKE '$dbname'")->num_rows > 0;
if (!$dbExists) {
    if (!$conn->query("CREATE DATABASE $dbname")) {
        die("❌ Failed to create database: " . $conn->error);
    }
}

// Step 3: Select the database
$conn->select_db($dbname);

// Step 4: Create tables
$tables = [
    "customers" => "
        CREATE TABLE IF NOT EXISTS customers (
            customer_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            address TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",

    "cart" => "
        CREATE TABLE IF NOT EXISTS cart (
            cart_id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NOT NULL,
            product_name VARCHAR(255) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
        )",

    "purchases" => "
        CREATE TABLE IF NOT EXISTS purchases (
            purchase_id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NOT NULL,
            purchase_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
        )",

    "purchase_items" => "
        CREATE TABLE IF NOT EXISTS purchase_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            purchase_id INT NOT NULL,
            product_name VARCHAR(255),
            price DECIMAL(10,2),
            quantity INT,
            image VARCHAR(255),
            FOREIGN KEY (purchase_id) REFERENCES purchases(purchase_id) ON DELETE CASCADE
        )"
];

// Step 5: Execute each table creation query
foreach ($tables as $name => $sql) {
    if (!$conn->query($sql)) {
        die("❌ Error creating table '$name': " . $conn->error);
    }
}
?>
