<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'adopter') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Frequently Asked Questions</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/adopter.css">
    <style>
        .faq-wrapper {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .faq-wrapper h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .faq {
            margin-bottom: 20px;
        }

        .faq h3 {
            margin-bottom: 5px;
        }

        .faq p {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <?php include('../includes/navbar_adopter.php'); ?>
<div class="faq-wrapper">
    <h2>Frequently Asked Questions</h2>

    <div class="faq">
        <h3>How do I adopt a pet?</h3>
        <p>Browse our available pets, submit an adoption application, and schedule an interview with the shelter.</p>
    </div>

    <div class="faq">
        <h3>Are there any fees for adopting?</h3>
        <p>Adoption fees vary by shelter, typically covering vaccination, neutering, and administrative costs.</p>
    </div>

    <div class="faq">
        <h3>Can I return a pet if it doesn’t work out?</h3>
        <p>Yes, most shelters have return policies to ensure the pet's best interests are prioritized.</p>
    </div>

    <div class="faq">
        <h3>How long does the adoption process take?</h3>
        <p>The process typically takes 1-2 weeks, depending on application review and interview scheduling.</p>
    </div>

    <div class="faq">
        <h3>Can I adopt multiple pets?</h3>
        <p>Yes, but it depends on your living situation and ability to care adequately for multiple pets.</p>
    </div>

    <h2>Steps to Adopt a Pet</h2>
    <ol>
        <li><strong>Find a Pet:</strong> Use our filters to find pets that match your preferences.</li>
        <li><strong>Apply:</strong> Fill out the adoption application form online.</li>
        <li><strong>Interview:</strong> Schedule and complete an interview with the shelter.</li>
        <li><strong>Finalize Adoption:</strong> If approved, complete the adoption paperwork and arrange pickup or delivery.</li>
    </ol>
</div>
<?php include('../includes/footer.php'); ?>
</body>
</html>
