<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost:3308", "root", "", "concertdb");
if (!$conn) {
    die("Unable to connect to the database: " . mysqli_connect_error());
}

// Check if the form is submitted
if(isset($_POST['submit'])) {
    // Get form inputs
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Get current admin details
    $username = 'admin'; // Change this to the admin username
    $sql = "SELECT upassword FROM useraccount WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $adminPassword = $row['upassword'];
        
        // Validate current password
        if($currentPassword === $adminPassword) {
            // Validate new password
            if($newPassword === $confirmPassword) {
                // Update admin password in the database
                $update_sql = "UPDATE useraccount SET upassword = '$newPassword' WHERE username = '$username'";
                $update_result = mysqli_query($conn, $update_sql);
                
                if($update_result) {
                    $successMessage = "Password updated successfully.";
                } else {
                    $errorMessage = "Error updating password: " . mysqli_error($conn);
                }
            } else {
                $errorMessage = "New password and confirm password do not match.";
            }
        } else {
            $errorMessage = "Current password is incorrect.";
        }
    } else {
        $errorMessage = "Error retrieving admin details.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Admin Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #ebe1c6;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            font-family: 'Archivo Black', sans-serif;
            color: black;
            font-size: 30px;
            margin-bottom: 20px;
        }

        form {
            text-align: center;
        }

        label {
            font-family: 'Archivo Black', sans-serif;
            display: block;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        input[type="submit"] {
            font-family: 'Archivo Black', sans-serif;
            background-color: #ff101d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #e6001a;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .return-btn-container {
            text-align: center;
        }

        .return-btn {
            display: inline-block;
            width: 200px;
            text-align: center;
            font-family: 'Archivo Black', sans-serif;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            margin-top: 20px;
        }

        .return-btn:hover {
            background-color: #0056b3;
        }

        
    </style>
</head>
<body>
    <div class="container">
        <h2>Modify Admin Password</h2>
        <?php if(isset($successMessage)) : ?>
            <div class="message success"><?= $successMessage ?></div>
        <?php endif; ?>
        <?php if(isset($errorMessage)) : ?>
            <div class="message error"><?= $errorMessage ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="current_password">Current Password:</label><br>
            <input type="password" id="current_password" name="current_password" required><br>
            <label for="new_password">New Password:</label><br>
            <input type="password" id="new_password" name="new_password" required><br>
            <label for="confirm_password">Confirm New Password:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>
            <input type="submit" name="submit" value="Update Password">
        </form>
        <div class="return-btn-container">
            <a href="server.php" class="return-btn">Return to Menu</a>
        </div>
    </div>
</body>
</html>
