<?php
    session_start();
    require_once 'db.php';

    if(isset($SESSION['user_id'])) {
        header("Location: employee/employee_dashboard.php");
        exit();
    }

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($POST['email'] ?? '');
        $password = trim($POST['password'] ?? '');

        if (!empty($email) && !empty($password)) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM employees WHERE email = :email LIMIT 1");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify ($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['full_name'];

                    header("Location: employee/employee_dashboard.php");
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            }catch (PDOException $e) {
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
                <p>Operations Portal</p>
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
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Please enter email address" required>
                </div>

                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Please enter password" required>
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form>
        </section>
    </body>
</html>
