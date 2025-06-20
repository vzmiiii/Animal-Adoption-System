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

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(rgba(255,255,255,0.5), rgba(255,255,255,0.5)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-color);
        }

        .tracker-wrapper {
            max-width: 900px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: transparent;
        }

        th, td {
            padding: 16px 20px;
            font-size: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            font-weight: 600;
            color: var(--text-color-light);
        }
        
        tr:last-child td {
            border-bottom: none;
        }

        .status-action {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .status-text {
            font-weight: 600;
            text-transform: capitalize;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 13px;
        }

        .status-text.approved {
            color: #1e6b3b;
            background-color: #d1f7de;
        }

        .status-text.pending {
            color: #9c5400;
            background-color: #fff1e0;
        }

        .status-text.rejected {
            color: #a82222;
            background-color: #ffe6e6;
        }

        .action-btn {
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .cancel-btn {
            background-color: #d32f2f;
        }

        .cancel-btn:hover {
            background-color: #b71c1c;
        }

        .schedule-btn {
            background: var(--accent-gradient);
        }
        .schedule-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .inline-form {
            display: inline;
        }

        .empty-msg {
            text-align: center;
            color: #777;
            margin-top: 40px;
            font-size: 16px;
            padding: 30px;
            background: rgba(255,255,255,0.5);
            border-radius: var(--border-radius);
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
                <th>Pet</th>
                <th>Application Date</th>
                <th>Status</th>
                <th>Action</th>
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
                <td><?= htmlspecialchars($row['pet_name']); ?> (<?= htmlspecialchars($row['species']); ?>)</td>
                <td><?= htmlspecialchars(date("F j, Y", strtotime($row['application_date']))); ?></td>
                <td><span class="<?= $status_class ?>"><?= ucfirst($status); ?></span></td>
                <td>
                    <div class="status-action">
                        <?php if ($status === 'pending'): ?>
                            <form method="POST" action="cancel_application.php" class="inline-form" onsubmit="return confirm('Are you sure you want to cancel this application?');">
                                <input type="hidden" name="application_id" value="<?= $application_id; ?>">
                                <button type="submit" class="action-btn cancel-btn">Cancel</button>
                            </form>
                        <?php elseif ($status === 'approved' && !$hasInterview): ?>
                            <a href="schedule_interview.php?app_id=<?= $application_id; ?>" class="action-btn schedule-btn">Schedule Interview</a>
                        <?php else: ?>
                            <span>—</span>
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
