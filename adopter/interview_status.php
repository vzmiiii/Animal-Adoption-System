<?php
session_start();

// Redirect if not logged in or not an adopter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

$adopter_id = $_SESSION['user_id'];

$sql = "SELECT i.*, p.name AS pet_name, u.username AS shelter_name
        FROM interviews i
        JOIN pets p ON i.pet_id = p.id
        JOIN users u ON i.shelter_id = u.id
        WHERE i.adopter_id = ?
        ORDER BY i.interview_datetime ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $adopter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Interview Status</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .interview-wrapper {
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

        .status-text {
            display: inline-block;
            font-weight: 600;
            text-transform: capitalize;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 13px;
        }

        .status-text.confirmed {
            color: #1e6b3b;
            background-color: #d1f7de;
        }

        .status-text.pending {
            color: #9c5400;
            background-color: #fff1e0;
        }
        
        .status-text.completed {
            color: #0d47a1;
            background-color: #e3f2fd;
        }
        
        .status-text.cancelled {
            color: #a82222;
            background-color: #ffe6e6;
        }

        .empty-msg {
            text-align: center;
            color: var(--text-color-light);
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

<div class="interview-wrapper">
    <h2>📅 My Interview Status</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Pet</th>
                <th>Shelter</th>
                <th>Date & Time</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()):
                $status = strtolower($row['status']);
                $status_class = "status-text " . $status;
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['pet_name']) ?></td>
                    <td><?= htmlspecialchars($row['shelter_name']) ?></td>
                    <td><?= date("F j, Y, g:i A", strtotime($row['interview_datetime'])) ?></td>
                    <td><span class="<?= $status_class ?>"><?= ucfirst($status) ?></span></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <div class="empty-msg">You do not have any scheduled interviews.</div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
