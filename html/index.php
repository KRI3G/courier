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
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #333;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
            transition: 0.3s;
        }
    </style>
</head>
<body>
    <div>
        <center><h1>Delivery Log</h1></center>
        <div>Number of Records: <?php echo $ordersArray->num_rows ?></div>
        <table style="width:100%">
            <tr>
                <th style="width:10%">ID</th>
                <th style="width:15%">Ticket #</th>
                <th style="width:20%">Requestor</th>
                <th style="width:40%">Items</th>
                <th style="width:15%">Status</th>
            </tr>
            <?php 
            foreach ($ordersArray as $order) {
                echo "<tr>";
                echo "<td>" . $order['orderID'] . "</td>";
                echo "<td>" . "TDx". $order['ticket_number'] . "</td>";
                echo "<td>" . $order['requestor_name'] . "</td>";
                echo "<td>" . $order['items'] . "</td>";
                echo "<td>" . $order['status'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>