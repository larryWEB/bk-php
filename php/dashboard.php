<?php
// dashboard.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

// Optional: Fetch user details
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching user details: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    
    <p>This is your dashboard. You are now logged in.</p>
    
    <form action="logout.php" method="post">
        <input type="submit" value="Logout">
    </form>
</body>
</html>
