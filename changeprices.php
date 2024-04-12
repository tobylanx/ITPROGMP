<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect("localhost:3308", "root", "") or die("Unable to connect!" . mysqli_error($conn));
mysqli_select_db($conn, "concertdb");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['prices'])) {
    $prices = $_POST['prices'];
    foreach ($prices as $seatid => $price) {
        $price = mysqli_real_escape_string($conn, $price);
        $sql = "UPDATE seats SET price = '$price' WHERE seatid = '$seatid'";
        mysqli_query($conn, $sql);
    }
    echo "<script>document.getElementById('successModal').style.display = 'block';</script>";
}

$concertid = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selectconcert'])) {
    $concertid = mysqli_real_escape_string($conn, $_POST['selectconcert']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Section Prices</title>
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

        .section-item {
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            display: flex;
            align-items: center;
        }

        .section-item span {
            font-weight: bold;
            margin-right: 10px;
            flex: 1;
        }

        .section-item input[type="number"] {
            flex: 2;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
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
    <h2>Change Section Prices</h2>
    <p>Select the concert you wish to change the section prices in Philippine Peso.</p>
    <form id="changeForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-item" style="margin-bottom: 20px;">
            <select name="selectconcert" id="selectconcert" onchange="this.form.submit()" required>
                <option value="" selected disabled>Select Concert</option>
                <?php
                $sql = "SELECT * FROM concertdetails c WHERE status = 'O'";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $selected = ($concertid == $row['concertid']) ? 'selected' : '';
                        echo "<option value='" . $row['concertid'] . "' $selected>" . "[" . $row['concertid'] . "] " . $row['concertname'] . " by " . $row['artist'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>
    </form>

    <?php if (!empty($concertid)) { ?>
        <form id="priceForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div id="section_prices">
                <?php
                $section_sql = "SELECT * FROM seats WHERE concertid = '$concertid'";
                $section_result = mysqli_query($conn, $section_sql);
                if (mysqli_num_rows($section_result) > 0) {
                    while ($row = mysqli_fetch_assoc($section_result)) {
                        echo "<div class='section-item'>";
                        echo "<span>" . $row['type'] . ": </span>";
                        echo "<input type='number' name='prices[" . $row['seatid'] . "]' value='" . $row['price'] . "' required>";
                        echo "</div>";
                    }
                }
                ?>
            </div>
        </form>
    <?php } ?>
    <div style="display: flex; justify-content: space-between;">
        <button class="submit-btn" type="submit" form="priceForm" style="margin-right: 10px; font-family: 'Archivo Black', sans-serif;">Change Prices</button>
        <button type="button" class="back-btn" onclick="window.location.href='server.php';" style="font-family: 'Archivo Black', sans-serif;">Back to Menu</button>
    </div>

</div>

<div id="successModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>Section Prices successfully changed!</p>
    </div>
</div>

<script>
    var modal = document.getElementById("successModal");
    var span = document.getElementsByClassName("close")[0];

    window.onload = function() {
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['prices'])) { ?>
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
