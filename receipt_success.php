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

    if(isset($_GET["transactionid"])) {
        $transactionId = $_GET["transactionid"];
        $userId = $_SESSION["userid"];

        // Fetch transaction details
        $transactionQuery = "SELECT t.*, c.concertname, c.showdate, s.type, s.price FROM transactions t 
                            JOIN seats s ON t.seatid = s.seatid
                            JOIN concertdetails c ON t.concertid = c.concertid
                            WHERE transactionid = $transactionId AND t.userid = $userId";
        $transactionResult = mysqli_query($conn, $transactionQuery);

        if(mysqli_num_rows($transactionResult) > 0) {
            $transactionDetails = mysqli_fetch_assoc($transactionResult);
            $concertName = $transactionDetails['concertname'];
            $showDate = $transactionDetails['showdate'];
            $seatType = $transactionDetails['type'];
            $quantity = $transactionDetails['quantity'];
            $seatPrice = $transactionDetails['price'];
            $totalAmount = $transactionDetails['totalamount'];

            // Generate a random seat number based on the quantity of tickets
            $seatNumber = generateSeatNumber($quantity);
        } else {
            echo "Transaction not found.";
        }
    } else {
        echo "Transaction ID not provided.";
    }

    // Function to generate a random seat number
    function generateSeatNumber($quantity) {
        $seatNumbers = [];
        for ($i = 0; $i < $quantity; $i++) {
            $seatNumbers[] = rand(100, 999);
        }
        // Return the seat numbers as a comma-separated string
        return implode(", ", $seatNumbers);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .receipt-container {
            max-width: 600px;
            margin: 50px auto; /* Center the container horizontally */
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center; /* Center the content inside the container */
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .receipt-details {
            margin-bottom: 20px;
        }

        .receipt-details p {
            margin: 10px 0;
        }

        .receipt-details strong {
            font-weight: bold;
        }

        .return-btn {
            display: inline-block;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            background-color: #ff101d;
            color: #fff;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .return-btn:hover {
            background-color: #ff5e67;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <h1>Transaction Receipt</h1>
        <div class="receipt-details">
            <p><strong>Transaction ID:</strong> <?php echo $transactionId; ?></p>
            <p><strong>User:</strong> <?php echo $_SESSION["username"]; ?></p>
            <p><strong>Concert Name:</strong> <?php echo $concertName; ?></p>
            <p><strong>Show Date:</strong> <?php echo $showDate; ?></p>
            <p><strong>Seat Type:</strong> <?php echo $seatType; ?></p>
            <p><strong>Seat Number:</strong> <?php echo $seatNumber; ?></p>
            <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
            <p><strong>Price per Seat:</strong> ₱<?php echo number_format($seatPrice, 2); ?></p>
            <p><strong>Total Amount:</strong> ₱<?php echo number_format($totalAmount, 2); ?></p>
        </div>
        <a href="main.php" class="return-btn">Return to Main Menu</a>
    </div>
</body>
</html>
