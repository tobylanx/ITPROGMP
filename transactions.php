<?php
    error_reporting(E_ALL); 
    ini_set('display_errors', 1);
    
    session_start();
    $conn = mysqli_connect("localhost:3308", "root", "", "concertdb");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Redirect to login page if user is not logged in
    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit;
    }

    $userId = $_SESSION["userid"];

    // Fetch user's transactions
    $transactionsQuery = "SELECT t.*, c.concertname, c.showdate, s.type, s.price 
                          FROM transactions t 
                          JOIN seats s ON t.seatid = s.seatid
                          JOIN concertdetails c ON t.concertid = c.concertid
                          WHERE t.userid = $userId
                          ORDER BY t.datetime DESC";
    $transactionsResult = mysqli_query($conn, $transactionsQuery);

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        header {
            background-color: #ff101d;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: bold;
        }

        header a {
            color: #fff;
            text-decoration: none;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-family: 'Archivo Black', sans-serif;
            text-align: left;
            margin-top: 0;
            font-size: 30px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <header>
        <h1>My Transactions</h1>
        <a href="main.php">Back to Main</a>
    </header>
    <div class="container">
        <h2>Transaction History</h2>
        <table>
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Concert Name</th>
                    <th>Show Date</th>
                    <th>Seat Type</th>
                    <th>Quantity</th>
                    <th>Total Amount</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    while ($row = mysqli_fetch_assoc($transactionsResult)) {
                        echo "<tr>";
                        echo "<td>{$row['transactionid']}</td>";
                        echo "<td>{$row['concertname']}</td>";
                        echo "<td>{$row['showdate']}</td>";
                        echo "<td>{$row['type']}</td>";
                        echo "<td>{$row['quantity']}</td>";
                        echo "<td>₱" . number_format($row['totalamount'], 2) . "</td>";
                        echo "<td>{$row['datetime']}</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
