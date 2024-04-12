<?php
    // Check if form data is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data from POST method
        $selectedSection = $_POST['section'];
        $quantity = $_POST['quantity'];
        $concertid = $_POST['concertid'];

        // Fetch additional details based on the selected section and concert ID
        // You can perform any necessary processing here, such as calculating total amount, updating database, etc.

        // Fetch seat details
        $conn = mysqli_connect("localhost:3308", "root", "", "concertdb");
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $seatQuery = "SELECT * FROM seats WHERE seatid = $selectedSection";
        $seatResult = mysqli_query($conn, $seatQuery);
        $seatDetails = mysqli_fetch_assoc($seatResult);
        $seatType = $seatDetails['type'];
        $seatPrice = $seatDetails['price'];

        // Fetch concert details
        $concertQuery = "SELECT cd.*, vd.venuename FROM concertdetails cd JOIN venuedetails vd ON cd.venueid = vd.venueid WHERE cd.concertid = $concertid";
        $concertResult = mysqli_query($conn, $concertQuery);
        $concertDetails = mysqli_fetch_assoc($concertResult);
        $concertname = $concertDetails['concertname'];
        $showdate = $concertDetails['showdate'];

        // Calculate total amount
        $totalAmount = $quantity * $seatPrice;

        // Close database connection
        mysqli_close($conn);
    } else {
        // If form data is not submitted properly, redirect back to the main page
        header("Location: main.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 600px;
            width: 100%;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center; /* Center align content */
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        h2 {
            color: #555;
            margin-top: 20px;
        }

        .container p {
            margin: 10px 0;
        }

        .container strong {
            font-weight: bold;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 20px; /* Add space between buttons */
        }

        input[type="submit"] {
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            background-color: #ff101d;
            color: #fff;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            cursor: pointer;
            margin: 0 10px; /* Add space between buttons */
        }

        input[type="submit"]:hover {
            background-color: #ff5e67;
        }

        input[type="hidden"] {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Transaction Details</h1>
        <h2>Concert Details:</h2>
        <p><strong>Concert Name:</strong> <?php echo $concertname; ?></p>
        <p><strong>Show Date:</strong> <?php echo $showdate; ?></p>
        <h2>Seat Details:</h2>
        <p><strong>Seat Type:</strong> <?php echo $seatType; ?></p>
        <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
        <p><strong>Price per Seat:</strong> ₱<?php echo number_format($seatPrice, 2); ?></p>
        <h2>Total Amount to Pay:</h2>
        <p><strong>₱<?php echo number_format($totalAmount, 2); ?></strong></p>
        
        <div class="button-container">
            <form action="receipt.php" method="post">
                <input type="hidden" name="section" value="<?php echo $selectedSection; ?>">
                <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
                <input type="hidden" name="concertid" value="<?php echo $concertid; ?>">
                <input type="hidden" name="totalAmount" value="<?php echo $totalAmount; ?>">
                <input type="submit" name="pay_now" value="Pay Now">
            </form>
            <form action="main.php" method="post">
                <input type="submit" name="cancel" value="Cancel">
            </form>
        </div>
    </div>
</body>
</html>
