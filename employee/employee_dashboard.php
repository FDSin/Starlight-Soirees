
<?php
session_start();
require_once '../check_auth.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Employee Dashboard - Starlight Soirées</title>
        <link rel="stylesheet" href="../css/dashboard_style.css">
    </head>
    <body>
        <aside class="sidebar">
            <ul class="sidebar-nav">
                <li><a href="#" class="active">Home</a></li>
                <li><a href="#">Events</a></li>
                <li><a href="#">Venue</a></li>
                <li><a href="#">Food & Catering</a></li>
                <li><a href="#">Payment</a></li>
                <li><a href="#">Report</a></li>
            </ul>
            <a href="../logout.php"
            style="color: #ef4444;
            text-decoration: none; 
            padding-left: 16px;">Logout</a>
        </aside>

        <div class="main-wrapper">
            <header class="top-header">
                <h2>Dashboard</h2>
                <div class="user-profile">
                    <div class="user-avatar">
                        <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                    </div>
                    <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                </div>
            </header>

            <main class="dashboard-content">
                <div class="card">
                    <h3 class="card-title">Active Events</h3>
                    <p style="font-size: 28px;
                        font-weight: 700;
                        color: var(--primary-navy);">12</p>
                </div>
                <div class="card">
                    <h3 class="card-tile">Pending Tasks</h3>
                    <p style="font-size: 28px;
                    font-weight: 700;
                    color: var(--text-gold);">5</p>
                </div>
                <div class="card">
                    <h3 class="card-title">Quick Actions</h3>
                    <button class="'btn-action">New Event</button>
                </div>
            </main>
        </div>
    </body>
</html>