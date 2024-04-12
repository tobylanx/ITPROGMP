<?php
// Function to retrieve and display announcement messages
function displayAnnouncements($conn) {
    $announcementQuery = "SELECT * FROM announcements ORDER BY annid DESC";
    $announcementResult = mysqli_query($conn, $announcementQuery);

    if (mysqli_num_rows($announcementResult) > 0) {
        echo "<div class='announcements'>";
        echo "<h2>Announcements</h2>";
        while ($row = mysqli_fetch_assoc($announcementResult)) {
            echo "<div class='announcement'>";
            echo "<p>{$row['message']}</p>";
            echo "</div>";
        }
        echo "</div>";
    }
}

error_reporting(E_ALL); 
ini_set('display_errors', 1);

session_start();
$conn = mysqli_connect("localhost:3308", "root", "") or die("Unable to connect!" . mysqli_error());
mysqli_select_db($conn, "concertdb");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$currentDate = date("Y-m-d H:i:s");
$openForTicketingQuery = "SELECT c.*, v.venuename, i.poster 
FROM concertdetails c 
INNER JOIN venuedetails v ON c.venueid = v.venueid
LEFT JOIN images i ON c.concertid = i.concertid
WHERE c.ticketingdate <= '$currentDate'
ORDER BY c.ticketingdate, c.ticketingtime";

$openForTicketingResult = mysqli_query($conn, $openForTicketingQuery);

$upcomingConcertsQuery = "SELECT c.*, v.venuename, i.poster 
                            FROM concertdetails c 
                            INNER JOIN venuedetails v ON c.venueid = v.venueid
                            LEFT JOIN images i ON c.concertid = i.concertid
                            WHERE c.ticketingdate > '$currentDate'
ORDER BY c.ticketingdate, c.ticketingtime";
$upcomingConcertsResult = mysqli_query($conn, $upcomingConcertsQuery);
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
            background-image: url('logo/bg2.png');
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
            backdrop-filter: blur(8px);
        }

        header {
            background-color: #ebe1c6;
            color: #fff;
            padding: 5px 20px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }

        header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: bold;
            padding-left: 13px;
        }

        header p {
            margin: 5px 0;
            font-size: 16px;
            font-style: italic;
        }

        header img{
            padding-left: 30px;
            height: 65px;
        }

        .logout-btn, .account-btn {
            font-family: 'Archivo Black', sans-serif;
            color: white;
            padding: 14px 20px;
            margin: 8px 10px; /* Adjusted margin */
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #ff101d;
        }

        .logout-btn:hover, .account-btn:hover {
            background-color: #ff5e67;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #ebe1c6;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #ff101d;
            color: white;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .container {
            max-width: 1300px;
            margin: 20px auto;
            padding: 20px;
        }

        h2 {
            font-family: 'Archivo Black', sans-serif;
            text-align: left;
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 50px;
        }

        .concert-section {
            margin-bottom: 20px;
            overflow-x: auto;
            white-space: nowrap;
        }

        .concert-section h3 {
            margin-top: 0;
        }

        .concert-list {
            list-style-type: none;
            padding: 0;
            display: inline-block; 
        }

        .concert-list li {
            display: inline-block; 
            margin-right: 20px;
            vertical-align: top;
            text-align: center;
        }

        .concert-img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .showing-on {
            display: block;
            margin-top: 20px;
            text-align: center;
        }

        .concert-item {
            position: relative;
            width: 400px; 
            height: 650px; 
        }

        .concert-img {
            width: 400px; 
            height: 550px; 
            border-radius: 10px;
            transition: opacity 0.5s ease;
        }

        .buy-ticket {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #F5E8DD;
            color: #ff101d;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.5s ease;
            text-decoration: none;
            font-size: 35px;
            cursor: pointer;
            font-family: 'Archivo Black', sans-serif;
        }

        .concert-item:hover .concert-img {
            opacity: 0.3; 
        }

        .concert-item:hover .buy-ticket {
            opacity: .70; 
        }

        .concert-info {
            margin-top: 10px;
            text-align: center;
        }

        .announcements {
            margin-top: 20px;
        }

        .announcement {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .announcement p {
            margin-bottom: 10px;
        }
    </style>
        
    </style>
</head>
<body>
    <header>
        <div class="dropdown">
            <button class="account-btn">ACCOUNT</button>
            <div class="dropdown-content">
                <a href="account.php">View Account Details</a>
                <a href="transactions.php">View Transactions</a>
            </div>
        </div>
        <a href="main.php"><img src="logo/Logo.png"></a>
        <form action="" method="post">
            <button class="logout-btn" name="logout">LOGOUT</button>
        </form>
    </header>

    <div class="container">
        <?php displayAnnouncements($conn); ?>

        <h2>FEATURED SHOWS</h2>
        <div class="concert-section">
            <ul class="concert-list">
                <?php
                while ($row = mysqli_fetch_assoc($openForTicketingResult)) {
                    // Retrieve poster image from database
                    $posterImage = base64_encode($row['poster']);
                    echo "<li class='concert-item'>";
                    echo "<a href='concertdetails.php?concertid={$row['concertid']}'>";
                    echo "<img class='concert-img' src='data:image/jpeg;base64,{$posterImage}' alt='{$row['artist']}'><span class='buy-ticket'><strong>BUY TICKET</strong></span></a>";
                    echo "<div class='concert-info'><strong>{$row['artist']}</strong><br>{$row['showdate']}<br>{$row['venuename']}<br>Ticketing Date: {$row['ticketingdate']}</div>";
                    echo "</li>";
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="container">
        <h2>UPCOMING SHOWS</h2>
        <div class="concert-section">
            <ul class="concert-list">
                <?php
                while ($row = mysqli_fetch_assoc($upcomingConcertsResult)) {
                    // Retrieve poster image from database
                    $posterImage = base64_encode($row['poster']);
                    echo "<li class='concert-item'>";
                    echo "<a href='concertdetails.php?concertid={$row['concertid']}'>";
                    echo "<img class='concert-img' src='data:image/jpeg;base64,{$posterImage}' alt='{$row['artist']}'><span class='buy-ticket'>BUY TICKET</span></a>";
                    echo "<div class='concert-info'><strong>{$row['artist']}</strong><br>{$row['showdate']}<br>{$row['venuename']}<br>Ticketing Date: {$row['ticketingdate']}</div>";
                    echo "</li>";
                }
                ?>
            </ul>
        </div>
    </div>
</body>
</html>
