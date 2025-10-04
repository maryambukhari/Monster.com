<?php
// employer_profile.php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'employer') {
    echo "<script>redirect('login.php');</script>";
}
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM employer_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_name = $_POST['company_name'];
    $company_description = $_POST['company_description'];
    $website = $_POST['website'];
    if ($profile) {
        $stmt = $pdo->prepare("UPDATE employer_profiles SET company_name = ?, company_description = ?, website = ? WHERE user_id = ?");
        $stmt->execute([$company_name, $company_description, $website, $user_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO employer_profiles (user_id, company_name, company_description, website) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $company_name, $company_description, $website]);
    }
    echo "<script>redirect('employer_profile.php');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Profile - Monster Clone</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #43cea2, #185a9d);
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
            color: #185a9d;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #43cea2;
            border: none;
            padding: 12px;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #185a9d;
        }
        a {
            color: #43cea2;
            text-decoration: none;
        }
        a:hover {
            color: #185a9d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Employer Profile</h2>
        <form method="POST">
            <input type="text" name="company_name" placeholder="Company Name" value="<?php echo $profile['company_name'] ?? ''; ?>" required>
            <textarea name="company_description" placeholder="Company Description"><?php echo $profile['company_description'] ?? ''; ?></textarea>
            <input type="url" name="website" placeholder="Website" value="<?php echo $profile['website'] ?? ''; ?>">
            <button type="submit">Update Profile</button>
        </form>
        <p><a href="#" onclick="redirect('job_post.php')">Post a Job</a></p>
        <p><a href="#" onclick="redirect('index.php')">Back to Home</a></p>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
