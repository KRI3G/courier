<?php
    // Find the document root
    
    // Include database connection
    // Connecting to the database
    $courierRoot = dirname(realpath($_SERVER['DOCUMENT_ROOT'])) . DIRECTORY_SEPARATOR; // Directory above DocumentRoot
    $dbEnvPath = $courierRoot . '.env'; // /var/www/courier/.env is used to pull database credentials and information
    if (!file_exists($dbEnvPath)) {
        die("Error: .env file not found at $dbEnvPath. Please refer to documentation");
    }
    $env = parse_ini_file($dbEnvPath);
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
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
        }
        header{
            display:flex;
        }


        .orders {
            overflow-x: auto;
            max-width: 100%;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #500000;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
            transition: 0.3s;
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


        @media (max-width: 768px) {
            body {
                font-size: 10px;
                padding: 5px;
            }
        
            h1 {
                font-size: 22px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin: 5px 0;
                font-size: 14px;
                text-align: left;
            }

            th, td {
                padding: 8px;
                border: 1px solid #ddd;
            }

            .table-container {
                margin: 0;
                padding: 0;
            }
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
                <center><h1>Delivery Log</h1></center>
            </span>
        </div>

        <div>Number of Records: <?php echo $ordersArray->num_rows; ?></div>

        <div id="orders"></div>

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
    // Script for switching tables dependent on device 
        function isMobileScreen() {
            return window.innerWidth <= 768; // Adjust threshold as needed
        }

        let container = document.getElementById("orders");
        let div = document.createElement("div");
        div.classList.add("item-group");
        
        // If it is a mobile device screen, adjust the orders table to have less, but relevant, columns
        if (isMobileScreen()) {
            div.innerHTML = `
                <table style="width:100%">
                    <tr>
                        <th style="width:5%">ID</th>
                        <th style="width:25%">Ticket #</th>
                        <th style="width:30%">Requestor</th>
                        <th style="width:20%">Location</th>
                        <th style="width:20%">Status</th>
                    </tr>
                    <?php 
                    foreach ($ordersArray as $order) {
                        echo "<tr>";
                        echo "<td onclick=\"window.location.href='/edit.php?id=" . $order['orderID'] . "';\" style=\"cursor: pointer; text-align: center;\">" . $order['orderID'] . "</td>";
                        echo "<td>";
                            // Link to TDx
                            echo "<a target=\"_blank\" rel=\"noopener noreferrer\" href='https://service.tamu.edu/TDNext/Apps/34/Tickets/TicketDet?TicketID=" . $order['ticket_number'] . "'>"; 
                                echo "TDx". $order['ticket_number'];
                            echo "</a>";  
                        echo "</td>";  
                        echo "<td>" . $order['requestor_name'] . "</td>";
                        echo "<td>" . $order['current_location'] . "</td>";
                        echo "<td style='text-align: center;'>" . $order['status'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            `;
            container.appendChild(div);
        // if it is not, go ahead and spew the important stuff out
        } else {
            div.innerHTML = `
                <table style="width:100%">
                    <tr>
                        <th style="width:7.5%">ID</th>
                        <th style="width:10%">Ticket #</th>
                        <th style="width:15%">Requestor</th>
                        <th style="width:42.5%">Items</th>
                        <th style="width:15%">Location</th>
                        <th style="width:10%">Status</th>
                    </tr>
                    <?php 
                    foreach ($ordersArray as $order) {
                        echo "<tr>";
                        echo "<td onclick=\"window.location.href='/edit.php?id=" . $order['orderID'] . "';\" style=\"cursor: pointer; text-align: center;\">" . $order['orderID'] . "</td>";
                        echo "<td>";
                            // Link to TDx link
                            echo "<a target=\"_blank\" rel=\"noopener noreferrer\" href='https://service.tamu.edu/TDNext/Apps/34/Tickets/TicketDet?TicketID=" . $order['ticket_number'] . "'>"; 
                                echo "TDx". $order['ticket_number'];
                            echo "</a>";  
                        echo "</td>";                   
                        echo "<td>" . $order['requestor_name'] . "</td>";
                        echo "<td>";
                        $items_decoded = json_decode($order["items"], true);
                        foreach ($items_decoded as $item) {
                            echo $item["quantity"] . "x " . $item["name"] . ", S/N: " . $item["serialNums"];
                            echo "<br>";
                        }
                        echo "</td>"; 
                        echo "<td>" . $order['current_location'] . "</td>";
                        echo "<td style='text-align: center;'>" . $order['status'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            `;
            container.appendChild(div);
        }

        

    </script>

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
    
</body>
</html>

<?php $conn->close(); ?>