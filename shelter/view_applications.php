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

    <!-- Existing global CSS -->
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/shelter.css">

    <!-- Page-specific minimalist styles -->
    <style>
        /* Ensure the entire page has a clean white background */
        body {
            background-color: #f5f5f5;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            color: #333;
        }

        /* Center content, constrain width, and add bottom padding */
        .page-wrapper {
            max-width: 1000px;
            margin: 40px auto 0;      /* top 40px, horizontally centered, bottom 0 */
            padding: 0 20px 40px;     /* left/right 20px, bottom 40px for breathing room */
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
        }

        /* Page heading */
        .page-wrapper h2 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
            color: #222;
        }

        /* Container for the table to allow horizontal scrolling on small screens */
        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        /* Styled, minimalist table */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px;
            background-color: #ffffff;
        }

        .styled-table th,
        .styled-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        /* Header row with light beige background */
        .styled-table th {
            background-color: #ffffff;
            color: #333;
            font-weight: 600;
        }

        /* Action buttons */
        .action-btn {
            display: inline-block;
            padding: 6px 14px;
            margin-right: 6px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        /* Approve button: subtle green */
        .action-btn.approve {
            background-color: #4caf50;
            color: #fff;
        }
        .action-btn.approve:hover {
            background-color: #45a047;
        }

        /* Reject button: subtle red */
        .action-btn.reject {
            background-color: #f44336;
            color: #fff;
        }
        .action-btn.reject:hover {
            background-color: #e53935;
        }

        /* When an action is already completed, show a muted dash */
        .action-complete {
            color: #888;
            font-weight: 600;
        }

        /* Message when no applications exist */
        .no-data {
            text-align: center;
            font-size: 18px;
            color: #555;
            margin-top: 40px;
        }

        /* A tiny spacer (if needed) before the footer */
        .bottom-spacer {
            height: 40px;
            width: 100%;
        }
    </style>
</head>
<body>

    <!-- --------------------------
         3. NAVIGATION BAR (SHELTER)
         -------------------------- -->
    <?php include('../includes/navbar_shelter.php'); ?>

    <!-- --------------------------
         4. MAIN CONTENT WRAPPER
         -------------------------- -->
    <div class="page-wrapper">

        <!-- Page Title -->
        <h2>Review Adoption Applications</h2>

        <!-- If there are any applications, show the table -->
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
                                <!-- Pet Name -->
                                <td><?php echo htmlspecialchars($row['pet_name']); ?></td>

                                <!-- Adopter Username -->
                                <td><?php echo htmlspecialchars($row['adopter_name']); ?></td>

                                <!-- Format the date as Day Month Year -->
                                <td><?php echo date("d M Y", strtotime($row['application_date'])); ?></td>

                                <!-- Application status: capitalize first letter -->
                                <td><?php echo ucfirst($row['status']); ?></td>

                                <!-- Action buttons (only if pending) -->
                                <td>
                                    <?php if ($row['status'] === 'pending'): ?>
                                        <!-- Approve link passes ID and new status -->
                                        <a 
                                            class="action-btn approve" 
                                            href="update_application_status.php?id=<?php echo $row['id']; ?>&status=approved"
                                        >
                                            Approve
                                        </a>

                                        <!-- Reject link passes ID and new status -->
                                        <a 
                                            class="action-btn reject" 
                                            href="update_application_status.php?id=<?php echo $row['id']; ?>&status=rejected"
                                        >
                                            Reject
                                        </a>
                                    <?php else: ?>
                                        <!-- Once approved or rejected, show a muted dash -->
                                        <span class="action-complete">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Message when no applications are found -->
            <p class="no-data">No adoption applications found.</p>
        <?php endif; ?>

    </div><!-- /.page-wrapper -->

    <!-- Spacer just before the footer, in case the footer is overlaying or coming up too quickly -->
    <div class="bottom-spacer"></div>

    <!-- --------------------------
         5. FOOTER
         -------------------------- -->
    <?php include('../includes/footer.php'); ?>
</body>
</html>
