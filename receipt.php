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

    // Check if form data is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $sectionId = $_POST["section"];
        $quantity = $_POST["quantity"];
        $concertId = $_POST["concertid"];
        $userId = $_SESSION["userid"];

        // Get seat details
        $seatDetailsQuery = "SELECT * FROM seats WHERE seatid = $sectionId";
        $seatDetailsResult = mysqli_query($conn, $seatDetailsQuery);

        if (mysqli_num_rows($seatDetailsResult) == 1) {
            $seatDetails = mysqli_fetch_assoc($seatDetailsResult);
            $price = $seatDetails["price"];

            // Calculate total amount
            $totalAmount = $price * $quantity;

            // Check if enough quantity is available
            if ($seatDetails["quantityavail"] >= $quantity) {
                // Update quantity available
                $newQuantity = $seatDetails["quantityavail"] - $quantity;
                $updateQuantityQuery = "UPDATE seats SET quantityavail = $newQuantity WHERE seatid = $sectionId";
                $updateQuantityResult = mysqli_query($conn, $updateQuantityQuery);

                // Insert transaction record
                $insertTransactionQuery = "INSERT INTO transactions (concertid, userid, seatid, quantity, totalamount, datetime) VALUES ($concertId, $userId, $sectionId, $quantity, $totalAmount, NOW())";
                $insertTransactionResult = mysqli_query($conn, $insertTransactionQuery);

                if ($updateQuantityResult && $insertTransactionResult) {
                    // Retrieve transaction ID
                    $transactionId = mysqli_insert_id($conn);

                    // Redirect to success page
                    header("Location: receipt_success.php?transactionid=$transactionId");
                    exit;
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            } else {
                echo "Insufficient quantity available.";
            }
        } else {
            echo "Seat not found.";
        }
    }
?>
