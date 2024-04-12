<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

session_start();
$error_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        $conn = mysqli_connect("localhost:3308", "root", "") or die("Unable to connect!" . mysqli_error());
        mysqli_select_db($conn, "concertdb");

        $login_username = $_POST['loginUsername'];
        $login_password = $_POST['loginPass'];

        $login_username = mysqli_real_escape_string($conn, $login_username);
        $login_password = mysqli_real_escape_string($conn, $login_password);

        $sql = "SELECT * FROM useraccount WHERE username = '$login_username' AND upassword = '$login_password'";
        $result = mysqli_query($conn, $sql);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $_SESSION["userid"] = $row["userid"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["firstname"] = $row["firstname"];
            $_SESSION["lastname"] = $row["lastname"];
            $_SESSION["email"] = $row["email"];
            $_SESSION["password"] = $row["upassword"];
            $_SESSION["secquestionid"] = $row["securityid"];
            $_SESSION["secanswer"] = $row["sanswer"];
            header("Location: main.php");
            exit;
        } else {
            $error_message = "Invalid username or password";
        }
    } elseif (isset($_POST['admin_login'])) {
        // Admin login logic here
        $admin_password = $_POST['adminPass'];

        $conn = mysqli_connect("localhost:3308", "root", "") or die("Unable to connect!" . mysqli_error());
        mysqli_select_db($conn, "concertdb");

        $sql = "SELECT * FROM useraccount WHERE username = 'admin'";
        $result = mysqli_query($conn, $sql);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if ($admin_password === $row['upassword']) {
                // Password matches, redirect to server.php
                header("Location: server.php");
                exit;
            } else {
                $error_message = "Invalid password";
            }
        } else {
            $error_message = "Admin account not found";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
            font-family: 'Archivo Black', sans-serif;
            display: block;
            margin-top: 10px;
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
            margin-bottom: 10px;
        }

        .button-container input[type="submit"] {
            flex: 1;
            margin: 0 5px;
        }

        .login-btn {
            font-family: 'Archivo Black', sans-serif;
            background-color: #ff101d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button-container input[type="submit"]:hover {
            opacity: 0.8;
        }

        .signup {
            text-align: center;
            margin-top: 10px;
        }

        .forgotpass {
            text-align: center;
            margin-top: 10px;
        }

        .admin-login {
            text-align: center;
            margin-top: 10px;
        }

        .error-message {
            color: red;
            font-size: 14px;
            text-align: center;
        }

        h2{
            font-family: 'Archivo Black', sans-serif;
            color: black;
            font-size: 50px;
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
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 30%;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
    <img style="padding-right:70px" src="logo/Logo.png">
    <div class="form-container">
        <h2>LOGIN</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <label for="loginUsername">USERNAME:</label>
            <input type="text" name="loginUsername" required><br>
            <label for="loginPass">PASSWORD:</label>
            <input type="password" name="loginPass" required><br>
            <div class="button-container">
                <input style="max-width= 1000px;" type="submit" name="login" value="LOGIN" class="login-btn">
            </div>
        </form>
        <div class="error-message"><?php echo $error_message; ?></div>
        <div class="signup">
            <p style="font-size: 15px; font-family: Roboto;"> Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
        <div class="forgotpass">
            <p style="font-size: 15px; font-family: Roboto;"> Forgot Your Password? <a href="forgotpass.php">Forgot Password</a></p>
        </div>
        <div class="admin-login">
            <p style="font-size: 15px; font-family: Roboto; text-align: center;"> <a href="#" onclick="openAdminModal()">Login as Administrator</a></p>
        </div>
    </div>

    <div id="adminModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAdminModal()">&times;</span>
            <h2>Admin Login</h2>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <label for="adminPass">PASSWORD:</label>
                <input type="password" name="adminPass" required><br>
                <input type="submit" name="admin_login" value="LOGIN" class="login-btn">
            </form>
        </div>
    </div>

    <script>
        function openAdminModal() {
            document.getElementById('adminModal').style.display = 'block';
        }

        function closeAdminModal() {
            document.getElementById('adminModal').style.display = 'none';
        }
    </script>
</body>
</html>
