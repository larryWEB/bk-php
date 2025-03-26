<?php
session_start();
require_once 'config.php'; // Database connection

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle form submission
$error = "";
$username = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        try {
            // Prepare SQL statement
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify user credentials
            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Redirect to index page
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error. Please try again later.";
        }
    } else {
        $error = "Please enter both username and password.";
    }
}

// Check for signup success
$signup_success = isset($_GET['signup']) && $_GET['signup'] === 'success';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to Brainkash</title>
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
                <h2 class="text-3xl font-bold">Welcome Back</h2>
                <p class="text-white/80 mt-2">Login to continue learning and earning</p>
            </div>

            <div class="p-6">
                <?php if($signup_success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                        <span class="block sm:inline">Account created successfully! Please log in.</span>
                    </div>
                <?php endif; ?>

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
                                placeholder="Enter your username" 
                                value="<?php echo htmlspecialchars($username); ?>"
                                required
                            >
                            <i data-feather="user" class="absolute right-4 top-4 text-gray-400"></i>
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
                                placeholder="Enter your password" 
                                required
                            >
                            <i data-feather="lock" class="absolute right-4 top-4 text-gray-400"></i>
                        </div>
                        <div class="text-right mt-2">
                            <a href="reset_password.php" class="text-sm text-secondary-color hover:underline">
                                Forgot Password?
                            </a>
                        </div>
                    </div>

                    <div>
                        <button 
                            class="brand-button text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline w-full" 
                            type="submit"
                        >
                            Log In
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p class="text-gray-600 text-sm">
                        Don't have an account? 
                        <a href="signup.php" class="text-secondary-color hover:underline">Sign up here</a>
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