<?php
// signup.php
require_once 'config.php';

$username = $email = $password = $confirm_password = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Enhanced input validation
    $errors = [];
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Username or email already exists.";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                if ($stmt->execute([$username, $email, $hashed_password])) {
                    header("Location: login.php?signup=success");
                    exit();
                }
            }
        } catch(PDOException $e) {
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }

    $error = implode("<br>", $errors);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Brainkash Account</title>
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
            <h2 class="text-3xl font-bold">Create Your Account</h2>
                <p class="text-white/80 mt-2">Start earning with knowledge!</p>
            </div>

            <div class="p-6">
                <?php if(!empty($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $error; ?></span>
                    </div>
                <?php endif; ?>

                <form method="post" action="" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                            Username
                        </label>
                        <div class="relative">
                            <input 
                                class="shadow-md appearance-none border-2 border-gray-200 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:border-secondary-color" 
                                id="username" 
                                name="username" 
                                type="text" 
                                placeholder="Choose a username" 
                                value="<?php echo htmlspecialchars($username); ?>"
                                required
                            >
                            <i data-feather="user" class="absolute right-4 top-4 text-gray-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                            Email
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
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                            Password
                        </label>
                        <div class="relative">
                            <input 
                                class="shadow-md appearance-none border-2 border-gray-200 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:border-secondary-color" 
                                id="password" 
                                name="password" 
                                type="password" 
                                placeholder="Create a strong password" 
                                required
                            >
                            <i data-feather="lock" class="absolute right-4 top-4 text-gray-400"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm_password">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <input 
                                class="shadow-md appearance-none border-2 border-gray-200 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:border-secondary-color" 
                                id="confirm_password" 
                                name="confirm_password" 
                                type="password" 
                                placeholder="Confirm your password" 
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
                            Sign Up
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="text-gray-600 text-sm">
                        Already have an account? 
                        <a href="login.php" class="text-secondary-color hover:underline">Login here</a>
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