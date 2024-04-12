<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Concert Status</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #ebe1c6;
        }

        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .menu-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 40%;
            margin-bottom: 20px;
        }

        .menu-container h2 {
            text-align: center;
            font-family: 'Archivo Black', sans-serif;
            color: black;
            font-size: 30px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        form {
            display: flex;
            justify-content: center;
        }

        input[type="submit"] {
            font-family: 'Archivo Black', sans-serif;
            background-color: #ff101d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            opacity: 0.8;
        }

        .return-btn {
            font-family: 'Archivo Black', sans-serif;
            background-color: #ff101d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
        }

        .return-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <?php
    // Connect to the database
    $conn = mysqli_connect("localhost:3308", "root", "", "concertdb");
    if (!$conn) {
        die("Unable to connect to the database: " . mysqli_connect_error());
    }

    // Check if a concert is chosen to close or reopen
    if(isset($_POST['action']) && isset($_POST['concert_id'])) {
        $concert_id = $_POST['concert_id'];
        $action = $_POST['action'];
        
        if($action == 'close') {
            // Update the status of the chosen concert to 'X' (closed)
            $update_sql = "UPDATE concertdetails SET status = 'X' WHERE concertid = $concert_id";
            $message = "Concert closed successfully.";
        } elseif ($action == 'reopen') {
            // Update the status of the chosen concert to 'O' (open)
            $update_sql = "UPDATE concertdetails SET status = 'O' WHERE concertid = $concert_id";
            $message = "Concert reopened successfully.";
        }
        
        $update_result = mysqli_query($conn, $update_sql);
        if($update_result) {
            echo "<p style='text-align: center; color: green;'>$message</p>";
        } else {
            echo "<p style='text-align: center; color: red;'>Error updating concert status: " . mysqli_error($conn) . "</p>";
        }
    }

    // Fetch concert details along with available seats quantity
    $sql = "SELECT cd.concertid, cd.concertname, cd.status, SUM(s.quantityavail) AS seats_available
            FROM concertdetails cd
            LEFT JOIN seats s ON cd.concertid = s.concertid
            GROUP BY cd.concertid";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Error retrieving data: " . mysqli_error($conn));
    }
    ?>
    
    <div class="menu-container">
        <h2>Manage Concert Status</h2>
        <table>
            <tr>
                <th>Concert ID</th>
                <th>Concert Name</th>
                <th>Status</th>
                <th>Seats Available</th>
                <th>Action</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
                <td><?php echo $row['concertid']; ?></td>
                <td><?php echo $row['concertname']; ?></td>
                <td><?php echo ($row['status'] == 'O' ? 'Open' : 'Closed'); ?></td>
                <td><?php echo $row['seats_available']; ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="concert_id" value="<?php echo $row['concertid']; ?>">
                        <?php if($row['status'] == 'O'): ?>
                            <input type="hidden" name="action" value="close">
                            <input type="submit" value="Close Concert">
                        <?php elseif($row['status'] == 'X'): ?>
                            <input type="hidden" name="action" value="reopen">
                            <input type="submit" value="Reopen Concert">
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <a class="return-btn" href="server.php">Return to Menu</a>

    <?php
    // Close database connection
    mysqli_close($conn);
    ?>
</body>
</html>
