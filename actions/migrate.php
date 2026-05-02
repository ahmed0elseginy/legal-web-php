<?php
require_once __DIR__ . "/../includes/config.php";

$conn = getDb();

// Manual check if column exists (more compatible than ADD COLUMN IF NOT EXISTS)
function addColumn($conn, $table, $column, $definition)
{
    $check = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    if ($check->num_rows == 0) {
        if ($conn->query("ALTER TABLE `$table` ADD `$column` $definition")) {
            echo "Successfully added $column to $table.<br>";
        } else {
            echo "Error adding $column: " . $conn->error . "<br>";
        }
    } else {
        echo "Column $column already exists in $table.<br>";
    }
}

addColumn($conn, 'users', 'remember_token', 'VARCHAR(255) DEFAULT NULL');
addColumn($conn, 'cases', 'document_url', 'VARCHAR(255) DEFAULT NULL');

// Seed a test request if lawyer 1 exists
$testReq = $conn->query("SELECT id FROM service_requests WHERE lawyer_id = 1 LIMIT 1");
if ($testReq && $testReq->num_rows == 0) {
    $conn->query("INSERT INTO service_requests (client_id, lawyer_id, service_id, service_name, client_name, status) 
                  VALUES (2, 1, 1, 'استشارة قانونية', 'Test Client', 'pending')");
    echo "Seeded a test request for Lawyer ID 1.<br>";
}

echo "<br><strong>Migration complete.</strong><br>";
echo "If you still have errors, run this in phpMyAdmin:<br>";
echo "<pre>ALTER TABLE users ADD remember_token VARCHAR(255) DEFAULT NULL;
ALTER TABLE cases ADD document_url VARCHAR(255) DEFAULT NULL;</pre>";
