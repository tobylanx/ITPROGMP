<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_username'])) {
    $conn = mysqli_connect("localhost:3308", "root", "") or die("Unable to connect!" . mysqli_error());
    mysqli_select_db($conn, "concertdb");

    $username = $_POST['username'];

    $username = mysqli_real_escape_string($conn, $username);

    $sql = "SELECT * FROM useraccount WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION["userid"] = $row["userid"];
        $_SESSION["username"] = $row["username"];
        $_SESSION["securityid"] = $row["securityid"];
        header("Location: security_question.php");
        exit;
    } else {
        $error_message = "Invalid username";
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
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h2 {
            font-family: 'Archivo Black', sans-serif;
            font-size: 32px;
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-top: 20px;
            font-size: 18px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #ff101d;
            color: #fff;
            border: none;
            padding: 12px 0;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #ff5e67;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Forgot Password</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <div class="error-message"><?php echo $error_message; ?></div>
            <input type="submit" name="submit_username" value="Submit">
        </form>
    </div>
</body>
</html>
