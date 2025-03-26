<?php
// reset_password.php
require_once 'config.php';

$stage = 'request'; // Default stage
$message = '';
$message_type = 'info';
$email = '';

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function sendEmail($to, $subject, $body) {
    // Implement your email sending logic here
    // This is a placeholder and should be replaced with actual email sending mechanism
    return true;
}

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
                    $message_type = 'success';
                } else {
                    $message = "Error sending reset email.";
                    $message_type = 'error';
                }
            } else {
                $message = "No account found with this email.";
                $message_type = 'error';
            }
        } catch(PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $message_type = 'error';
        }
    } elseif (isset($_POST['reset_token']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
        // Stage 2: Reset password
        $reset_token = $_POST['reset_token'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $errors = [];
        if (empty($new_password)) {
            $errors[] = "New password is required.";
        } elseif (strlen($new_password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }

        if ($new_password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        if (empty($errors)) {
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

                    $message = "Password reset successfully. <a href='login.php' class='underline'>Login here</a>";
                    $message_type = 'success';
                } else {
                    $message = "Invalid or expired reset token.";
                    $message_type = 'error';
                }
            } catch(PDOException $e) {
                $message = "Error: " . $e->getMessage();
                $message_type = 'error';
            }
        } else {
            $message = implode("<br>", $errors);
            $message_type = 'error';
            $stage = 'reset';
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
            $message_type = 'error';
        }
    } catch(PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Brainkash Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #FF4E8E, #FF7A5A);
            --secondary-color: #6D2EFF;
            --background-color: #F9F5FF;
        }
        body {
            background: var(--background-color);
        }
        .brand-gradient {
            background: var(--primary-gradient);
            color: white;
        }
        .brand-button {
            background: var(--secondary-color);
            transition: transform 0.2s ease;
        }
        .brand-button:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <div class="brand-gradient p-6 text-center">
            <img src="imges\bk logo.png" alt="Brainkash Logo" class="mx-auto h-16 mb-4">
            <h2 class="text-3xl font-bold">Reset Password</h2>
                <p class="text-white/80 mt-2">
                    <?php echo $stage === 'request' 
                        ? 'Enter your email to reset your password' 
                        : 'Create a new password for your account'; 
                    ?>
                </p>
            </div>

            <div class="p-6">
                <?php if(!empty($message)): ?>
                    <div class="
                        <?php 
                        echo $message_type === 'success' 
                            ? 'bg-green-100 border-green-400 text-green-700' 
                            : 'bg-red-100 border-red-400 text-red-700'; 
                        ?> 
                        px-4 py-3 rounded-lg relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $message; ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($stage === 'request'): ?>
                    <form method="post" action="" class="space-y-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                                Email Address
                            </label>
                            <div class="relative">
                                <input 
                                    class="shadow-md appearance-none border-2 border-gray-200 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:border-secondary-color" 
                                    id="email" 
                                    name="email" 
                                    type="email" 
                                    placeholder="Enter your email" 
                                    value="<?php echo htmlspecialchars($email); ?>"
                                    required
                                >
                                <i data-feather="mail" class="absolute right-4 top-4 text-gray-400"></i>
                            </div>
                        </div>

                        <div>
                            <button 
                                class="brand-button text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline w-full" 
                                type="submit"
                            >
                                Send Reset Link
                            </button>
                        </div>
                    </form>
                <?php elseif ($stage === 'reset'): ?>
                    <form method="post" action="" class="space-y-4">
                        <input type="hidden" name="reset_token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
                        
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="new_password">
                                New Password
                            </label>
                            <div class="relative">
                                <input 
                                    class="shadow-md appearance-none border-2 border-gray-200 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:border-secondary-color" 
                                    id="new_password" 
                                    name="new_password" 
                                    type="password" 
                                    placeholder="Create a new password" 
                                    required
                                >
                                <i data-feather="lock" class="absolute right-4 top-4 text-gray-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm_password">
                                Confirm New Password
                            </label>
                            <div class="relative">
                                <input 
                                    class="shadow-md appearance-none border-2 border-gray-200 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:border-secondary-color" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    type="password" 
                                    placeholder="Confirm your new password" 
                                    required
                                >
                                <i data-feather="check-circle" class="absolute right-4 top-4 text-gray-400"></i>
                            </div>
                        </div>

                        <div>
                            <button 
                                class="brand-button text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline w-full" 
                                type="submit"
                            >
                                Reset Password
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <p class="text-gray-600 text-sm">
                        <a href="login.php" class="text-secondary-color hover:underline">
                            Back to Login
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        feather.replace();
    </script>
</body>
</html>