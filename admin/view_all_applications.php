<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

$sql = "SELECT a.id, a.status, a.application_date,
               u.username AS adopter_name,
               p.name AS pet_name,
               s.username AS shelter_name
        FROM adoption_applications a
        JOIN users u ON a.adopter_id = u.id
        JOIN pets p ON a.pet_id = p.id
        JOIN users s ON p.shelter_id = s.id
        ORDER BY a.application_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Adoption Applications</title>
    <link rel="stylesheet" href="../css/common.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(255,255,255,0.2), rgba(255,255,255,0.2)),
                        url('../images/PetsBackground2.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }

        .page-wrapper {
            padding: 40px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .content-container {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            margin-top: 20px;
        }

        h1 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 40px;
            color: #2c3e50;
            font-size: 2.8em;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        th, td {
            padding: 18px 15px;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        th {
            background: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            color: white;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:last-of-type td {
            border-bottom: none;
        }

        tr:hover {
            background: rgba(110, 214, 165, 0.1);
        }

        .status-approved, .status-rejected, .status-pending {
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 20px;
            display: inline-block;
            font-size: 14px;
        }

        .status-approved { 
            color: #207d3a; 
            background: rgba(40, 167, 69, 0.15);
        }
        .status-rejected { 
            color: #a72836;
            background: rgba(220, 53, 69, 0.15);
        }
        .status-pending { 
            color: #b98b00;
            background: rgba(255, 193, 7, 0.15);
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_admin.php'); ?>

<div class="page-wrapper">
    <div class="content-container">
        <h1>All Adoption Applications</h1>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Adopter</th>
                    <th>Pet</th>
                    <th>Shelter</th>
                    <th>Status</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['adopter_name']) ?></td>
                    <td><?= htmlspecialchars($row['pet_name']) ?></td>
                    <td><?= htmlspecialchars($row['shelter_name']) ?></td>
                    <td><span class="status-<?= strtolower(htmlspecialchars($row['status'])) ?>"><?= ucfirst(htmlspecialchars($row['status'])) ?></span></td>
                    <td><?= date('Y-m-d', strtotime($row['application_date'])) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
