<?php
// signup.php
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

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = '{$_SESSION['user_type']}_profile.php';</script>";
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($user_type)) {
        $error = "All fields are required.";
    } elseif (!in_array($user_type, ['employer', 'seeker'])) {
        $error = "Invalid user type.";
    } else {
        try {
            // Check for duplicate email or username
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Email or username already exists.";
            } else {
                // Hash password and insert user
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $email, $password_hash, $user_type]);

                // Set session variables
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_type'] = $user_type;

                // Redirect to appropriate profile
                echo "<script>window.location.href = '{$user_type}_profile.php';</script>";
                exit;
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
    <title>Sign Up - Monster Clone</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }
        .signup-box {
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            width: 400px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }
        .signup-box:hover {
            transform: translateY(-5px);
        }
        .signup-box h2 {
            margin: 0 0 20px;
            color: #764ba2;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        input:focus, select:focus {
            border-color: #764ba2;
            outline: none;
        }
        button {
            background: #764ba2;
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
            background: #667eea;
        }
        .error {
            color: #764ba2;
            margin: 10px 0;
            font-size: 0.9em;
        }
        a {
            color: #764ba2;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        a:hover {
            color: #667eea;
        }
        @media (max-width: 600px) {
            .signup-box {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="signup-box">
        <h2>Sign Up</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="user_type" required>
                <option value="">Select User Type</option>
                <option value="seeker">Job Seeker</option>
                <option value="employer">Employer</option>
            </select>
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="#" onclick="redirect('login.php')">Login</a></p>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
