<?php
// login.php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Include database connection
try {
    include 'db.php';
} catch (Exception $e) {
    die("Failed to include db.php: " . $e->getMessage());
}

// Handle logout query parameter to clear session (for testing)
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validate input
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        try {
            // Query to fetch user by email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_type'] = $user['user_type'];
                    echo "<script>window.location.href = '{$user['user_type']}_profile.php';</script>";
                    exit;
                } else {
                    $error = "Incorrect password.";
                }
            } else {
                $error = "No account found with this email.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Monster Clone</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }
        .login-box {
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            width: 400px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }
        .login-box:hover {
            transform: translateY(-5px);
        }
        .login-box h2 {
            margin: 0 0 20px;
            color: #f5576c;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        input:focus {
            border-color: #f5576c;
            outline: none;
        }
        button {
            background: #f5576c;
            border: none;
            padding: 12px;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1.1em;
            transition: background 0.3s;
        }
        button:hover {
            background: #f093fb;
        }
        .error {
            color: #f5576c;
            margin: 10px 0;
            font-size: 0.9em;
        }
        a {
            color: #f5576c;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        a:hover {
            color: #f093fb;
        }
        @media (max-width: 600px) {
            .login-box {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login to Monster Clone</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="#" onclick="redirect('signup.php')">Sign Up</a></p>
        <p><a href="login.php?logout=1">Force Logout (Clear Session)</a></p>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
