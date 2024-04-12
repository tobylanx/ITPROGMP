<?php
session_start();

if (!isset($_SESSION["userid"]) || !isset($_SESSION["securityid"])) {
    header("Location: forgotpass.php");
    exit;
}

$conn = mysqli_connect("localhost:3308", "root", "") or die("Unable to connect!" . mysqli_error());
mysqli_select_db($conn, "concertdb");

$userid = $_SESSION["userid"];
$securityid = $_SESSION["securityid"];

$sql = "SELECT * FROM securityques WHERE securityid = $securityid";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$security_question = $row['squestion'];

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_answer'])) {
    $sanswer = $_POST['sanswer'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $sql = "SELECT * FROM useraccount WHERE userid = $userid AND sanswer = '$sanswer'";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows == 1) {
        // Valid answer, check if new password and confirm password match
        if ($new_password === $confirm_password) {
            // Update password
            $update_sql = "UPDATE useraccount SET upassword = '$confirm_password' WHERE userid = $userid";
            if (mysqli_query($conn, $update_sql)) {
                // Password updated successfully
                $success_message = "Password updated successfully!";
            } else {
                $error_message = "Failed to update password";
            }
        } else {
            $error_message = "New password and confirm password do not match";
        }
    } else {
        $error_message = "Invalid answer";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            font-family: 'Roboto', sans-serif;
            font-size: 16px;
            background-color: #ebe1c6;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .form-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 90%;
        }

        h2 {
            font-family: 'Archivo Black', sans-serif;
            color: #ff101d;
            font-size: 32px;
            margin-top: 0;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 20px;
            font-size: 20px;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .error-message {
            color: #ff101d;
            margin-bottom: 20px;
            text-align: center;
        }

        .success-message {
            color: #32CD32;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .button-container input[type="submit"],
        .button-container a {
            background-color: #ff101d;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-family: 'Archivo Black', sans-serif;
            font-size: 18px;
            transition: background-color 0.3s;
            max-width: 45%;
            text-align: center;
            display: inline-block;
        }

        .button-container input[type="submit"]:hover,
        .button-container a:hover {
            background-color: #ff5e67;
        }

        .button-container a {
            text-align: center;
        }
        /* Add any additional styles here for password strength feedback */
        .password-strength {
            font-size: 14px;
            margin-top: 5px;
        }

        .weak {
            color: red;
        }

        .strong {
            color: green;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Security Question</h2>
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
            <meta http-equiv="refresh" content="2;url=login.php" />
        <?php endif; ?>
        <form id="passwordForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" onsubmit="return validateForm()">
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <label for="sanswer">Your Answer:</label>
            <input type="text" name="sanswer" required><br>
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required><br>
            <div id="passwordStrength" class="password-strength"></div>
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" required><br>
            <div id="passwordStrengthError" class="error-message"></div>
            <div class="button-container">
                <input type="submit" name="submit_answer" value="Submit">
                <a href="login.php">Back to Login</a>
            </div>
        </form>
    </div>

    <script>
        function validateForm() {
            var password = document.getElementById("new_password").value;
            var passwordStrength = document.getElementById("passwordStrength");
            var passwordStrengthError = document.getElementById("passwordStrengthError");

            // Password length between 8 to 25 characters
            if (password.length < 8 || password.length > 25) {
                passwordStrengthError.textContent = "Password must be between 8 to 25 characters.";
                return false;
            } else {
                passwordStrengthError.textContent = "";
            }

            // Password contains at least one capital letter
            var capitalRegex = /[A-Z]/;
            if (!capitalRegex.test(password)) {
                passwordStrengthError.textContent = "Password must contain at least one capital letter.";
                return false;
            } else {
                passwordStrengthError.textContent = "";
            }

            // Password contains at least one special character
            var specialCharRegex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
            if (!specialCharRegex.test(password)) {
                passwordStrengthError.textContent = "Password must contain at least one special character.";
                return false;
            } else {
                passwordStrengthError.textContent = "";
            }

            // Password strength indicator (optional)
            if (password.length < 12) {
                passwordStrength.textContent = "Password Strength: Weak";
                passwordStrength.className = "password-strength weak";
            } else {
                passwordStrength.textContent = "Password Strength: Strong";
                passwordStrength.className = "password-strength strong";
            }

            return true;
        }
    </script>
</body>

</html>