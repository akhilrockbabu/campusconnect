<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role']!='organizer') {
    header("Location: log_reg.html");
    exit();
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusConnect</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #343a40;
        }

        .header {
            background-color: #343a40;
            color: white;
            padding: 10px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            font-size: 24px;
            font-weight: bold;
        }

        .navbar {
            display: flex;
            gap: 20px;
        }

        .nav-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .nav-btn:hover {
            background-color: #0056b3;
        }

        .nav-btn:focus {
            outline: none;
        }

        .main-content {
            margin-top: 70px;
            padding: 20px;
        }

        .section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .navbar {
                margin-top: 10px;
                flex-direction: column;
                gap: 10px;
            }

            .nav-btn {
                width: 100%;
                padding: 12px;
                font-size: 18px;
            }

            .section {
                padding: 15px;
            }
        }

    </style>
</head>
<body>
    <script>
        document.getElementById('dashboardBtn').addEventListener('click', function() {
            alert('Dashboard clicked');
        });

        document.getElementById('usersBtn').addEventListener('click', function() {
            alert('Users clicked');
        });

        document.getElementById('settingsBtn').addEventListener('click', function() {
            alert('Settings clicked');
        });

        document.getElementById('notificationsBtn').addEventListener('click', function() {
            alert('Notifications clicked');
        });

        document.getElementById('profileBtn').addEventListener('click', function() {
            alert('Profile clicked');
        });
    </script>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h1>CampusConnect</h1>
            </div>
            <nav class="navbar">
                <a href="#"><button class="nav-btn" id="dashboardBtn">Home</button></a>
                <a href="create_event.php"><button class="nav-btn" id="dashboardBtn">Create Events</button></a>
                <a href="approved_events.php"><button class="nav-btn" id="dashboardBtn">Approved Events</button></a>
                <button class="nav-btn" id="settingsBtn">Update Profile</button>
                <!-- <button class="nav-btn" id="notificationsBtn">Manage Participants</button> -->
                <a href="logout.php"><button class="nav-btn" id="dashboardBtn">Logout</button></a>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <section class="section">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>Organizer Panel</p>
        </section>
    </div>

    <script src="scripts.js"></script>
</body>
</html>
