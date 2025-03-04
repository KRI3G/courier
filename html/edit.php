<?php
    // Sanitation function (Thanks Chat)
    function sanitize_input($data) {
        if (is_array($data)) {
            return array_map('sanitize_input', $data); // Recursively sanitize arrays
        }

        $data = trim($data); // Remove extra spaces
        $data = stripslashes($data); // Remove backslashes
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Convert special chars to safe HTML entities
        return $data;
    }


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

    // Check if $_POST exists. This will be used to edit the current order
    if (!empty($_POST)) {
        $postExists = true;

        // Loop through $_POST and sanitize all values, thanks Chat
        if (!empty($_POST)) {
            $sanitized_post = array_map('sanitize_input', $_POST);
            
            // Example usage:
            $ticket_number = filter_var($sanitized_post['ticket_number'] ?? '', FILTER_VALIDATE_INT);
            $tracking_number = $sanitized_post['tracking_number'] ?? '';
            $requestor_name = $sanitized_post['requestor_name'] ?? '';
        
            // Validate required fields
            if (!$ticket_number) {
                die("Error: Invalid or missing ticket number.");
            }
            
            // Now $sanitized_post is safe to use in your queries
        }


        // Some values that are not user inputted from this form
        $receivedDatetime = "CURRENT_TIMESTAMP"; # SQL generate timestamp
        $receivedBy = "grunt"; # Test variable for now, will remove later 
        $status = "Received"; # Leaving 'Received' as the default creation value for status
        // Specifically gonna deal with items and their bs
        $items = [];
        for ($i = 0; $i < count($_POST["items"]); $i++) {
            $items[] = [
                "name" => $_POST["items"][$i],
                "quantity" => $_POST["quantities"][$i],
                "serialNums" => $_POST["serials"][$i]
            ];
        }
        $items_json = json_encode($items);

        // Setting the columns and values to be used in the SQL query
        $query = "INSERT INTO orders (";
        $columns = ["received_datetime", "received_by", "ticket_number", "tracking_number", "requestor_name", "items", "current_location", "notes", "status"];
        $values = [$receivedDatetime, $receivedBy, $_POST["ticket_number"], $_POST["tracking_number"], $_POST["requestor_name"], $items_json, $_POST["current_location"], $_POST["notes"], $status];
        
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
                    $query = $query . "'" . $value . "'";
                }
            }
            elseif (in_array($value, $exceptionValues)) { 
                $query = $query . $value . ", ";
            }
            else {
                $query = $query . "'" . $value . "'" . ", ";
            }
        }
        $query = $query . ")";
        

        $conn->query($query);
    }

    // Check if $_GET is empty. Used to recall specific order details
    if (!empty($_GET)) {
        $orderID = intval($_GET['id']);
        $getQuery = "SELECT * FROM orders WHERE orderID = $orderID";

        $result = $conn->query($getQuery);
        if ($result) {
            $order = $result->fetch_assoc();
            echo $order;
        }
        else {
            echo "<center><h1>Error " . $conn->error . "</h1></center>";
        }
    } 
    else {
        echo "<center><h1>Error getting details for order. Please ensure ?id= is not empty.</h1></center>";
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
        header {
            display:flex;
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
            background: #500000;
            color: white;
            padding: 10px;
            border: none;
            margin-top: 15px;
            cursor: pointer;
            width: 100%;
            border-radius: 4px;
        }
        button[type="submit"]:hover {
            background: #3e0000;
        }


        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background-color: #500000;
            padding-top: 60px;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            box-shadow: 2px 0px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        /* Sidebar links */
        .sidebar a {
            padding: 15px;
            display: block;
            color: white;
            text-decoration: none;
            font-size: 18px;
        }

        .sidebar a:hover {
            background-color: #575757;
        }

        /* Close button */
        .close-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
        }

        /* Open sidebar button */
        .sidebar-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #500000;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
        }

        /* Show the sidebar */
        .sidebar.show {
            transform: translateX(0);
        }
    </style>
</head>
<body>
    <div>
        <div class="header">
            <span>
                <button id="openSidebar" class="sidebar-btn">☰</button>
            </span>
            <span>
                <center><h1>Edit an Entry</h1></center>
            </span>
        </div>
        
        
        <h3 id="successBanner" ></div>

        <form action="create.php" method="POST">

            <label for="ticket_number">Ticket #</label>
            <input type="number" id="ticket_number" name="ticket_number" value="<?php echo $order['ticket_number'];?>" required>

            <label for="requestor_name">Requestor Name</label>
            <input type="text" id="requestor_name" name="requestor_name" value="<?php echo $order['requestor_name'];?>" required>

            <label for="tracking_number">Tracking Number</label>
            <input type="text" id="tracking_number" name="tracking_number" value="<?php echo $order['tracking_number'];?>">

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
            <textarea id="notes" name="notes" rows="4"><?php echo $order['notes'];?></textarea>

            <button type="submit">Submit Order</button>
        </form>


        <div id="sidebar" class="sidebar">
            <button id="closeSidebar" class="close-btn">&times;</button>
            <div  style="text-align:center; margin-bottom:30px">
                <img style="width:65%; border:5px solid grey;" src="/images/logo.png" alt="Technology Services logo">
                <h1 style="color:white">Courier</h1>
            </div>
            <a href="/">Home</a>
            <a href="/create.php">Create</a>
        </div>
    </div>


    <script>
        // Script for toggling side bar on and off
        document.addEventListener("DOMContentLoaded", function () {
            const sidebar = document.getElementById("sidebar");
            const openBtn = document.getElementById("openSidebar");
            const closeBtn = document.getElementById("closeSidebar");
        
            openBtn.addEventListener("click", () => {
                sidebar.classList.add("show");
            });
        
            closeBtn.addEventListener("click", () => {
                sidebar.classList.remove("show");
            });
        
            // Close sidebar when clicking outside of it
            document.addEventListener("click", (event) => {
                if (!sidebar.contains(event.target) && !openBtn.contains(event.target)) {
                    sidebar.classList.remove("show");
                }
            });
        }); 
    </script>

    
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
                banner.innerHTML = "Success!";
                banner.style.backgroundColor = '#218838';
                banner.style.padding = '20px';
                banner.style.color= 'white';
                banner.style.borderRadius= '8px';
                banner.style.boxshadow = '0 0 10px rgba(0, 0, 0, 0.1)';
            }
        </script>

</body>
</html>

<?php $conn->close(); ?>