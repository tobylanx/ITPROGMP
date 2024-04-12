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

// Logout logic
if (isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$concertid = isset($_GET["concertid"]) ? $_GET["concertid"] : null;

if ($concertid) {
    $concertDetailsQuery = "SELECT cd.*, vd.venuename, i.poster, i.seatplan 
                            FROM concertdetails cd 
                            JOIN venuedetails vd ON cd.venueid = vd.venueid
                            LEFT JOIN images i ON cd.concertid = i.concertid
                            WHERE cd.concertid = ?";
    $stmt = mysqli_prepare($conn, $concertDetailsQuery);
    mysqli_stmt_bind_param($stmt, "i", $concertid);
    mysqli_stmt_execute($stmt);
    $concertDetailsResult = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($concertDetailsResult) > 0) {
        $row = mysqli_fetch_assoc($concertDetailsResult);
        $concertname = $row['concertname'];
        $artist = $row['artist'];
        $showdate = $row['showdate'];
        $venuename = $row['venuename'];
        $ticketingdate = $row['ticketingdate'];
        $posterImage = $row['poster']; // Retrieve poster image
        $seatplanImage = $row['seatplan']; // Retrieve seat plan image
    } else {
        $artist = "Concert not found.";
        $showdate = "";
        $venuename = "";
        $ticketingdate = "";
        $posterImage = ""; // Set poster image to empty string if not found
        $seatplanImage = ""; // Set seat plan image to empty string if not found
    }
}

$currentDate = date("Y-m-d");
$openForTicketingQuery = "SELECT * FROM concertdetails WHERE concertid = ? AND ticketingdate <= ?";
$stmt = mysqli_prepare($conn, $openForTicketingQuery);
mysqli_stmt_bind_param($stmt, "is", $concertid, $currentDate);
mysqli_stmt_execute($stmt);
$openForTicketingResult = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($openForTicketingResult) > 0) {
    $sectionPricesQuery = "SELECT s.*, v.venuename FROM seats s
                            JOIN concertdetails cd ON s.concertid = cd.concertid
                            JOIN venuedetails v ON cd.venueid = v.venueid
                            WHERE s.concertid = ?";
    $stmt = mysqli_prepare($conn, $sectionPricesQuery);
    mysqli_stmt_bind_param($stmt, "i", $concertid);
    mysqli_stmt_execute($stmt);
    $sectionPricesResult = mysqli_stmt_get_result($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concert Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('logo/bg2.png');
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
            backdrop-filter: blur(8px);
        }
        header {
            background-color: #ebe1c6;
            color: #fff;
            padding: 20px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        header img {
            height: 50px;
            margin-left: 20px;
        }
        .logout-btn {
            font-family: 'Archivo Black', sans-serif;
            color: white;
            padding: 10px 20px;
            margin-right: 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #ff101d;
            transition: background-color 0.3s ease;
        }
        .logout-btn:hover {
            background-color: #ff5e67;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            align-items: flex-start;
        }
        .concert-details,
        .seatplan,
        .transaction-form {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            flex: 1;
            max-width: 600px;
        }
        .poster img,
        .seatplan img {
            width: 100%;
            display: block;
        }
        .details,
        .transaction-form form {
            padding: 20px;
        }
        h2 {
            margin-top: 0;
            font-size: 24px;
            color: #333;
        }
        .details div {
            margin-bottom: 10px;
            font-size: 18px;
        }
        .section-selection select,
        .section-selection input[type="number"] {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            width: calc(100% - 22px); /* Adjust for border width */
        }
        .section-selection button {
            font-family: 'Archivo Black', sans-serif;
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #ff101d;
            transition: background-color 0.3s ease;
            width: 100%;
            text-align: center;
            text-decoration: none;
            margin-top: 5px;
        }
        .section-selection button:hover {
            background-color: #ff5e67;
        }
        .sold-out {
            color: #ff0000;
        }

        button{
            font-family: 'Archivo Black', sans-serif;
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #ff101d;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <a href="main.php"><img src="logo/Logo.png" alt="Logo"></a>
        <form action="" method="post">
            <button class="logout-btn" name="logout">LOGOUT</button>
        </form>
    </header>

    <div class="container">
        <div class="concert-details">
            <div class="poster">
                <img src="data:image/jpeg;base64,<?php echo base64_encode($posterImage); ?>" alt="Concert Poster">
            </div>
            <div class="details">
                <h2><?php echo $concertname; ?></h2>
                <div><strong>Artist:</strong> <?php echo $artist; ?></div>
                <div><strong>Show Date:</strong> <?php echo $showdate; ?></div>
                <div><strong>Venue:</strong> <?php echo $venuename; ?></div>
                <div><strong>Ticketing Date:</strong> <?php echo $ticketingdate; ?></div>
            </div>
        </div>

        <div class="seatplan">
            <?php if (!empty($seatplanImage)): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($seatplanImage); ?>" alt="Seat Plan">
            <?php endif; ?>
        </div>

        <div class="transaction-form">
            <?php if (mysqli_num_rows($openForTicketingResult) > 0): ?>
                <div class="section-selection">
                    <form action="processing.php" method="post" onsubmit="return validateTicketing()">
                        <label for="section">Select Section:</label>
                        <select name="section" id="section">
                            <?php while ($section = mysqli_fetch_assoc($sectionPricesResult)): ?>
                                <?php if ($section['quantityavail'] > 0): ?>
                                    <option value="<?php echo $section['seatid']; ?>"><?php echo $section['venuename'] . " - " . $section['type'] . " - ₱" . $section['price']; ?></option>
                                <?php else: ?>
                                    <option value="<?php echo $section['seatid']; ?>" class="sold-out"><?php echo $section['venuename'] . " - " . $section['type'] . " - ₱" . $section['price'] . " (Sold Out)"; ?></option>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </select>
                        <br><br>
                        <label for="quantity">Quantity (Max 5):</label>
                        <input type="number" name="quantity" id="quantity" min="1" max="5" required>
                        <br><br>
                        <input type="hidden" name="concertid" value="<?php echo $concertid; ?>">
                        <button type="submit">Proceed transaction</button>
                    </form>
                </div>
            <?php endif; ?>
            <form action="main.php" method="get">
                <button class="section-selection" type="submit">Back to Main Menu</button>
            </form>
        </div>
    </div>
</div>
    <script>
        function validateTicketing() {
            var selectedSection = document.getElementById("section");
            if (selectedSection.options[selectedSection.selectedIndex].classList.contains("sold-out")) {
                alert("Sorry, the selected section is sold out. Please choose another section.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
