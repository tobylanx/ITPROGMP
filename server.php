<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ebe1c6;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .container h1 {
            text-align: center;
            color: #333;
        }

        .menu-group {
            margin-bottom: 30px;
        }

        .menu-group h2 {
            color: black;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .menu-item {
            background-color: red;
            border: 2px solid #ff101d;
            color: #ff101d;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .menu-item a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .menu-item {
                font-size: 16px;
            }
        }

        .return-btn-container {
            text-align: center;
            margin-top: 50px;
        }

        .return-btn {
            font-family: 'Roboto', sans-serif;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
        }

        .return-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Menu</h1>

        <div class="menu-group">
            <h2>Modify Concerts</h2>
            <div class="menu">
                <div class="menu-item">
                    <a href="addconcert.php">Add New Concerts</a>
                </div>
                <div class="menu-item">
                    <a href="cancelconcert.php">Cancel Existing Concerts</a>
                </div>
                <div class="menu-item">
                    <a href="changevenue.php">Change Venue</a>
                </div>
                <div class="menu-item">
                    <a href="changeprices.php">Change Section Prices</a>
                </div>
                <div class="menu-item">
                    <a href="changeticketing.php">Change Ticketing Date</a>
                </div>
            </div>
        </div>

        <div class="menu-group">
            <h2>User Account Module</h2>
            <div class="menu">
                <div class="menu-item">
                    <a href="viewuser.php">View User Information</a>
                </div>
                <div class="menu-item">
                    <a href="updateuser.php">Update User Details</a>
                </div>
                <div class="menu-item">
                    <a href="deleteuser.php">Delete User</a>
                </div>
                <div class="menu-item">
                    <a href="adminpw.php">Modify Admin Password</a>
                </div>
            </div>
        </div>

        <div class="menu-group">
            <h2>Upload Announcement</h2>
            <div class="menu">
                <div class="menu-item">
                    <a href="postann.php">Post Announcements</a>
                </div>
                <div class="menu-item">
                    <a href="deleteann.php">Delete Announcements</a>
                </div>
            </div>
        </div>

        <div class="menu-group">
            <h2>Generate Reports</h2>
            <div class="menu">
                <div class="menu-item">
                    <a href="generatesales.php">Generate Sales</a>
                </div>
                <div class="menu-item">
                    <a href="concertstat.php">Check Status</a>
                </div>
            </div>
        </div>

        <div class="return-btn-container">
            <a href="login.php" class="return-btn">Return to Login</a>
        </div>
    </div>
</body>
</html>


