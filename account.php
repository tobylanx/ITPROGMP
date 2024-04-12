<?php
    error_reporting(E_ALL); 
    ini_set('display_errors', 1);
    
    session_start();
    $conn = mysqli_connect("localhost:3308", "root", "", "concertdb");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Redirect to login page if user is not logged in
    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit;
    }

    $userId = $_SESSION["userid"];

    // Fetch user's account details
    $userQuery = "SELECT u.*, sq.squestion 
                  FROM useraccount u
                  INNER JOIN securityques sq ON u.securityid = sq.securityid
                  WHERE u.userid = $userId";
    $userResult = mysqli_query($conn, $userQuery);
    $userData = mysqli_fetch_assoc($userResult);

    // Check if form is submitted successfully and update profile
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
        // Update user profile
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];

        $updateQuery = "UPDATE useraccount 
                        SET firstname = '$firstname', lastname = '$lastname', email = '$email'
                        WHERE userid = $userId";
        mysqli_query($conn, $updateQuery);

        // Set success message
        $updateMessage = "Profile updated successfully!";
    } elseif (isset($_POST['change_password'])) {
            // Change password
            $newPassword = $_POST['new_password'];
            $securityAnswer = $_POST['security_answer'];

            // Verify security answer
            $verifyQuery = "SELECT * FROM useraccount WHERE userid = $userId AND sanswer = '$securityAnswer'";
            $verifyResult = mysqli_query($conn, $verifyQuery);
            $count = mysqli_num_rows($verifyResult);

            if ($count == 1) {
                // Update password
                $updatePasswordQuery = "UPDATE useraccount SET upassword = '$newPassword' WHERE userid = $userId";
                mysqli_query($conn, $updatePasswordQuery);
                $passwordMessage = "Password updated successfully!";
            } else {
                $passwordError = "Security answer is incorrect!";
            }
        }

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        header {
            background-color: #ff101d;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 32px;
            font-weight: bold;
        }

        header a {
            color: #fff;
            text-decoration: none;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-family: 'Archivo Black', sans-serif;
            text-align: left;
            margin-top: 0;
            font-size: 30px;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #ff101d;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #ff5e67;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }

        .success-message {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>My Account</h1>
        <a href="main.php">Back to Main</a>
    </header>
    <div class="container">
        <h2>Update Profile</h2>
        <form action="" method="post">
            <label for="firstname">First Name</label>
            <input type="text" id="firstname" name="firstname" value="<?php echo isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : htmlspecialchars($userData['firstname']); ?>" required>

            <label for="lastname">Last Name</label>
            <input type="text" id="lastname" name="lastname" value="<?php echo isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : htmlspecialchars($userData['lastname']); ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($userData['email']); ?>" required>

            <input type="submit" name="update_profile" value="Update Profile">
            
            </form>
    <?php if(isset($updateMessage)): ?>
        <p class="success-message"><?php echo $updateMessage; ?></p>
    <?php endif; ?>

    </div>
    <div class="container">
        <h2>Change Password</h2>
        <form action="" method="post">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            <label for="security_question">Security Question:</label>
            <p><?php echo $userData['squestion']; ?></p>
            <label for="security_answer">Answer to Security Question:</label>
            <input type="text" id="security_answer" name="security_answer" required>
            <?php if(isset($passwordError)): ?>
                <p class="error-message"><?php echo $passwordError; ?></p>
            <?php endif; ?>
            <?php if(isset($passwordMessage)): ?>
                <p class="success-message"><?php echo $passwordMessage; ?></p>
            <?php endif; ?>
            <input type="submit" name="change_password" value="Change Password">
        </form>
    </div>
</body>
</html>
