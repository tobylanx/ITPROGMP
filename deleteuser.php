<?php
error_reporting(E_ALL); 
ini_set('display_errors', 1);

// Database connection
$conn = mysqli_connect("localhost:3308", "root", "") or die("Unable to connect!" . mysqli_error());
mysqli_select_db($conn, "concertdb");

// Fetch user accounts
$user_query = "SELECT userid, username FROM useraccount";
$user_result = mysqli_query($conn, $user_query);

// Delete user if confirmed
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $userid_to_delete = $_POST['userid_to_delete'];

    // Perform deletion
    $delete_query = "DELETE FROM useraccount WHERE userid = '$userid_to_delete'";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('User deleted successfully');</script>";
    } else {
        echo "Error deleting user: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete User Accounts</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* CSS styles */
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
        <h2>Delete User Accounts</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-item">
                <label for="userid_to_delete">Select User:</label>
                <select name="userid_to_delete" required>
                    <option value="" selected disabled>Select User</option>
                    <?php
                    while ($row = mysqli_fetch_assoc($user_result)) {
                        echo "<option value='" . $row['userid'] . "'>" . $row['userid'] . " - " . $row['username'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="delete_user" onclick="return confirm('Are you sure you want to delete this user?')">Delete User</button>
        </form>
        
        <button class="back-btn" onclick="window.location.href = 'server.php';">Back to Menu</button>
    </div>
</body>
</html>
