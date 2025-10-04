<?php
// index.php
session_start();
include 'db.php';
$stmt = $pdo->query("SELECT * FROM jobs ORDER BY posted_at DESC LIMIT 6");
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monster Clone - Job Portal</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            margin: 0;
            color: #fff;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        header {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        header h1 {
            font-size: 2.5em;
            margin: 0;
            color: #00d4ff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        nav a {
            color: #fff;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        nav a:hover {
            color: #00d4ff;
        }
        .job-card {
            background: #fff;
            color: #333;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
        }
        .job-card:hover {
            transform: translateY(-5px);
        }
        .job-card h3 {
            margin: 0;
            color: #2a5298;
        }
        .categories {
            display: flex;
            justify-content: space-around;
            margin: 40px 0;
        }
        .category {
            background: #00d4ff;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            width: 20%;
            transition: background 0.3s;
        }
        .category:hover {
            background: #1e3c72;
        }
        button {
            background: #00d4ff;
            border: none;
            padding: 10px 20px;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #1e3c72;
        }
    </style>
</head>
<body>
    <header>
        <h1>Monster Clone</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="#" onclick="redirect('job_search.php')">Find Jobs</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#" onclick="redirect('<?php echo $_SESSION['user_type'] == 'employer' ? 'employer_profile.php' : 'seeker_profile.php'; ?>')">Profile</a>
                <a href="#" onclick="redirect('messages.php')">Messages</a>
                <a href="#" onclick="redirect('logout.php')">Logout</a>
            <?php else: ?>
                <a href="#" onclick="redirect('signup.php')">Sign Up</a>
                <a href="#" onclick="redirect('login.php')">Login</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="container">
        <h2>Featured Jobs</h2>
        <?php foreach ($jobs as $job): ?>
            <div class="job-card">
                <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                <p><?php echo htmlspecialchars($job['description']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary_range']); ?></p>
                <button onclick="redirect('job_apply.php?job_id=<?php echo $job['id']; ?>')">Apply Now</button>
            </div>
        <?php endforeach; ?>
        <h2>Trending Categories</h2>
        <div class="categories">
            <div class="category">Technology</div>
            <div class="category">Finance</div>
            <div class="category">Healthcare</div>
            <div class="category">Marketing</div>
        </div>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
