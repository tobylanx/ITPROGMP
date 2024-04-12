<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

session_start();
$conn = mysqli_connect("localhost:3308", "root", "") or die("Unable to connect!" . mysqli_error());
mysqli_select_db($conn, "concertdb");

$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $firstname = ($_POST['firstname']);
    $lastname = ($_POST['lastname']);
    $email = $_POST['email'];
    $password = $_POST['password'];
    $secquestionid = $_POST['secq'];
    $secanswer = ($_POST['secanswer']);

    $valid = true;
    $error_message = "";

    if (strlen($username) < 8 || strlen($username) > 25) {
        $valid = false;
        $error_message .= "Username should be atleast 8 characters.<br>";
    }

    $checkun = "SELECT * FROM useraccount WHERE username = '$username'";
    $resultun = $conn->query($checkun);
    if ($resultun->num_rows > 0) {
        $valid = false;
        $error_message .= "Username is already taken. <br>";
    }

    $checkemail = "SELECT * FROM useraccount WHERE email = '$email'";
    $resultemail = $conn->query($checkemail);
    if ($resultemail->num_rows > 0) {
        $valid = false;
        $error_message .= "Email is already registered. <br>";
    }

    $checkpass = "SELECT * FROM useraccount WHERE upassword = '$password'";
    $resultpass = $conn->query($checkpass);
    if ($resultpass->num_rows > 0) {
        $valid = false;
        $error_message .= "Password is already used. Please choose a different one. <br>";
    }

    if (!preg_match('/[@]/', $email) || strlen($email) < 8) {
        $valid = false;
        $error_message .= "Make sure email is valid. <br>";
    }

    if (strlen($password) < 8 || strlen($password) > 25) {
        $valid = false;
        $error_message .= "Password must be between 8 and 25 characters.<br>";
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $valid = false;
        $error_message .= "Password must contain at least one capital letter.<br>";
    }

    if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $password)) {
        $valid = false;
        $error_message .= "Password must contain at least one special character.";
    }

    if (!$valid) {
        $error_message = '<p class="error-message">' . $error_message . '</p>';
    } else {
        $sql = "INSERT INTO useraccount (username, firstname, lastname, email, upassword, securityid, sanswer)
                VALUES ('$username', '$firstname', '$lastname', '$email', '$password', '$secquestionid', '$secanswer')";

        if ($conn->query($sql) === TRUE) {
            $success_message = '<p class="success-message">Account created successfully. Please <a href="login.php">login</a>.</p>';
        } else {
            $error_message = '<p class="error-message">Error: ' . $conn->error . '</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<head>
    <style>
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

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 30%;
            margin-bottom: 20px;
        }

        .form-container h2 {
            text-align: center;
        }

        .form-container form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
        }

        .button-container input[type="submit"] {
            flex: 1;
            margin: 0 5px;
        }

        .signup-btn {
            background-color: #ff101d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .signup-btn:hover {
            opacity: 0.8;
        }

        .login {
            text-align: center;
            margin-top: 10px;
        }
        .password-condition {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }

        .success-message {
            color: green;
            font-size: 15px;
            margin-top: 5px;
            text-align: center;
        }

        h2{
            font-family: 'Archivo Black', sans-serif;
            color: black;
            font-size: 50px;
        }

    </style>
</head>
<body>
<img style="padding-right:70px" src="logo/Logo.png">
<div class="form-container">
    <h2>SIGN UP</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>
        <label for="firstname">First Name:</label>
        <input type="text" name="firstname" required><br>
        <label for="lastname">Last Name:</label>
        <input type="text" name="lastname" required><br>
        <label for="email">Email:</label>
        <input type="text" name="email" required><br>
        <label for="password">Password:</label>
        <input type="password" name="password" required><br>
        <label for="secq">Security Question:</label>
        <select name="secq" required>
            <option value="" disabled selected>Select a security question</option>
            <?php
            $sql = "SELECT * FROM securityques";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row["securityid"] . "'>" . $row["squestion"] . "</option>";
                }
            }
            ?>
        </select><br>
        <label for="secanswer">Answer:</label>
        <input type="text" name="secanswer" required><br>

        <div class="button-container">
            <input style="font-family: 'Archivo Black', sans-serif;" type="submit" name="signup" value="SIGN UP" class="signup-btn">
        </div>
    </form>
    <?php echo $success_message; ?>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
        if (!$valid) {
            echo $error_message;
        }
    }
    ?>

    <div class="login">
        <p style="font-size: 15px;">Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>

</body>
</html>
