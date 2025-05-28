<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$adopter_id = $_SESSION['user_id'];

$sql = "SELECT aa.*, p.name AS pet_name, p.species
        FROM adoption_applications aa
        JOIN pets p ON aa.pet_id = p.id
        WHERE aa.adopter_id = ?
        ORDER BY aa.application_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $adopter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Adoption Applications</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/adopter.css">
    <style>
        /* Same styles as before... */
        .schedule-btn {
            background-color: #000;
            color: #fff;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            cursor: pointer;
        }

        .schedule-btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="tracker-wrapper">
    <h2>📋 My Adoption Applications</h2>
    <a href="dashboard.php">← Back to Dashboard</a>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Pet Name</th>
                <th>Species</th>
                <th>Application Date</th>
                <th>Status / Action</th>
            </tr>
            <?php while($row = $result->fetch_assoc()):
                $application_id = $row['id'];

                // Check if interview already exists
                $checkInterview = $conn->prepare("SELECT id FROM interviews WHERE application_id = ?");
                $checkInterview->bind_param("i", $application_id);
                $checkInterview->execute();
                $interviewResult = $checkInterview->get_result();
                $hasInterview = $interviewResult->num_rows > 0;
            ?>
            <tr>
                <td><?= htmlspecialchars($row['pet_name']); ?></td>
                <td><?= htmlspecialchars($row['species']); ?></td>
                <td><?= htmlspecialchars($row['application_date']); ?></td>
                <td>
                    <div class="status-action">
                        <span class="status-text"><?= ucfirst(htmlspecialchars($row['status'])); ?></span>
                        <?php if ($row['status'] === 'pending'): ?>
                            <form method="POST" action="cancel_application.php" class="inline-form" onsubmit="return confirm('Are you sure you want to cancel this application?');">
                                <input type="hidden" name="application_id" value="<?= $row['id']; ?>">
                                <button type="submit" class="cancel-btn">Cancel</button>
                            </form>
                        <?php elseif ($row['status'] === 'approved' && !$hasInterview): ?>
                            <a href="schedule_interview.php?app_id=<?= $row['id']; ?>" class="schedule-btn">Schedule Interview</a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <div class="empty-msg">You haven't submitted any adoption applications yet.</div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
