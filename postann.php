<?php
// Check if the form is submitted
if(isset($_POST['announcement'])) {
    // Get the announcement message from the form
    $announcement = $_POST['announcement'];
    
    // Connect to the database
    $conn = mysqli_connect("localhost:3308", "root", "", "concertdb");
    if (!$conn) {
        die("Unable to connect to the database: " . mysqli_connect_error());
    }
    
    // Prepare the SQL statement to insert the announcement into the database
    $insert_sql = "INSERT INTO announcements (message) VALUES ('$announcement')";
    
    // Execute the SQL statement
    $insert_result = mysqli_query($conn, $insert_sql);
    
    // Check if the insertion was successful
    if($insert_result) {
        $success_message = "Announcement posted successfully.";
    } else {
        $error_message = "Error posting announcement: " . mysqli_error($conn);
    }
    
    // Close the database connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Announcement</title>
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

        .announcement-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 40%;
            text-align: center;
        }

        h2 {
            text-align: center;
            font-family: 'Archivo Black', sans-serif;
            color: black;
            font-size: 30px;
            margin-bottom: 20px;
        }

        label {
            font-family: 'Archivo Black', sans-serif;
            display: block;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 20px;
            resize: none;
            padding: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .post-btn {
            font-family: 'Archivo Black', sans-serif;
            background-color: #ff101d;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .post-btn:hover {
            opacity: 0.8;
        }

        .message {
            margin-top: 10px;
            margin-bottom: 20px;
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

        .return-btn {
            margin-top: 10px;
            font-family: 'Archivo Black', sans-serif;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .return-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="announcement-form">
        <h2>Post Announcement</h2>
        <form method="post">
            <label for="announcement">Announcement:</label>
            <textarea name="announcement" id="announcement" required></textarea>
            <br>
            <?php if(isset($success_message)) : ?>
                <div class="message success"><?= $success_message ?></div>
            <?php endif; ?>
            <?php if(isset($error_message)) : ?>
                <div class="message error"><?= $error_message ?></div>
            <?php endif; ?>
            <input type="submit" class="post-btn" value="Post Announcement">
        </form>
        <a href="server.php" class="return-btn">Return to Menu</a>
    </div>
</body>
</html>
