<?php
// Connect to the database
$conn = mysqli_connect("localhost:3308", "root", "", "concertdb");
if (!$conn) {
    die("Unable to connect to the database: " . mysqli_connect_error());
}

// Check if an announcement is selected for deletion
if(isset($_POST['delete_announcement'])) {
    $announcement_id = $_POST['delete_announcement'];
    // Delete the selected announcement
    $delete_sql = "DELETE FROM announcements WHERE annid = $announcement_id";
    $delete_result = mysqli_query($conn, $delete_sql);
    if($delete_result) {
        $success_message = "Announcement deleted successfully.";
    } else {
        $error_message = "Error deleting announcement: " . mysqli_error($conn);
    }
}

// Fetch announcements from the database
$sql = "SELECT * FROM announcements";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error retrieving data: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
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

        .announcement-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 60%;
        }

        h2 {
            text-align: center;
            font-family: 'Archivo Black', sans-serif;
            color: black;
            font-size: 30px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .delete-btn {
            background-color: #ff101d;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .delete-btn:hover {
            background-color: #e00b1a;
        }

        .message {
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

        .return-btn {
            margin-top: 20px;
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

        .return-container {
            text-align: center;
            margin-top: 10px;
        }

    </style>
</head>
<body>
    <div class="announcement-container">
        <h2>Announcements</h2>
        <table>
            <tr>
                <th>Message</th>
                <th>Action</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?= $row['message'] ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="delete_announcement" value="<?= $row['annid'] ?>">
                            <button type="submit" class="delete-btn">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <?php if(isset($success_message)) : ?>
            <div class="message success"><?= $success_message ?></div>
        <?php endif; ?>
        <?php if(isset($error_message)) : ?>
            <div class="message error"><?= $error_message ?></div>
        <?php endif; ?>
        <div class="return-container">
            <a href="server.php" class="return-btn">Return to Menu</a>
        </div>

    </div>
</body>
</html>
