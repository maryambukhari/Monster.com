<?php
// job_search.php
session_start();
include 'db.php';
$filters = [];
$sql = "SELECT * FROM jobs WHERE 1=1";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['category'])) {
        $filters[] = "category = " . $pdo->quote($_POST['category']);
    }
    if (!empty($_POST['location'])) {
        $filters[] = "location = " . $pdo->quote($_POST['location']);
    }
    if (!empty($_POST['salary_range'])) {
        $filters[] = "salary_range = " . $pdo->quote($_POST['salary_range']);
    }
    if (!empty($_POST['job_type'])) {
        $filters[] = "job_type = " . $pdo->quote($_POST['job_type']);
    }
    if (!empty($_POST['experience_level'])) {
        $filters[] = "experience_level = " . $pdo->quote($_POST['experience_level']);
    }
    if ($filters) {
        $sql .= " AND " . implode(" AND ", $filters);
    }
}
$stmt = $pdo->query($sql);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Jobs - Monster Clone</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #a1c4fd, #c2e9fb);
            margin: 0;
            color: #fff;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .filter-box {
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        .job-card {
            background: rgba(255, 255, 255, 0.95);
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
            color: #a1c4fd;
        }
        input, select {
            padding: 10px;
            margin: 10px 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #a1c4fd;
            border: none;
            padding: 12px;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #c2e9fb;
        }
        a {
            color: #a1c4fd;
            text-decoration: none;
        }
        a:hover {
            color: #c2e9fb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Find Jobs</h2>
        <div class="filter-box">
            <form method="POST">
                <input type="text" name="category" placeholder="Category">
                <input type="text" name="location" placeholder="Location">
                <input type="text" name="salary_range" placeholder="Salary Range">
                <select name="job_type">
                    <option value="">Job Type</option>
                    <option value="full-time">Full-Time</option>
                    <option value="part-time">Part-Time</option>
                    <option value="remote">Remote</option>
                </select>
                <input type="text" name="experience_level" placeholder="Experience Level">
                <button type="submit">Filter</button>
            </form>
        </div>
        <?php foreach ($jobs as $job): ?>
            <div class="job-card">
                <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                <p><?php echo htmlspecialchars($job['description']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary_range']); ?></p>
                <button onclick="redirect('job_apply.php?job_id=<?php echo $job['id']; ?>')">Apply Now</button>
            </div>
        <?php endforeach; ?>
        <p><a href="#" onclick="redirect('index.php')">Back to Home</a></p>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
