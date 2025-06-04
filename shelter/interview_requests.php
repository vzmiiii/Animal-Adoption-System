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
    <link rel="stylesheet" href="../css/shelter.css">
    <style>
        /* Base styles */
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        /* Container for the entire content */
        .page-wrapper {
            max-width: 900px;
            margin: 80px auto;
            padding: 32px;
            background-color: #ffffff;
            border-radius: 16px;
            border: 1px solid #e0e0e0;
            
        }

        h2 {
            text-align: center;
            margin-bottom: 24px;
            font-size: 26px;
            font-weight: 600;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            background-color: #fafafa;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: 500;
        }

        tr + tr td {
            border-top: 1px solid #e0e0e0;
        }

        /* Action buttons (Confirm / Reject) */
        .action-btn {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .action-btn:hover {
            opacity: 0.9;
        }

        /* Status text when already handled */
        .status-text {
            font-weight: 500;
            text-transform: capitalize;
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .page-wrapper {
                margin: 40px 16px;
                padding: 24px;
            }

            th, td {
                padding: 10px 12px;
                font-size: 14px;
            }

            h2 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar (shelter version) -->
    <?php include('../includes/navbar_shelter.php'); ?>

    <div class="page-wrapper">
        <h2>📨 Interview Requests</h2>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Adopter</th>
                        <th>Pet</th>
                        <th>Scheduled At</th>
                        <th>Status / Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['adopter_name']); ?></td>
                            <td><?= htmlspecialchars($row['pet_name']); ?></td>
                            <td>
                                <?php
                                    // Format the interview date/time
                                    echo date("d M Y, h:i A", strtotime($row['interview_datetime']));
                                ?>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'pending'): ?>
                                    <!-- Confirm button -->
                                    <form method="POST" style="display:inline-block;" onsubmit="return confirm('Confirm this interview?');">
                                        <input type="hidden" name="interview_id" value="<?= $row['id']; ?>">
                                        <input type="hidden" name="action" value="confirm">
                                        <button type="submit" class="action-btn">✅ Confirm</button>
                                    </form>

                                    <!-- Reject button -->
                                    <form method="POST" style="display:inline-block;" onsubmit="return confirm('Reject this interview?');">
                                        <input type="hidden" name="interview_id" value="<?= $row['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="action-btn">❌ Reject</button>
                                    </form>
                                <?php else: ?>
                                    <!-- Show status text if already confirmed/rejected -->
                                    <span class="status-text"><?= htmlspecialchars($row['status']); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; margin-top: 24px;">No interview requests yet.</p>
        <?php endif; ?>
    </div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
