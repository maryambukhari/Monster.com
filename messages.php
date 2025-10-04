<?php
// messages.php
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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';

// Fetch sent and received messages
try {
    $stmt = $pdo->prepare("
        SELECT m.*, 
               u_sender.username AS sender_username, 
               u_receiver.username AS receiver_username,
               j.title AS job_title
        FROM messages m
        LEFT JOIN users u_sender ON m.sender_id = u_sender.id
        LEFT JOIN users u_receiver ON m.receiver_id = u_receiver.id
        LEFT JOIN jobs j ON m.job_id = j.id
        WHERE m.sender_id = ? OR m.receiver_id = ?
        ORDER BY m.sent_at DESC
    ");
    $stmt->execute([$user_id, $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch messages: " . $e->getMessage();
}

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $receiver_id = filter_var($_POST['receiver_id'], FILTER_VALIDATE_INT);
    $job_id = !empty($_POST['job_id']) ? filter_var($_POST['job_id'], FILTER_VALIDATE_INT) : null;
    $message = trim(filter_var($_POST['message'], FILTER_SANITIZE_STRING));

    // Validate input
    if (!$receiver_id || $receiver_id <= 0) {
        $error = "Please enter a valid receiver ID.";
    } elseif ($receiver_id == $user_id) {
        $error = "You cannot send a message to yourself.";
    } elseif (empty($message)) {
        $error = "Message cannot be empty.";
    } else {
        try {
            // Verify receiver exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$receiver_id]);
            if (!$stmt->fetch()) {
                $error = "Receiver ID does not exist.";
            } else {
                // Verify job_id if provided
                if ($job_id) {
                    $stmt = $pdo->prepare("SELECT id FROM jobs WHERE id = ?");
                    $stmt->execute([$job_id]);
                    if (!$stmt->fetch()) {
                        $error = "Invalid job ID.";
                    }
                }
                // Insert message if no errors
                if (!$error) {
                    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, job_id, message) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$user_id, $receiver_id, $job_id, $message]);
                    echo "<script>window.location.href = 'messages.php';</script>";
                    exit;
                }
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
    <title>Messages - Monster Clone</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #d4fc79, #96e6a1);
            margin: 0;
            color: #fff;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease-in-out;
        }
        .container:hover {
            transform: translateY(-5px);
        }
        h2, h3 {
            color: #96e6a1;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }
        .message {
            border-bottom: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .message.sent {
            background: #e6f3e6;
        }
        .message.received {
            background: #f0f0f0;
        }
        input, textarea, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #96e6a1;
            outline: none;
        }
        button {
            background: #96e6a1;
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
            background: #d4fc79;
        }
        .error {
            color: #96e6a1;
            margin: 10px 0;
            font-size: 0.9em;
        }
        a {
            color: #96e6a1;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        a:hover {
            color: #d4fc79;
        }
        @media (max-width: 600px) {
            .container {
                width: 90%;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Messages</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="number" name="receiver_id" placeholder="Receiver ID" required>
            <input type="number" name="job_id" placeholder="Job ID (Optional)">
            <textarea name="message" placeholder="Type your message" required></textarea>
            <button type="submit">Send Message</button>
        </form>
        <h3>Inbox</h3>
        <?php if (empty($messages)): ?>
            <p>No messages found.</p>
        <?php else: ?>
            <?php foreach ($messages as $message): ?>
                <div class="message <?php echo $message['sender_id'] == $user_id ? 'sent' : 'received'; ?>">
                    <p><strong><?php echo $message['sender_id'] == $user_id ? 'To' : 'From'; ?>:</strong> 
                       <?php echo htmlspecialchars($message['sender_id'] == $user_id ? $message['receiver_username'] : $message['sender_username']); ?>
                    </p>
                    <p><strong>Message:</strong> <?php echo htmlspecialchars($message['message']); ?></p>
                    <?php if ($message['job_id']): ?>
                        <p><strong>Job:</strong> <?php echo htmlspecialchars($message['job_title'] ?: 'Job ID ' . $message['job_id']); ?></p>
                    <?php endif; ?>
                    <p><small>Sent: <?php echo $message['sent_at']; ?></small></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <p><a href="#" onclick="redirect('index.php')">Back to Home</a></p>
    </div>
    <script>
        function redirect(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
