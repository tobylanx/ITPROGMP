<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

$conn = mysqli_connect("localhost:3308", "root", "") or die("Unable to connect!" . mysqli_error());
mysqli_select_db($conn, "concertdb");

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $concertname = $_POST['concertname'];
    $artist = $_POST['artist'];
    $showdate = $_POST['showdate'];
    $showtime = $_POST['showtime'];
    $ticketingdate = $_POST['ticketingdate'];
    $ticketingtime = $_POST['ticketingtime'];
    $venueid = $_POST['venue'];
    
    $current_date = date("Y-m-d");
    if ($showdate < $current_date || $ticketingdate < $current_date) {
        $error_message = "Show date or ticketing date cannot be in the past.";
    } elseif ($ticketingdate >= $showdate) {
        $error_message = "Ticketing date must be before the show date.";
    } else {
        $concert_query = "INSERT INTO concertdetails (venueid, concertname, artist, showdate, showtime, ticketingdate, ticketingtime, status) 
                  VALUES ('$venueid', '$concertname', '$artist', '$showdate', '$showtime', '$ticketingdate', '$ticketingtime', 'O')";
        mysqli_query($conn, $concert_query);

        $concertid = mysqli_insert_id($conn);
        foreach ($_POST['section'] as $sectionData) {
            $sectionName = $sectionData['name'];
            $quantity = $sectionData['quantity'];
            $price = $sectionData['price'];
            $seats_query = "INSERT INTO seats (concertid, type, price, quantityavail) 
                            VALUES ('$concertid', '$sectionName', '$price', '$quantity')";
            mysqli_query($conn, $seats_query);
        }
        
        if(isset($_FILES['coverposter']) && isset($_FILES['seatplan'])) {
            $coverposter = $_FILES['coverposter'];
            $seatplan = $_FILES['seatplan'];

            if (($coverposter['size'] > 40000000) || ($seatplan['size'] > 40000000)) {
                $error_message = "File size cannot exceed 40 MiB.";
            } else {
                $coverposter = addslashes(file_get_contents($coverposter['tmp_name']));
                $seatplan = addslashes(file_get_contents($seatplan['tmp_name']));

                $image_query = "INSERT INTO images (concertid, poster, seatplan) 
                                VALUES ('$concertid', '$coverposter', '$seatplan')";
                mysqli_query($conn, $image_query);
                
                $success_message = "Concert successfully added!";
            }
        } else {
            $error_message = "Error uploading images.";
        }
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Add Concert</title>
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

        .form-container button {
            background-color: #ff101d;
            font-family: 'Archivo Black', sans-serif;
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

        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .form-item {
            margin-bottom: 10px;
            display: flex; 
            align-items: center;
        }

        .form-item label {
            font-family: 'Archivo Black', sans-serif;
            margin-right: 10px;
            display: inline-block; 
            width: 200px;
        }

        .form-item input[type="text"],
        .form-item input[type="date"],
        .form-item input[type="time"],
        .form-item input[type="number"],
        .form-item select {
            width: calc(100% - 140px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .section-fields {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
        }

        .section-fields label {
            font-family: 'Archivo Black', sans-serif;
            margin-right: 10px;
            display: inline-block;
            width: 130px;
        }

        .section-fields input[type="text"],
        .section-fields input[type="number"] {
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .section-fields button {
            background-color: #ff101d;
            font-family: 'Archivo Black', sans-serif;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .section-fields button:hover {
            background-color: #d60c1e;
        }

        .submit-btn { 
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

        .section-container {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
        }

        .section-container label {
            font-family: 'Archivo Black', sans-serif;
            margin-right: 10px;
            margin-bottom: 10px;
            display: inline-block;
            width: 130px;
        }

        .section-container input[type="text"],
        .section-container input[type="number"] {
            width: 70%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .section-container button {
            background-color: #ff101d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .section-container button:hover {
            background-color: #d60c1e;
        }
        
        #addSection { 
            background-color: #ff101d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: inline-block;
            margin-top: 10px;
         
        }

        #addSection:hover {
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
        
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add Concert</h2>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
            <div class="form-item">
                <label for="concertname">Concert Name:</label>
                <input type="text" name="concertname" required>
            </div>
            <div class="form-item">
                <label for="artist">Artist:</label>
                <input type="text" name="artist" required>
            </div>
            <div class="form-item">
                <label for="showdate">Show Date:</label>
                <input type="date" name="showdate" required>
            </div>
            <div class="form-item">
                <label for="showtime">Show Time:</label>
                <input type="time" name="showtime" required>
            </div>
            <div class="form-item">
                <label for="ticketingdate">Ticketing Date:</label>
                <input type="date" name="ticketingdate" required>
            </div>
            <div class="form-item">
                <label for="ticketingtime">Ticketing Time:</label>
                <input type="time" name="ticketingtime" required>
            </div>
            <div class="form-item">
                <label for="venue">Venue:</label>
                <select name="venue" required>
                    <option value="" selected disabled>Select Venue</option>
                    <?php
                    $venue_query = mysqli_query($conn, "SELECT * FROM venuedetails");
                    while ($row = mysqli_fetch_assoc($venue_query)) {
                        echo "<option value='" . $row['venueid'] . "'>" . $row['venuename'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-item">
                <label for="coverposter">Cover Poster Image:</label>
                <input type="file" name="coverposter" accept="image/*" required>
            </div>
            <div class="form-item">
                <label for="seatplan">Seat Plan Image:</label>
                <input type="file" name="seatplan" accept="image/*" required>
            </div>
            <div class="section-fields">
                <div class="section-container">
                    <label>Section 1:</label><br>
                    <label for="sectionName1">Name:</label>
                    <input type="text" id="sectionName1" name="section[1][name]" required>
                    <label for="quantity1">Quantity:</label>
                    <input type="number" id="quantity1" name="section[1][quantity]" min="0" required>
                    <label for="price1">Price:</label>
                    <input type="number" id="price1" name="section[1][price]" min="0" required>
                    <button type="button" class="delete-section-btn" >Delete Section</button>
                </div>
            </div>
            <div class="error-message"><?php echo $error_message; ?></div>
            <div style="display: flex; justify-content: space-between;">
                <button type="button" id="addSection" style="margin-right: 10px;">Add Section</button>
                <button type="button" onclick="window.location.href='server.php'">Back to Menu</button>   
            </div>
            <input type="submit" class="submit-btn" style="font-family: 'Archivo Black', sans-serif;" value="Add Concert">
        </form>
    </div>
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p><?php echo $error_message; ?></p>
        </div>
    </div>

    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p><?php echo $success_message; ?></p>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#addSection").click(function(){
                var sectionCount = $(".section-container").length + 1;
                var sectionHTML = 
                    '<div class="section-container" style="margin-top: 10px;">' +
                        '<label>Section ' + sectionCount + ':</label><br>' +
                        '<label for="sectionName' + sectionCount + '">Name:</label>' +
                        '<input type="text" id="sectionName' + sectionCount + '" name="section[' + sectionCount + '][name]" required>' +
                        '<label for="quantity' + sectionCount + '">Quantity:</label>' +
                        '<input type="number" id="quantity' + sectionCount + '" name="section[' + sectionCount + '][quantity]" min="0" required>' +
                        '<label for="price' + sectionCount + '">Price:</label>' +
                        '<input type="number" id="price' + sectionCount + '" name="section[' + sectionCount + '][price]" min="0" required>' +
                        '<button type="button" class="delete-section-btn" onclick="deleteSection(' + sectionCount + ')">Delete Section</button>' +
                    '</div>';
                $(".section-fields").append(sectionHTML);
                 $(".form-container").scrollTop(0);
            });
            
        });

        function deleteSection(sectionId) {
            $(".section-container:nth-child(" + sectionId + ")").remove();
            $(".section-container").each(function(index) {
                $(this).find('label:first').text('Section ' + (index + 1) + ':');
                $(this).find('input[type="text"]').attr('id', 'sectionName' + (index + 1)).attr('name', 'section[' + (index + 1) + '][name]');
                $(this).find('input[type="number"]').eq(0).attr('id', 'quantity' + (index + 1)).attr('name', 'section[' + (index + 1) + '][quantity]');
                $(this).find('input[type="number"]').eq(1).attr('id', 'price' + (index + 1)).attr('name', 'section[' + (index + 1) + '][price]');
                $(this).find('button').attr('onclick', 'deleteSection(' + (index + 1) + ')');
            });
        }

        var modal = document.getElementById("successModal");
        var span = document.getElementsByClassName("close")[0];

        window.onload = function() {
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST") { ?>
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

