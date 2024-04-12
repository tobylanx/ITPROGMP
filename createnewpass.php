<?php
session_start();
$conn = mysqli_connect("localhost:3308", "root", "") or die("Unable to connect!" . mysqli_error());
mysqli_select_db($conn, "concertdb");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $valid = true;
    $error_message = "";

    if (strlen($new_password) < 8 || strlen($new_password) > 25) {
        $valid = false;
        $error_message .= "Password must be between 8 and 25 characters.<br>";
    }

    if (!preg_match('/[A-Z]/', $new_password)) {
        $valid = false;
        $error_message .= "Password must contain at least one capital letter.<br>";
    }

    if (!preg_match('/[\'^£$%&*()}{@#~?!><>,|=_+¬-]/', $new_password)) {
        $valid = false;
        $error_message .= "Password must contain at least one special character.<br>";
    }

    if ($new_password !== $confirm_password) {
        $valid = false;
        $error_message .= "Passwords do not match.<br>";
    }

    if ($valid) {
        $username = $_SESSION['username'];
        
        $sql_check_password = "SELECT upassword FROM useraccount WHERE username = '$username'";
        $result_check_password = $conn->query($sql_check_password);
        $row_check_password = $result_check_password->fetch_assoc();
        $old_password = $row_check_password['upassword'];

        if ($new_password === $old_password) {
            $error_message = "New password cannot be the same as the old password.<br>";
        } else {
            $sql = "UPDATE useraccount SET upassword = '$new_password' WHERE username = '$username'";
            if ($conn->query($sql) === TRUE) {
                header("Location: main.php");
                exit;
            } else {
                $error_message = "Error updating password: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<head>
    <title>Create New Password</title>
    <style>
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 30%;
            margin: 50px auto;
        }

        html, body {
            height: 100%; /* Make sure the html and body cover the full height of the page */
            margin: 0; /* Remove default margin */
            font-family: futura, serif;
            font-size: 24px;
            background-color: #ebe1c6;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: futura, serif;
            font-size: 24px;
            margin-top: 50px;
            margin-bottom: 50px;
        }
        h2{
            font-family: 'Archivo Black', sans-serif;
            color: black;
            font-size: 35px;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 10px;
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
        }


        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .button-container {
            text-align: center;
        }

        .error-message {
            color: red;
            text-align: center;
        }

        .back-btn {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            margin-top: 10px;
            display: inline-block;
        }
        .button-container input[type="submit"], .button-container a {
            background-color: #ff101d; /* This is the button background color from signup.php */
            color: #fff; /* Text color */
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none; /* Remove underline from anchor tag */
            display: inline-block; /* Align inline with text */
            font-family: 'Archivo Black', sans-serif; /* Font style from signup.php */
            font-size: 16px; /* Adjust as per your design */
            transition: background-color 0.3s;
            margin-right: 10px; /* Spacing between buttons */
            max-width: 200px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>CREATE NEW PASSWORD</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" required><br>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" required><br>
            <div class="button-container">
                <input type="submit" name="submit" value="Submit">
                <a href="login.php" class="back-btn">Back to Login</a>
            </div>
        </form>
        <?php if(isset($error_message)) { ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php } ?>
    </div>
</body>
</html>
