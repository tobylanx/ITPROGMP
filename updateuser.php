<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

$conn = mysqli_connect("localhost:3308", "root", "") or die("Unable to connect!" . mysqli_error());
mysqli_select_db($conn, "concertdb");

$user_query = "SELECT userid, username FROM useraccount";
$user_result = mysqli_query($conn, $user_query);

$security_query = "SELECT securityid, squestion FROM securityques";
$security_result = mysqli_query($conn, $security_query);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['viewuser'])) {
    $viewuser = $_POST['viewuser'];

    $userdetails = "SELECT * FROM useraccount WHERE userid = '$viewuser'";
    $userresult = mysqli_query($conn, $userdetails);
    $viewdetails = mysqli_fetch_assoc($userresult);
}

// Update user details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $userid = $_POST['userid'];
    $username = $_POST['username'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $securityid = $_POST['securityid'];
    $sanswer = $_POST['sanswer'];

    // Validation
    $valid = true;
    $error_message = "";

    // Check username length
    if (strlen($username) < 8 || strlen($username) > 25) {
        $valid = false;
        $error_message .= "Username should be between 8 and 25 characters.<br>";
    }

    // Check username uniqueness
    $checkun = "SELECT * FROM useraccount WHERE username = '$username' AND userid != '$userid'";
    $resultun = mysqli_query($conn, $checkun);
    if (mysqli_num_rows($resultun) > 0) {
        $valid = false;
        $error_message .= "Username is already taken. <br>";
    }

    // Check email uniqueness
    $checkemail = "SELECT * FROM useraccount WHERE email = '$email' AND userid != '$userid'";
    $resultemail = mysqli_query($conn, $checkemail);
    if (mysqli_num_rows($resultemail) > 0) {
        $valid = false;
        $error_message .= "Email is already registered. <br>";
    }

    // Check password length
    if (strlen($password) < 8 || strlen($password) > 25) {
        $valid = false;
        $error_message .= "Password must be between 8 and 25 characters.<br>";
    }

    // Check security answer length
    if (strlen($sanswer) < 3 || strlen($sanswer) > 100) {
        $valid = false;
        $error_message .= "Security answer should be between 3 and 100 characters.<br>";
    }

    // Check if security question ID exists
    $check_security_id = "SELECT * FROM securityques WHERE securityid = '$securityid'";
    $result_security_id = mysqli_query($conn, $check_security_id);
    if (mysqli_num_rows($result_security_id) == 0) {
        $valid = false;
        $error_message .= "Invalid security question ID.<br>";
    }

    // If all validations pass, update user details
    if ($valid) {
        $update_query = "UPDATE useraccount SET username = '$username', firstname = '$firstname', lastname = '$lastname', email = '$email', upassword = '$password', securityid = '$securityid', sanswer = '$sanswer' WHERE userid = '$userid'";
        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('User details updated successfully');</script>";
        } else {
            echo "Error updating user details: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('$error_message');</script>";
    }
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

        select, input[type="text"], input[type="email"], input[type="password"] {
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
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            background-color: #ff101d;
            color: #fff;
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
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            background-color: #007bff;
            color: #fff;
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
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="userid" value="<?php echo $viewdetails['userid']; ?>">
                    <div class="form-item">
                        <label for="username">Username:</label>
                        <input type="text" name="username" value="<?php echo $viewdetails['username']; ?>" required>
                    </div>
                    <div class="form-item">
                        <label for="firstname">First Name:</label>
                        <input type="text" name="firstname" value="<?php echo $viewdetails['firstname']; ?>" required>
                    </div>
                    <div class="form-item">
                        <label for="lastname">Last Name:</label>
                        <input type="text" name="lastname" value="<?php echo $viewdetails['lastname']; ?>" required>
                    </div>
                    <div class="form-item">
                        <label for="email">Email:</label>
                        <input type="email" name="email" value="<?php echo $viewdetails['email']; ?>" required>
                    </div>
                    <div class="form-item">
                        <label for="password">Password:</label>
                        <input type="password" name="password" value="<?php echo $viewdetails['upassword']; ?>" required>
                    </div>
                    <div class="form-item">
                        <label for="securityid">Security Question ID:</label>
                        <select name="securityid" required>
                            <?php
                            $sql = "SELECT * FROM securityques";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row["securityid"] . "'>" . $row["squestion"] . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-item">
                        <label for="sanswer">Security Answer:</label>
                        <input type="text" name="sanswer" value="<?php echo $viewdetails['sanswer']; ?>" required>
                    </div>
                    <button type="submit" name="update_user">Update User Details</button>
                </form>
            </div>
        <?php endif; ?>
        
        <button class="back-btn" onclick="window.location.href = 'server.php';">Back to Menu</button>
    </div>
</body>
</html>
