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
    $sql = "SELECT * FROM orders ORDER BY received_datetime DESC";
    $result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #ddd; }
    </style>
</head>
<body>

<h2>Order Dashboard</h2>
<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Received</th>
            <th>Received By</th>
            <th>Tracking</th>
            <th>Location</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['orderID']); ?></td>
                <td><?php echo htmlspecialchars($row['received_datetime']); ?></td>
                <td><?php echo htmlspecialchars($row['received_by']); ?></td>
                <td><?php echo htmlspecialchars($row['tracking_number']); ?></td>
                <td><?php echo htmlspecialchars($row['current_location']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>

<?php $conn->close(); ?>