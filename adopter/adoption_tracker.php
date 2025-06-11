<?php
session_start();

// Redirect if not logged in or not an adopter
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
        ORDER BY aa.application_date ASC";

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
        .tracker-wrapper {
            max-width: 900px;
            margin: 80px auto;
            padding: 40px;
            background-color: #fef9ec;
            border-radius: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        h2 {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
        }

        th, td {
            padding: 14px 18px;
            font-size: 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #eee;
            font-weight: bold;
        }

        .status-action {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .status-text {
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-text.approved {
            color: #2e7d32;
        }

        .status-text.pending {
            color: #ff9800;
        }

        .status-text.rejected {
            color: #d32f2f;
        }

        .cancel-btn {
            background-color: #ff4d4d;
            color: #fff;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            cursor: pointer;
        }

        .cancel-btn:hover {
            background-color: #e60000;
        }

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

        .inline-form {
            display: inline;
        }

        .empty-msg {
            text-align: center;
            color: #777;
            margin-top: 40px;
            font-style: italic;
        }
    </style>
</head>
<body>

<?php include('../includes/navbar_adopter.php'); ?>

<div class="tracker-wrapper">
    <h2>📋 My Adoption Applications</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Pet Name</th>
                <th>Species</th>
                <th>Application Date</th>
                <th>Status / Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()):
                $application_id = $row['id'];

                // Check interview
                $checkInterview = $conn->prepare("SELECT id FROM interviews WHERE application_id = ?");
                $checkInterview->bind_param("i", $application_id);
                $checkInterview->execute();
                $interviewResult = $checkInterview->get_result();
                $hasInterview = $interviewResult->num_rows > 0;

                $status = strtolower($row['status']);
                $status_class = "status-text " . $status;
            ?>
            <tr>
                <td><?= htmlspecialchars($row['pet_name']); ?></td>
                <td><?= htmlspecialchars($row['species']); ?></td>
                <td><?= htmlspecialchars($row['application_date']); ?></td>
                <td>
                    <div class="status-action">
                        <span class="<?= $status_class ?>"><?= ucfirst($status); ?></span>

                        <?php if ($status === 'pending'): ?>
                            <form method="POST" action="cancel_application.php" class="inline-form" onsubmit="return confirm('Are you sure you want to cancel this application?');">
                                <input type="hidden" name="application_id" value="<?= $application_id; ?>">
                                <button type="submit" class="cancel-btn">Cancel</button>
                            </form>
                        <?php elseif ($status === 'approved' && !$hasInterview): ?>
                            <a href="schedule_interview.php?app_id=<?= $application_id; ?>" class="schedule-btn">Schedule Interview</a>
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
