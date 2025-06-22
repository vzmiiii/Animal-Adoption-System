<?php
session_start();

// Restrict access: only allow shelter users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$shelter_id = $_SESSION['user_id'];

// -------------------------------------------------------------
// Handle POST: updating interview status (confirm or reject)
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['interview_id'], $_POST['action'])) {
    $interview_id = intval($_POST['interview_id']);
    $action = $_POST['action'];

    if (in_array($action, ['confirm', 'reject'], true)) {
        $new_status = ($action === 'confirm') ? 'confirmed' : 'rejected';

        // Update the interview record for this shelter
        $updateStmt = $conn->prepare("
            UPDATE interviews 
            SET status = ? 
            WHERE id = ? AND shelter_id = ?
        ");
        $updateStmt->bind_param("sii", $new_status, $interview_id, $shelter_id);
        $updateStmt->execute();
        $updateStmt->close();

        // Fetch the adopter_id and pet_id to send notification
        $infoStmt = $conn->prepare("
            SELECT adopter_id, pet_id 
            FROM interviews 
            WHERE id = ?
        ");
        $infoStmt->bind_param("i", $interview_id);
        $infoStmt->execute();
        $info = $infoStmt->get_result()->fetch_assoc();
        $infoStmt->close();

        // Fetch pet name for the message
        $petStmt = $conn->prepare("
            SELECT name 
            FROM pets 
            WHERE id = ?
        ");
        $petStmt->bind_param("i", $info['pet_id']);
        $petStmt->execute();
        $pet = $petStmt->get_result()->fetch_assoc();
        $petStmt->close();

        // Compose notification message
        $messageText = "Your interview for pet \"" . htmlspecialchars($pet['name']) . "\" was " . $new_status . ".";

        // Insert notification into notifications table for the adopter
        $notifStmt = $conn->prepare("
            INSERT INTO notifications (user_id, role, message) 
            VALUES (?, 'adopter', ?)
        ");
        $notifStmt->bind_param("is", $info['adopter_id'], $messageText);
        $notifStmt->execute();
        $notifStmt->close();

        // Redirect back to refresh the listing
        header("Location: interview_requests.php");
        exit();
    }
}

// -------------------------------------------------------------
// Fetch all interview requests for this shelter
// -------------------------------------------------------------
$listSql = "
    SELECT 
        i.id,
        i.interview_datetime,
        i.status,
        u.username AS adopter_name,
        p.name   AS pet_name
    FROM interviews i
    JOIN users u ON i.adopter_id = u.id
    JOIN pets  p ON i.pet_id     = p.id
    WHERE i.shelter_id = ?
    ORDER BY i.interview_datetime ASC
";
$listStmt = $conn->prepare($listSql);
$listStmt->bind_param("i", $shelter_id);
$listStmt->execute();
$result = $listStmt->get_result();
$listStmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Interview Requests</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(255,255,255,0.2), rgba(255,255,255,0.2)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }
        .content-container {
            max-width: 1100px;
            margin: 80px auto 40px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.92);
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.4);
        }
        .page-header h1 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-top: 0;
            margin-bottom: 30px;
        }
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
        }
        .styled-table th, .styled-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
            vertical-align: middle;
        }
        .styled-table th {
            font-weight: 600;
            color: #555;
            background-color: rgba(255,255,255,0.7);
        }
        .styled-table tr:last-child td { border-bottom: none; }
        .status-text {
            font-weight: 600;
            text-transform: capitalize;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 13px;
        }
        .status-text.confirmed { color: #1e6b3b; background-color: #d1f7de; }
        .status-text.rejected { color: #a82222; background-color: #ffe6e6; }
        .action-cell { display: flex; gap: 10px; }
        .action-btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            color: #fff;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-family: inherit;
            font-size: 14px;
        }
        .action-btn.confirm { background-color: #28a745; }
        .action-btn.confirm:hover { background-color: #218838; transform: translateY(-2px); }
        .action-btn.reject { background-color: #dc3545; }
        .action-btn.reject:hover { background-color: #c82333; transform: translateY(-2px); }
        .no-data {
            text-align: center;
            font-size: 1.2rem;
            color: #777;
            padding: 3rem;
            background: rgba(255,255,255,0.7);
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <?php include('../includes/navbar_shelter.php'); ?>
    <div class="content-container">
        <div class="page-header"><h1>📨 Interview Requests</h1></div>
        <?php if ($result->num_rows > 0): ?>
            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Adopter</th>
                            <th>Pet</th>
                            <th>Requested Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['adopter_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['pet_name']); ?></td>
                                <td><?php echo date("F j, Y, h:i A", strtotime($row['interview_datetime'])); ?></td>
                                <td>
                                    <?php if ($row['status'] === 'pending'): ?>
                                        <div class="action-cell">
                                            <form method="POST" onsubmit="return confirm('Confirm this interview?');">
                                                <input type="hidden" name="interview_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="action" value="confirm">
                                                <button type="submit" class="action-btn confirm">Confirm</button>
                                            </form>
                                            <form method="POST" onsubmit="return confirm('Reject this interview?');">
                                                <input type="hidden" name="interview_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="action-btn reject">Reject</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="status-text <?php echo strtolower($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-data">No new interview requests.</p>
        <?php endif; ?>
    </div>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
