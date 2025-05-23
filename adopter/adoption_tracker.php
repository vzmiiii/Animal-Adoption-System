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
        body {
            background-color: #fff;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .tracker-wrapper {
            max-width: 900px;
            margin: 80px auto;
            padding: 40px;
            background-color: #f7e6cf;
            border-radius: 30px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        .tracker-wrapper h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 30px;
        }

        .tracker-wrapper a {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #000;
            font-weight: 500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
            background-color: #fff;
        }

        th, td {
            padding: 14px 18px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #eee;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .empty-msg {
            text-align: center;
            font-size: 16px;
            padding: 20px;
            background-color: #fff;
            border-radius: 20px;
            margin-top: 20px;
        }

        .cancel-btn {
            background-color: #000;
            color: #fff;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            cursor: pointer;
        }

        .cancel-btn:hover {
            opacity: 0.9;
        }

        form.inline-form {
            display: inline;
        }
        .status-action {
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-text {
    font-weight: 500;
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
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['pet_name']); ?></td>
                <td><?php echo htmlspecialchars($row['species']); ?></td>
                <td><?php echo htmlspecialchars($row['application_date']); ?></td>
                <td>
    <div class="status-action">
        <span class="status-text"><?php echo ucfirst(htmlspecialchars($row['status'])); ?></span>
        <?php if ($row['status'] === 'pending'): ?>
            <form method="POST" action="cancel_application.php" class="inline-form" onsubmit="return confirm('Are you sure you want to cancel this application?');">
                <input type="hidden" name="application_id" value="<?php echo $row['id']; ?>">
                <button type="submit" class="cancel-btn">Cancel</button>
            </form>
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
