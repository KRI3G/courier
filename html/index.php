<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courier</title>
</head>
<body>
    <?php
    echo "HOWDY";
    $env = parse_ini_file(__DIR__ . '/.env');
    $conn = new mysqli($env=['DB_LOCATION'], $env=['DB_USER'], $env=['DB_PASSWORD'], $env=['DB_NAME']);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    echo "Connected Succesfully";
    ?>
</body>
</html>