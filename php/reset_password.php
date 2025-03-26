<?php
// reset_password.php
require_once 'config.php';

$stage = 'request'; // Default stage
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && !isset($_POST['reset_token'])) {
        // Stage 1: Generate and send reset token
        $email = trim($_POST['email']);
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Generate reset token
                $reset_token = generateToken();
                $reset_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Store token in database
                $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
                $stmt->execute([$reset_token, $reset_expiry, $email]);

                // Send reset email
                $reset_link = "http://yourwebsite.com/reset_password.php?token=" . $reset_token;
                $email_message = "Click the following link to reset your password: $reset_link";
                
                if (sendEmail($email, "Password Reset", $email_message)) {
                    $message = "A password reset link has been sent to your email.";
                } else {
                    $message = "Error sending reset email.";
                }
            } else {
                $message = "No account found with this email.";
            }
        } catch(PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['reset_token']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
        // Stage 2: Reset password
        $reset_token = $_POST['reset_token'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $message = "Passwords do not match.";
            $stage = 'reset';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
                $stmt->execute([$reset_token]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    // Hash new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update password and clear reset token
                    $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
                    $stmt->execute([$hashed_password, $user['id']]);

                    $message = "Password reset successfully. <a href='login.php'>Login here</a>";
                } else {
                    $message = "Invalid or expired reset token.";
                }
            } catch(PDOException $e) {
                $message = "Error: " . $e->getMessage();
            }
        }
    }
} elseif (isset($_GET['token'])) {
    // Check token validity
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
        $stmt->execute([$_GET['token']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $stage = 'reset';
        } else {
            $message = "Invalid or expired reset token.";
        }
    } catch(PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <?php if (!empty($message)) echo "<p>" . $message . "</p>"; ?>

    <?php if ($stage === 'request'): ?>
    <form method="post" action="">
        <input type="email" name="email" placeholder="Enter your email" required><br>
        <input type="submit" value="Send Reset Link">
    </form>
    <?php elseif ($stage === 'reset'): ?>
    <form method="post" action="">
        <input type="hidden" name="reset_token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
        <input type="password" name="new_password" placeholder="New Password" required><br>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required><br>
        <input type="submit" value="Reset Password">
    </form>
    <?php endif; ?>

    <p><a href="login.php">Back to Login</a></p>
</body>
</html>
