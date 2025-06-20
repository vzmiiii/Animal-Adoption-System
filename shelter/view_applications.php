<?php
session_start();

// -----------------------------
// 1. ACCESS CONTROL
// -----------------------------
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login.php");
    exit();
}

include('../db_connection.php');

// -----------------------------
// 2. FETCH APPLICATION DATA
// -----------------------------
$shelter_id = $_SESSION['user_id'];
$sql = "
    SELECT 
        a.id,
        a.status,
        a.application_date,
        u.username AS adopter_name,
        p.name AS pet_name
    FROM adoption_applications a
    JOIN users u ON a.adopter_id = u.id
    JOIN pets p ON a.pet_id = p.id
    WHERE p.shelter_id = ?
    ORDER BY a.application_date DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $shelter_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Adoption Applications</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <style>
        body {
            background: linear-gradient(rgba(255,255,255,0.5), rgba(255,255,255,0.5)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
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
        }
        .styled-table th {
            font-weight: 600;
            color: #555;
            background-color: rgba(255,255,255,0.7);
        }
        .styled-table tr:last-child td { border-bottom: none; }
        .status {
            font-weight: 600;
            text-transform: capitalize;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 13px;
        }
        .status.approved { color: #1e6b3b; background-color: #d1f7de; }
        .status.pending { color: #9c5400; background-color: #fff1e0; }
        .status.rejected { color: #a82222; background-color: #ffe6e6; }
        .action-cell { display: flex; gap: 10px; }
        .action-btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            color: #fff;
            transition: all 0.3s;
        }
        .action-btn.approve { background-color: #28a745; }
        .action-btn.approve:hover { background-color: #218838; transform: translateY(-2px); }
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
        <div class="page-header"><h1>Review Adoption Applications</h1></div>
        <?php if ($result->num_rows > 0): ?>
            <div class="table-container">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Pet</th>
                            <th>Adopter</th>
                            <th>Application Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['pet_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['adopter_name']); ?></td>
                                <td><?php echo date("F j, Y", strtotime($row['application_date'])); ?></td>
                                <td><span class="status <?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                <td class="action-cell">
                                    <?php if ($row['status'] === 'pending'): ?>
                                        <a class="action-btn approve" href="update_application_status.php?id=<?php echo $row['id']; ?>&status=approved">Approve</a>
                                        <a class="action-btn reject" href="update_application_status.php?id=<?php echo $row['id']; ?>&status=rejected">Reject</a>
                                    <?php else: ?>
                                        <span>—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-data">No adoption applications found.</p>
        <?php endif; ?>
    </div>
    <?php include('../includes/footer.php'); ?>
</body>
</html>
