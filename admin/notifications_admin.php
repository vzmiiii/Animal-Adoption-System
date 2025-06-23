<?php
// AJAX endpoint for fetching users by role (must be before any output)
if (isset($_GET['fetch_users']) && isset($_GET['role'])) {
    include('../db_connection.php');
    $role = $_GET['role'];
    $users = [];
    $sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS name, email FROM users WHERE role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($users);
    exit();
}

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $audience = $_POST['audience'];
    $message = trim($_POST['message']);
    $user_ids = isset($_POST['user_ids']) ? $_POST['user_ids'] : [];

    if (!empty($message)) {
        if ($audience === 'all') {
            $user_sql = "SELECT id FROM users";
            $users = $conn->query($user_sql);
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            while ($row = $users->fetch_assoc()) {
                $stmt->bind_param("is", $row['id'], $message);
                $stmt->execute();
            }
            $msg = "Notification sent successfully to all users.";
        } else {
            if (!empty($user_ids) && is_array($user_ids)) {
                $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                foreach ($user_ids as $uid) {
                    $uid = intval($uid);
                    $stmt->bind_param("is", $uid, $message);
                    $stmt->execute();
                }
                $msg = "Notification sent successfully to selected users.";
            } else {
                $msg = "Please select at least one user.";
            }
        }
    } else {
        $msg = "Message cannot be empty.";
    }
    // To prevent resubmission on refresh
    echo '<script>window.location.href = window.location.pathname + "?msg=" + encodeURIComponent("' . $msg . '");</script>';
    exit();
}
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Send Notifications</title>
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(255,255,255,0.2), rgba(255,255,255,0.2)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }

        .page-wrapper {
            padding: 40px 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .content-container {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            margin-top: 20px;
        }

        h1 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 40px;
            color: #2c3e50;
            font-size: 2.8em;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
        }

        form { 
            margin-top: 20px; 
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
            font-size: 16px;
        }

        textarea, select {
            width: 100%;
            padding: 16px;
            margin-bottom: 25px;
            font-size: 16px;
            border: 1.5px solid rgba(0,0,0,0.1);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s;
            box-sizing: border-box;
        }

        textarea:focus, select:focus {
            outline: none;
            border-color: #6ed6a5;
            box-shadow: 0 0 10px rgba(110, 214, 165, 0.2);
        }

        .multi-select {
            height: 120px;
        }

        button {
            width: 100%;
            padding: 16px 24px;
            background: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .message {
            background: #e1f8e1;
            border: 1px solid #b3e6b3;
            padding: 15px;
            margin-bottom: 20px;
            color: #333;
            border-radius: 12px;
            text-align: center;
        }
    </style>
    <script>
    function fetchUsers(role) {
        if (role === 'all') {
            document.getElementById('user-select-group').style.display = 'none';
            return;
        }
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'notifications_admin.php?fetch_users=1&role=' + encodeURIComponent(role), true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var users = JSON.parse(xhr.responseText);
                var select = document.getElementById('user_ids');
                select.innerHTML = '';
                users.forEach(function(user) {
                    var opt = document.createElement('option');
                    opt.value = user.id;
                    opt.textContent = user.name + ' (' + user.email + ')';
                    select.appendChild(opt);
                });
                document.getElementById('user-select-group').style.display = 'block';
            }
        };
        xhr.send();
    }
    window.onload = function() {
        var audience = document.getElementById('audience');
        audience.addEventListener('change', function() {
            fetchUsers(this.value);
        });
        // On page load, if not 'all', fetch users
        if (audience.value !== 'all') {
            fetchUsers(audience.value);
        } else {
            document.getElementById('user-select-group').style.display = 'none';
        }
    };
    </script>
</head>
<body>
<?php include('../includes/navbar_admin.php'); ?>

<div class="page-wrapper">
    <div class="content-container">
        <h1>Send Notification</h1>

        <?php if ($msg): ?>
            <div class="message"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="post">
            <label for="audience">Select Audience:</label>
            <select name="audience" id="audience" required>
                <option value="all">All Users</option>
                <option value="adopter">Adopters Only</option>
                <option value="shelter">Shelter Personnel Only</option>
            </select>
            <div id="user-select-group" style="display:none;">
                <label for="user_ids">Select Users (hold Ctrl or Cmd to select multiple):</label>
                <select name="user_ids[]" id="user_ids" class="multi-select" multiple></select>
            </div>
            <label for="message">Notification Message:</label>
            <textarea name="message" id="message" rows="5" required></textarea>
            <button type="submit">Send Notification</button>
        </form>
    </div>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
