<?php
// job_post.php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employer') {
    echo "<script>redirect('login.php');</script>";
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $salary_range = $_POST['salary_range'];
    $job_type = $_POST['job_type'];
    $experience_level = $_POST['experience_level'];
    $employer_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("INSERT INTO jobs (employer_id, title, description, category, location, salary_range, job_type, experience_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$employer_id, $title, $description, $category, $location, $salary_range, $job_type, $experience_level]);
    echo "<script>redirect('employer_profile.php');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job - Monster Clone</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #6b7280, #4b5563);
            margin: 0;
            color: #fff;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        h2 {
            color: #4b5563;
        }
        input, textarea, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #6b7280;
            border: none;
            padding: 12px;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #4b5563;
        }
        a {
            color: #6b7280;
            text-decoration: none;
        }
        a:hover {
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Post a Job</h2>
        <form method="POST">
            <input type="text" name="title" placeholder="Job Title" required>
            <textarea name="description" placeholder="Job Description" required></textarea>
            <input type="text" name="category" placeholder="Category" required>
            <input type="text" name="location" placeholder="Location" required>
            <input type="text" name="salary_range" placeholder="Salary Range">
            <select name="job_type" required>
                <option value="full-time">Full-Time</option>
                <option value="part-time">Part-Time</option>
                <option value="remote">Remote</option>
            </select>
            <input type="text" name="experience_level" placeholder="Experience Level">
            <button type="submit">Post Job</button>
        </form>
        <p><a href="#" onclick="redirect('employer_profile.php')">Back to Profile</a></p>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
