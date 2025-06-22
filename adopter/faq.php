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
    <link rel="stylesheet" href="../css/sidebar.css">
    <style>
        :root {
            --text-color: #333;
            --text-color-light: #555;
            --container-bg: rgba(255, 255, 255, 0.92);
            --border-color: #e0e0e0;
            --shadow: 0 8px 25px rgba(0,0,0,0.1);
            --border-radius: 16px;
        }
        .faq-wrapper {
            max-width: 800px;
            margin: 80px auto 40px;
            padding: 40px;
            background: var(--container-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.4);
        }

        .faq-wrapper h2 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #1a1a1a;
            margin-top: 0;
            margin-bottom: 30px;
        }

        .faq {
            margin-bottom: 25px;
            background: #fff;
            padding: 20px;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
        }

        .faq h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
            font-weight: 600;
            color: var(--text-color);
        }

        .faq p {
            margin: 0;
            color: var(--text-color-light);
            line-height: 1.6;
        }
        
        h2.steps-title {
            margin-top: 50px;
        }

        ol {
            margin-top: 20px;
            padding-left: 20px;
            line-height: 1.8;
            background: #fff;
            padding: 25px 25px 25px 45px;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
        }

        ol li {
            margin-bottom: 15px;
            color: var(--text-color);
        }

        ol li:last-child {
            margin-bottom: 0;
        }

        ol li strong {
            color: #1a1a1a;
        }

        @media (max-width: 768px) {
            .faq-wrapper {
                margin: 40px 20px;
                padding: 30px;
            }
            .faq-wrapper h2 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
<?php include('../includes/navbar_adopter.php'); ?>

<div class="faq-wrapper">
    <h2>📖 Frequently Asked Questions</h2>

    <div class="faq">
        <h3>How do I adopt a pet?</h3>
        <p>Browse our available pets, submit an adoption application, and schedule an interview with the shelter.</p>
    </div>

    <div class="faq">
        <h3>Are there any fees for adopting?</h3>
        <p>Adoption fees vary by shelter, typically covering vaccination, neutering, and administrative costs.</p>
    </div>

    <div class="faq">
        <h3>Can I return a pet if it doesn't work out?</h3>
        <p>Yes, most shelters have return policies to ensure the pet's best interests are prioritized.</p>
    </div>

    <div class="faq">
        <h3>How long does the adoption process take?</h3>
        <p>The process typically takes 1–2 weeks, depending on application review and interview scheduling.</p>
    </div>

    <div class="faq">
        <h3>Can I adopt multiple pets?</h3>
        <p>Yes, but it depends on your living situation and ability to care adequately for multiple pets.</p>
    </div>

    <h2 class="steps-title">🐾 Steps to Adopt a Pet</h2>
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

