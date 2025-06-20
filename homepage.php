<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Animal Adoption System - Home</title>

    <!-- Google Fonts for modern typography -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <!-- External CSS for consistent styling across pages -->
    <link rel="stylesheet" href="css/common.css">

    <style>
        body {
            margin: 0;
            background-color: #f8f9fa;
            font-family: 'Montserrat', 'Segoe UI', sans-serif;
            color: #222;
        }

        /* Hero Section */
        .hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 60px 80px;
            background: linear-gradient(90deg, #e3d9b5 0%, #b5c6e3 100%);
            flex-wrap: wrap;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        }
        .hero-text {
            max-width: 50%;
        }
        .hero-text h1 {
            font-size: 40px;
            margin-bottom: 18px;
            font-weight: 700;
            line-height: 1.2;
            color: #2d3a4a;
        }
        .hero-text .tagline {
            font-size: 18px;
            color: #4a5a6a;
            margin-bottom: 28px;
            font-weight: 400;
        }
        .hero-text a {
            display: inline-block;
            background: linear-gradient(90deg, #4e8cff 0%, #6ed6a5 100%);
            color: #fff;
            padding: 14px 32px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 15px;
            box-shadow: 0 2px 8px rgba(78,140,255,0.12);
            transition: background 0.2s, transform 0.2s;
        }
        .hero-text a:hover {
            background: linear-gradient(90deg, #6ed6a5 0%, #4e8cff 100%);
            transform: translateY(-2px) scale(1.04);
        }
        .hero-image img {
            max-width: 420px;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
        }

        /* Section Title */
        .section-title {
            text-align: center;
            font-size: 26px;
            background: #fff;
            display: inline-block;
            padding: 14px 38px;
            border-radius: 30px;
            margin: 48px auto 28px;
            font-weight: 700;
            color: #2d3a4a;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            letter-spacing: 1px;
        }

        /* Pet Types Grid */
        .pet-types {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px 60px;
            max-width: 1000px;
            margin: 40px auto 60px;
            padding: 0 20px;
        }
        .pet-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            transition: transform 0.25s, box-shadow 0.25s;
            cursor: pointer;
            padding: 24px 18px 18px 18px;
            min-height: 340px;
        }
        .pet-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 8px 32px rgba(78,140,255,0.13);
        }
        .pet-card .image {
            width: 160px;
            height: 200px;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            border-radius: 18px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        }
        .pet-card p {
            font-weight: 700;
            font-size: 17px;
            margin: 0 0 8px 0;
            color: #3a4a5a;
        }
        .pet-card .desc {
            font-size: 14px;
            color: #6a7a8a;
            font-weight: 400;
            margin: 0;
        }

        /* Responsive Styling */
        @media (max-width: 900px) {
            .pet-types {
                grid-template-columns: repeat(2, 1fr);
                gap: 30px;
            }
            .hero {
                flex-direction: column;
                padding: 40px 20px;
                text-align: center;
            }
            .hero-text, .hero-image {
                max-width: 100%;
            }
            .hero-image img {
                max-width: 100%;
                margin-top: 20px;
            }
        }
        @media (max-width: 600px) {
            .pet-types {
                grid-template-columns: 1fr;
            }
            .pet-card .image {
                width: 100%;
                height: 180px;
            }
            .section-title {
                font-size: 20px;
                padding: 10px 18px;
            }
        }
    </style>
</head>
<body>

<!-- Include public navbar -->
<?php include('includes/navbar_public.php'); ?>

<!-- Hero Section -->
<div class="hero">
    <div class="hero-text">
        <h1>Find your perfect<br>companion today!</h1>
        <div class="tagline">Connecting loving families with pets in need of a home.</div>
        <a href="register.php">Adopt Now</a>
    </div>
    <div class="hero-image">
        <img src="images/home/hero-dog.jpg" alt="Shelter dogs">
    </div>
</div>

<!-- Section Heading -->
<div style="text-align: center;">
    <h2 class="section-title">Types of pets available</h2>
</div>

<!-- Pet Categories Grid -->
<div class="pet-types">
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/cat.jpg');" role="img" aria-label="Cats"></div>
        <p>Cats</p>
        <div class="desc">Graceful, independent, and loving companions for any home.</div>
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/dog.jpeg');" role="img" aria-label="Dogs"></div>
        <p>Dogs</p>
        <div class="desc">Loyal, playful, and always ready for an adventure with you.</div>
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/bird.jpeg');" role="img" aria-label="Birds"></div>
        <p>Birds</p>
        <div class="desc">Colorful, intelligent, and full of cheerful songs.</div>
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/reptile.jpg');" role="img" aria-label="Reptiles"></div>
        <p>Reptiles</p>
        <div class="desc">Unique pets for those who love something a little different.</div>
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/hamster.jpg');" role="img" aria-label="Small Mammals"></div>
        <p>Small Mammals</p>
        <div class="desc">Tiny, adorable, and perfect for smaller living spaces.</div>
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/lizard.jpg');" role="img" aria-label="Exotic Animals"></div>
        <p>Exotic Animals</p>
        <div class="desc">Fascinating creatures for experienced and curious owners.</div>
    </div>
</div>

<!-- Include footer -->
<?php include('includes/footer.php'); ?>

</body>
</html>