<?php
    // Include database connection
    // Connecting to the database
    $courierRoot = '/var/www/courier/'; // DocumentRoot is /var/www/courier/html
    $envPath = $courierRoot . '.env'; // /var/www/courier/.env is used to pull database credentials and information
    if (!file_exists($envPath)) {
        die("Error: .env file not found at $envPath. Please refer to documentation");
    }
    $env = parse_ini_file($envPath);
    $conn = new mysqli($env['DB_LOCATION'], $env['DB_USER'], $env['DB_PASSWORD'], $env['DB_NAME']);


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch all orders
    $ordersArray = $conn->query("SELECT * FROM orders ORDER BY received_datetime DESC");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
</head>
<body>
    <div>
        <center><h1>Create an entry</h1></center>
        <center><div>
            test
        </div></center>
    </div>
</body>
</html>

<?php $conn->close(); ?>