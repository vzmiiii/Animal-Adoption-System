<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$shelter_id = $_SESSION['user_id'];

// Handle status update if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['interview_id'], $_POST['action'])) {
    $interview_id = intval($_POST['interview_id']);
    $action = $_POST['action'];

    if (in_array($action, ['confirm', 'reject'])) {
        $new_status = $action === 'confirm' ? 'confirmed' : 'rejected';
        $update = $conn->prepare("UPDATE interviews SET status = ? WHERE id = ? AND shelter_id = ?");
        $update->bind_param("sii", $new_status, $interview_id, $shelter_id);
        $update->execute();

        // Fetch adopter ID and pet name
        $infoQuery = $conn->prepare("SELECT adopter_id, pet_id FROM interviews WHERE id = ?");
        $infoQuery->bind_param("i", $interview_id);
        $infoQuery->execute();
        $info = $infoQuery->get_result()->fetch_assoc();

        $petQuery = $conn->prepare("SELECT name FROM pets WHERE id = ?");
        $petQuery->bind_param("i", $info['pet_id']);
        $petQuery->execute();
        $pet = $petQuery->get_result()->fetch_assoc();

        $message = "Your interview for pet '" . $pet['name'] . "' was " . $new_status . ".";

        $notif = $conn->prepare("INSERT INTO notifications (user_id, role, message) VALUES (?, 'adopter', ?)");
        $notif->bind_param("is", $info['adopter_id'], $message);
        $notif->execute();

        header("Location: interview_requests.php");
        exit();
    }
}

// Fetch interview requests
$sql = "SELECT i.*, u.username AS adopter_name, p.name AS pet_name
        FROM interviews i
        JOIN users u ON i.adopter_id = u.id
        JOIN pets p ON i.pet_id = p.id
        WHERE i.shelter_id = ?
        ORDER BY i.interview_datetime ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shelter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Interview Requests</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/shelter.css">
    <style>
        body {
            background-color: #fff;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
        }

        .tracker-wrapper {
            max-width: 900px;
            margin: 80px auto;
            padding: 40px;
            background-color: #fce7cd;
            border-radius: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #eee;
        }

        td form {
            display: inline-block;
            margin-right: 8px;
        }

        button {
            background-color: #000;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }

        .status-text {
            font-weight: 500;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_shelter.php'); ?>

<div class="tracker-wrapper">
    <h2>📨 Interview Requests</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Adopter</th>
                <th>Pet</th>
                <th>Scheduled At</th>
                <th>Status / Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['adopter_name']) ?></td>
                    <td><?= htmlspecialchars($row['pet_name']) ?></td>
                    <td><?= date("d M Y, h:i A", strtotime($row['interview_datetime'])) ?></td>
                    <td>
                        <?php if ($row['status'] === 'pending'): ?>
                            <form method="POST" onsubmit="return confirm('Confirm this interview?');">
                                <input type="hidden" name="interview_id" value="<?= $row['id']; ?>">
                                <input type="hidden" name="action" value="confirm">
                                <button type="submit">✅ Confirm</button>
                            </form>
                            <form method="POST" onsubmit="return confirm('Reject this interview?');">
                                <input type="hidden" name="interview_id" value="<?= $row['id']; ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit">❌ Reject</button>
                            </form>
                        <?php else: ?>
                            <span class="status-text"><?= ucfirst($row['status']) ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No interview requests yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
