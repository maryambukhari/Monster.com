<?php
// seeker_profile.php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'seeker') {
    echo "<script>redirect('login.php');</script>";
}
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM seeker_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $skills = $_POST['skills'];
    $experience = $_POST['experience'];
    $resume_path = '';
    if ($_FILES['resume']['name']) {
        $resume_path = 'assets/resumes/' . time() . '_' . $_FILES['resume']['name'];
        move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path);
    }
    if ($profile) {
        $stmt = $pdo->prepare("UPDATE seeker_profiles SET full_name = ?, skills = ?, experience = ?, resume_path = ? WHERE user_id = ?");
        $stmt->execute([$full_name, $skills, $experience, $resume_path ?: $profile['resume_path'], $user_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO seeker_profiles (user_id, full_name, skills, experience, resume_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $full_name, $skills, $experience, $resume_path]);
    }
    echo "<script>redirect('seeker_profile.php');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Profile - Monster Clone</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
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
            color: #ff9a9e;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #ff9a9e;
            border: none;
            padding: 12px;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #fad0c4;
        }
        a {
            color: #ff9a9e;
            text-decoration: none;
        }
        a:hover {
            color: #fad0c4;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Job Seeker Profile</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="full_name" placeholder="Full Name" value="<?php echo $profile['full_name'] ?? ''; ?>" required>
            <textarea name="skills" placeholder="Skills"><?php echo $profile['skills'] ?? ''; ?></textarea>
            <textarea name="experience" placeholder="Experience"><?php echo $profile['experience'] ?? ''; ?></textarea>
            <input type="file" name="resume" accept=".pdf">
            <button type="submit">Update Profile</button>
        </form>
        <p><a href="#" onclick="redirect('job_search.php')">Find Jobs</a></p>
        <p><a href="#" onclick="redirect('index.php')">Back to Home</a></p>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
