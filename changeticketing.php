<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect("localhost:3308", "root", "") or die("Unable to connect!" . mysqli_error($conn));
mysqli_select_db($conn, "concertdb");

$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $concert_id = $_POST['selectconcert'];
    $new_ticketing_date = $_POST['ticketingdate'];
    $new_ticketing_time = $_POST['ticketingtime'];
    
    $show_date_query = "SELECT showdate FROM concertdetails WHERE concertid = '$concert_id'";
    $show_date_result = mysqli_query($conn, $show_date_query);
    $row = mysqli_fetch_assoc($show_date_result);
    $show_date = $row['showdate'];

    if (strtotime($new_ticketing_date) < strtotime(date("Y-m-d"))) {
        $error = "Error: Ticketing date cannot be in the past.";
    } elseif (strtotime($new_ticketing_date) > strtotime($show_date)) {
        $error = "Error: Ticketing date cannot be after the show date.";
    } else {
        $sql = "UPDATE concertdetails SET ticketingdate = '$new_ticketing_date', ticketingtime = '$new_ticketing_time' WHERE concertid = '$concert_id'";
        if (mysqli_query($conn, $sql)) {
            $success = true;
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Change Ticketing Date</title>
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

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 40%;
            max-height: 90%;
            overflow-y: auto;
        }

        .form-container h2 {
            text-align: center;
            font-family: 'Archivo Black', sans-serif;
            color: black;
            font-size: 30px;
            margin-bottom: 20px;
        }

        .form-container p {
            text-align: center;
        }

        .form-container button {
            background-color: #ff101d;
            color: #fff;
            border: none;

            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
            display: block;
            margin-top: 10px;
        }

        .form-container button:hover {
            background-color: #d60c1e;
        }

        .form-item {
            display: flex; 
            align-items: center;
        }

        .form-item label {
            font-family: 'Archivo Black', sans-serif;
            margin-right: 10px;
            display: inline-block; 
            width: 200px;
        }

        .form-item input[type="date"],
        .form-item input[type="time"],
        .form-item select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .submit-btn { 
            font-family: 'Archivo Black', sans-serif;
            background-color: #ff101d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
            display: block;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background-color: #d60c1e;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            margin-top: 20%; 
            padding: 20px;
            border: 1px solid #888;
            width: 20%;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            text-align: center; 
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .error-message {
            color: red;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="form-container">
        <h2>Change Ticketing Date</h2>
        <p>Select the concert you wish to change the ticketing date.</p>
        <form id="changeForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-item">
                <select name="selectconcert" required>
                    <option value="" selected disabled>Select Concert</option>
                    <?php
                        $sql = "SELECT * FROM concertdetails c JOIN venuedetails v ON v.venueid = c.venueid WHERE status = 'O'";
                        $result = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['concertid'] . "'>" . "[" . $row['concertid'] . "] " . $row['concertname'] . " by " . $row['artist'] . " at " . $row['venuename'] . "</option>";
                            }
                        }
                    ?>
                </select>
            </div>
            <div class="form-item" style="margin-top: 10px;">
                <label for="ticketingdate">Ticketing Date:</label>
                <input type="date" name="ticketingdate" required>
            </div>
            <div class="form-item" style="margin-top: 10px;">
                <label for="ticketingtime">Ticketing Time:</label>
                <input type="time" name="ticketingtime" required>
            </div>
            
            <p class="error-message"><?php echo $error; ?></p>
        
            <div style="display: flex; justify-content: space-between;">
                <button class="submit-btn" type="submit" style="margin-right: 10px;">Change Ticketing Date</button>
                <button class="back-btn" onclick="window.location.href='server.php';" style="font-family: 'Archivo Black', sans-serif;">Back to Menu</button>
            </div>
        </form>
    </div>

    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Ticketing Date successfully changed!</p>
        </div>
    </div>

    <script>
    var modal = document.getElementById("successModal");
    var span = document.getElementsByClassName("close")[0];

    window.onload = function() {
        <?php if ($success) { ?>
            modal.style.display = "block";
        <?php } ?>
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
