<?php
    session_start();
    require_once 'db.php';

    if (isset($_SESSION['user_id'])) {
        header("Location: employee/employee_dashboard.php");
        exit();
    }

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!empty($username) && !empty($password)) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
                $stmt->execute(['username' => $username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];

                    header("Location: employee/employee_dashboard.php");
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        } else {
            $error = "Please fill in all necessary fields.";
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Employee Login - Starlight Soirées</title>
        <link rel="stylesheet" href="css/login_style.css">
    </head>
    <body>
        <section class="login-card">
            <header>
                <h1>Starlight Soirées</h1>
            </header>

            <?php if (!empty($error)): ?>
                <div style="background: rgba(239, 68, 68, 0.2); 
                    border: 1px solid #ef4444; 
                    color: #fca5a5; 
                    padding: 10px; 
                    border-radius: 8px; 
                    margin-bottom: 20px; 
                    text-align: center; 
                    font-size: 13px;">
                <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="input-group">
                    <label for="username">Email:</label>
                    <input type="text" id="username" name="username" placeholder="name@example.com" required>
                </div>

                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form>
        </section>
    </body>
</html>
