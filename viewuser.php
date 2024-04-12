<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

$conn = mysqli_connect("localhost:3308", "root", "") or die("Unable to connect!" . mysqli_error());
mysqli_select_db($conn, "concertdb");

$user_query = "SELECT userid, username FROM useraccount";
$user_result = mysqli_query($conn, $user_query);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['viewuser'])) {
    $viewuser = $_POST['viewuser'];

    $userdetails = "SELECT * FROM useraccount WHERE userid = '$viewuser'";
    $userresult = mysqli_query($conn, $userdetails);
    $viewdetails = mysqli_fetch_assoc($userresult);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View User Accounts</title>
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

        h2 {
            font-family: 'Archivo Black', sans-serif;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        .form-item {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        select, button {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background: url('data:image/svg+xml;utf8,<svg fill="%23444" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>') no-repeat right 10px center/15px auto;
        }

        button {
            background-color: #ff101d;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #d60c1e;
        }

        .user-details {
            margin-top: 20px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .user-details h3 {
            font-family: 'Archivo Black', sans-serif;
            margin-bottom: 10px;
        }

        .user-details p {
            margin-bottom: 5px;
        }

        .back-btn {
            background-color: #007bff;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>View User Accounts</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-item">
                <label for="viewuser">Select User:</label>
                <select name="viewuser" required>
                    <option value="" selected disabled>Select User</option>
                    <?php
                    while ($row = mysqli_fetch_assoc($user_result)) {
                        echo "<option value='" . $row['userid'] . "'>" . $row['userid'] . " - " . $row['username'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit">View User Details</button>
        </form>

        <?php if (isset($viewdetails)) : ?>
            <div class="user-details">
                <h3>User Details</h3>
                <p>User ID: <?php echo $viewdetails['userid']; ?></p>
                <p>Username: <?php echo $viewdetails['username']; ?></p>
                <p>First Name: <?php echo $viewdetails['firstname']; ?></p>
                <p>Last Name: <?php echo $viewdetails['lastname']; ?></p>
                <p>Email: <?php echo $viewdetails['email']; ?></p>
                <p>Password: <?php echo $viewdetails['upassword']; ?></p>
                <p>Security Question ID: <?php echo $viewdetails['securityid']; ?></p>
                <p>Security Answer: <?php echo $viewdetails['sanswer']; ?></p>
            </div>
        <?php endif; ?>
        
        <button class="back-btn" onclick="window.location.href = 'server.php';">Back to Menu</button>
    </div>
</body>
</html>
