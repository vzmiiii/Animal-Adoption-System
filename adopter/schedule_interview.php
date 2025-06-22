<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$app_id = $_GET['app_id'] ?? null;
if (!$app_id) {
    echo "Missing application ID.";
    exit();
}

$sql = "SELECT aa.*, p.name AS pet_name, p.id AS pet_id, p.shelter_id
        FROM adoption_applications aa
        JOIN pets p ON aa.pet_id = p.id
        WHERE aa.id = ? AND aa.adopter_id = ? AND aa.status = 'approved'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $app_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Invalid application.";
    exit();
}
$data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $datetime = $_POST['interview_datetime'];

    $insert = $conn->prepare("INSERT INTO interviews (application_id, adopter_id, shelter_id, pet_id, interview_datetime) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("iiiis", $app_id, $_SESSION['user_id'], $data['shelter_id'], $data['pet_id'], $datetime);
    if ($insert->execute()) {
        // Notify shelter
        $msg = "New interview scheduled for pet: " . $data['pet_name'];
        $notif = $conn->prepare("INSERT INTO notifications (user_id, role, message) VALUES (?, 'shelter', ?)");
        $notif->bind_param("is", $data['shelter_id'], $msg);
        $notif->execute();

        header("Location: adoption_tracker.php");
        exit();
    } else {
        echo "Error scheduling interview.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Interview</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/adopter.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <style>
        :root {
            --accent-gradient: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            --text-color: #333;
            --text-color-light: #555;
            --container-bg: rgba(255, 255, 255, 0.92);
            --border-color: #e0e0e0;
            --shadow: 0 8px 25px rgba(0,0,0,0.1);
            --border-radius: 16px;
        }
        .schedule-wrapper {
            max-width: 600px;
            margin: 80px auto 40px;
            padding: 40px;
            background: var(--container-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.4);
        }

        h2 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-top: 0;
            margin-bottom: 30px;
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        label {
            font-weight: 600;
            font-size: 16px;
            color: var(--text-color-light);
        }

        input[type="datetime-local"] {
            width: 100%;
            max-width: 300px;
            padding: 14px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            font-size: 15px;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input[type="datetime-local"]:focus {
            outline: none;
            border-color: #6ed6a5;
            box-shadow: 0 0 0 3px rgba(110, 214, 165, 0.18);
        }

        button {
            width: 100%;
            max-width: 300px;
            padding: 15px;
            background: var(--accent-gradient);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="schedule-wrapper">
    <h2>📅 Schedule Interview for <?= htmlspecialchars($data['pet_name']) ?></h2>
    <form method="POST">
        <label for="interview_datetime">Select a date and time for your interview:</label>
        <input type="datetime-local" id="interview_datetime" name="interview_datetime" required>
        <button type="submit">Confirm Interview Slot</button>
    </form>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>
