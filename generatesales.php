<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Sales Report</title>
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
        }

        .menu-container h1 {
            text-align: center;
            font-family: 'Archivo Black', sans-serif;
            color: black;
            font-size: 30px;
            margin-bottom: 20px;
        }

        .select-concert {
            margin-bottom: 20px;
        }

        .select-concert label {
            font-family: 'Archivo Black', sans-serif;
            display: block;
            margin-top: 10px;
            margin-bottom: 10px;
            text-align: center;
        }

        .select-concert select {
            padding: 8px;
            font-size: 16px;
            width: 100%;
        }

        .select-concert input[type="submit"] {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #ff101d; /* Red color */
            color: #fff;
            border: none;
            margin-top: 15px;
            border-radius: 4px;
            width: 100%;
            transition: background-color 0.3s;
        }

        .select-concert input[type="submit"]:hover {
            background-color: #cc0e1a; /* Darker shade of red */
        }

        .back-to-menu {
            text-align: center;
        }

        .back-to-menu a {
            display: inline-block;
            padding: 10px 20px;
            width: 93%;
            background-color: #007bff; /* Blue color */
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .back-to-menu a:hover {
            background-color: #0056b3; /* Darker shade of blue */
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
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

// Fetch concert details for dropdown menu
$sql_concerts = "SELECT concertid, concertname FROM concertdetails";
$result_concerts = mysqli_query($conn, $sql_concerts);
if (!$result_concerts) {
    die("Error retrieving concert details: " . mysqli_error($conn));
}
?>

<div class="menu-container">
    <h1>Concert Sales Report</h1>

    <div class="select-concert">
    <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="concert">Select Concert:</label>
        <select name="concert" id="concert">
            <?php 
            while ($row = mysqli_fetch_assoc($result_concerts)) {
                $concert_id = $row['concertid'];
                $concert_name = $row['concertname'];
                // Check if the current option matches the selected concert ID
                $selected = ($concert_id == $_GET['concert']) ? 'selected' : '';
                echo "<option value='$concert_id' $selected>$concert_name</option>";
            }
            ?>
        </select>
        <input type="submit" value="Generate Report">
    </form>
</div>


    <div class="back-to-menu">
        <a href="server.php">Back to Menu</a>
    </div>

    <?php
    // Check if a concert is selected
    if (isset($_GET['concert'])) {
        $selected_concert_id = $_GET['concert'];

        // Get concert details for the selected concert
        $sql_concert = "SELECT cd.concertname, cd.showdate, v.venuename
                        FROM concertdetails cd
                        INNER JOIN venuedetails v ON cd.venueid = v.venueid
                        WHERE cd.concertid = $selected_concert_id";
        $result_concert = mysqli_query($conn, $sql_concert);
        if (!$result_concert) {
            die("Error retrieving concert details: " . mysqli_error($conn));
        }

        // Display selected concert information
        $row_concert = mysqli_fetch_assoc($result_concert);
        $concert_name = $row_concert['concertname'];
        $concert_date = $row_concert['showdate'];
        $venue_name = $row_concert['venuename'];
        echo "<h2>Sales Report for <em>$concert_name</em> at <em>$venue_name</em> on <em>$concert_date</em></h2>";

        // Get ticket sales for the selected concert
        $sql_sales = "SELECT s.type, SUM(t.quantity) AS total_sales, SUM(t.totalamount) AS total_revenue
                      FROM transactions t
                      INNER JOIN seats s ON t.seatid = s.seatid
                      WHERE t.concertid = $selected_concert_id
                      GROUP BY s.type";
        $result_sales = mysqli_query($conn, $sql_sales);
        if (!$result_sales) {
            die("Error retrieving sales information: " . mysqli_error($conn));
        }

        // Display sales report for the selected concert
        echo "<table>";
        echo "<tr><th>Ticket Category</th><th>Total Sales</th><th>Total Revenue</th></tr>";

        $total_revenue_concert = 0;
        // Display sales for each ticket category
        while ($row_sales = mysqli_fetch_assoc($result_sales)) {
            $ticket_category = $row_sales['type'];
            $total_sales = $row_sales['total_sales'];
            $total_revenue = $row_sales['total_revenue'];
            $total_revenue_concert += $total_revenue;

            echo "<tr>";
            echo "<td>$ticket_category</td>";
            echo "<td>$total_sales</td>";
            echo "<td>$total_revenue</td>";
            echo "</tr>";
        }

        // Display total revenue for the concert
        echo "<tr><td colspan='2'><strong>Total Revenue</strong></td><td><strong>$total_revenue_concert</strong></td></tr>";

        echo "</table>";
    }

    // Close database connection
    mysqli_close($conn);
    ?>

</div>

</body>
</html>
