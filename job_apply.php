<?php
// job_apply.php
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

// Check if user is logged in as a seeker
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'seeker') {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

// Validate job_id
$job_id = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;
if ($job_id <= 0) {
    die("Invalid job ID.");
}

// Fetch job details
try {
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
    $stmt->execute([$job_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$job) {
        die("Job not found.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $seeker_id = $_SESSION['user_id'];

    // Handle resume upload
    $resume_path = '';
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'assets/resumes/';
        // Ensure upload directory exists and is writable
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $resume_path = $upload_dir . time() . '_' . basename($_FILES['resume']['name']);
        if (!move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path)) {
            $error = "Failed to upload resume.";
        }
    } else {
        $error = "Please upload a valid PDF resume.";
    }

    // Insert application if no upload error
    if (!$error) {
        try {
            $stmt = $pdo->prepare("INSERT INTO applications (job_id, seeker_id, resume_path) VALUES (?, ?, ?)");
            $stmt->execute([$job_id, $seeker_id, $resume_path]);
            echo "<script>window.location.href = 'job_search.php';</script>";
            exit;
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
    <title>Apply for Job - Monster Clone</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ffecd2, #fcb69f);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            width: 400px;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }
        .container:hover {
            transform: translateY(-5px);
        }
        h2 {
            color: #fcb69f;
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
            border-color: #fcb69f;
            outline: none;
        }
        button {
            background: #fcb69f;
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
            background: #ffecd2;
        }
        .error {
            color: #fcb69f;
            margin: 10px 0;
            font-size: 0.9em;
        }
        a {
            color: #fcb69f;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        a:hover {
            color: #ffecd2;
        }
        @media (max-width: 600px) {
            .container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Apply for <?php echo htmlspecialchars($job['title']); ?></h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="resume" accept=".pdf" required>
            <button type="submit">Apply</button>
        </form>
        <p><a href="#" onclick="redirect('job_search.php')">Back to Jobs</a></p>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
