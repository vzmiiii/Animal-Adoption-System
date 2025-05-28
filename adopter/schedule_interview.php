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
<html>
<head>
    <title>Schedule Interview</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/adopter.css">
</head>
<body>

<div class="tracker-wrapper">
    <h2>📅 Schedule Interview for <?= htmlspecialchars($data['pet_name']) ?></h2>
    <form method="POST">
        <label for="interview_datetime">Select Date & Time:</label><br><br>
        <input type="datetime-local" name="interview_datetime" required>
        <br><br>
        <button type="submit" class="schedule-btn">Submit</button>
    </form>
</div>

</body>
</html>
