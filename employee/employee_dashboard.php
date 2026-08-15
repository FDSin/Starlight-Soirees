//TEMPORARY FOR TESTING(WILL CHANGE LATER)
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<body>
    <h1>Welcome to Employee Dashboard, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
</body>
</html>