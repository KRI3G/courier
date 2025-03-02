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

    // Check if $_POST exists
    if (!empty($_POST)) {
        $postExists = true;

        // Some values that are not user inputted from this form
        $receivedDatetime = "CURRENT_TIMESTAMP"; # SQL generate timestamp
        $receivedBy = "grunt"; # Test variable for now, will remove later 
        $status = "Received"; # Leaving 'Received' as the default creation value for status

        // Setting the columns and values to be used in the SQL query
        $query = "INSERT INTO orders (";
        $columns = ["received_datetime", "received_by", "ticket_number", "tracking_number", "requestor_name", "items", "serial_numbers", "current_location", "notes", "status"];
        $values = [$receivedDatetime, $receivedBy, $_POST["ticket_number"], $_POST["tracking_number"], $_POST["requestor_name"], $_POST["items[]"], $_POST["serial_numbers[]"], $_POST["current_location"], $_POST["notes"], $status];
        
        // Iterate through $columns to add the columns to the SQL query
        foreach ($columns as $column) {
            if ($column === end($columns)) {
                $query = $query . $column;
            }
            else {
                $query = $query . $column . ", ";
            }
        }

        // Append and be ready to receive POST values for SQL query
        $query = $query . ") VALUES (";

        $exceptionValues = [$_POST["ticket_number"], $receivedDatetime]; # specific values that shouldn't be inserted as strings
        // Iterate through $values to add the values of the $_POST variable into the SQL query (janky, but it works)
        foreach ($values as $value) {
            if ($value === end($values)) {
                if (in_array($value, $exceptionValues)) { # Because you can never be too sure, and status is indeed at the end
                    $query = $query . $value;
                }
                else {
                    $query = $query . "\'" . $value . "\'";
                }
            }
            elseif (in_array($value, $exceptionValues)) { 
                $query = $query . $value . ", ";
            }
            else {
                $query = $query . "\'" . $value . "\'" . ", ";
            }
        }
        $query = $query . ")";
        

        #$conn->query($query);
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Entry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .item-group {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .item-group input {
            flex: 1;
        }
        .add-item {
            display: block;
            background: #007bff;
            color: white;
            padding: 8px;
            border: none;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .add-item:hover {
            background: #0056b3;
        }
        button[type="submit"] {
            background: #28a745;
            color: white;
            padding: 10px;
            border: none;
            margin-top: 15px;
            cursor: pointer;
            width: 100%;
            border-radius: 4px;
        }
        button[type="submit"]:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <div>
    <h1>Create an Entry</h1>
    <h3 id="successBanner" ></div>
    <form action="create.php" method="POST">
        <label for="ticket_number">Ticket #</label>
        <input type="number" id="ticket_number" name="ticket_number" required>

        <label for="requestor_name">Requestor Name</label>
        <input type="text" id="requestor_name" name="requestor_name" required>

        <label for="tracking_number">Tracking Number</label>
        <input type="text" id="tracking_number" name="tracking_number">

        <label>Items</label>
        <div id="items">
            <div class="item-group">
                <input type="text" name="items[]" placeholder="Item Name" required>
                <input type="number" name="quantities[]" placeholder="Quantity" min="1" required>
                <input type="text" name="serials[]" placeholder="Serial Number">
            </div>
        </div>
        <button type="button" class="add-item" onclick="addItem()">+ Add Item</button>

        <label for="current_location">Current Location</label>
        <select id="current_location" name="current_location" required>
            <option value="Back of the Tech-Shop">Back of the Tech-Shop</option>
            <option value="Drop-off Shelf">Drop-off Shelf</option>
            <option value="Building 3025">Building 3025</option>
            <option value="Other">Other</option>
        </select>

        <label for="notes">Extra Notes</label>
        <textarea id="notes" name="notes" rows="4"></textarea>

        <button type="submit">Submit Order</button>
    </form>

    <script>
        function addItem() {
            let container = document.getElementById("items");
            let div = document.createElement("div");
            div.classList.add("item-group");
            div.innerHTML = `
                <input type="text" name="items[]" placeholder="Item Name" required>
                <input type="number" name="quantities[]" placeholder="Quantity" required>
                <input type="text" name="serials[]" placeholder="Serial Number">
            `;
            container.appendChild(div);
        }
    </script>

    <script>
        // Display a banner informing the user the entry has been created
        if (<?php echo $postExists; ?>) {
            let banner = document.getElementById("successBanner")
            banner.innerHTML = "Success!" + " " + "<?php echo $query . "\\n" . $_POST["items[]"] . "and" . $_POST["serial_numbers[]"]; ?>";
            banner.style.backgroundColor = '#218838';
            banner.style.padding = '20px';
            banner.style.color= 'white';
            banner.style.borderRadius= '8px';
            banner.style.boxshadow = '0 0 10px rgba(0, 0, 0, 0.1)';
        }
    </script>
    </div>
</body>
</html>

<?php $conn->close(); ?>