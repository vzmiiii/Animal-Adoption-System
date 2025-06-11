<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Animal Adoption System - Home</title>

    <!-- External CSS for consistent styling across pages -->
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/adopter.css">

    <style>
        /* General body styling */
        body {
            margin: 0;
            background-color: #ffffff;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Hero Section */
        .hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 60px 80px;
            background-color: #ffffff;
            flex-wrap: wrap;
        }

        .hero-text {
            max-width: 50%;
        }

        .hero-text h1 {
            font-size: 36px;
            margin-bottom: 20px;
            font-weight: bold;
            line-height: 1.3;
        }

        .hero-text a {
            display: inline-block;
            background-color: #000;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
        }

        .hero-image img {
            max-width: 400px;
            border-radius: 8px;
        }

        /* Section Title */
        .section-title {
            text-align: center;
            font-size: 22px;
            background-color: #d5d0b0;
            display: inline-block;
            padding: 10px 25px;
            border-radius: 20px;
            margin: 40px auto 20px;
            font-weight: bold;
        }

        /* Pet Types Grid */
        .pet-types {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px 60px;
            max-width: 900px;
            margin: 40px auto 60px;
            padding: 0 20px;
            text-align: center;
        }

        .pet-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: none;
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .pet-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .pet-card .image {
            width: 180px;
            height: 240px;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            border-radius: 30px;
            margin-bottom: 12px;
        }

        .pet-card p {
            font-weight: 600;
            font-size: 15px;
            margin: 0;
        }

        /* Responsive Styling */
        @media (max-width: 768px) {
            .hero {
                flex-direction: column;
                padding: 40px 20px;
                text-align: center;
            }

            .hero-text, .hero-image {
                max-width: 100%;
            }

            .hero-text h1 {
                font-size: 28px;
            }

            .hero-text a {
                font-size: 13px;
                padding: 10px 20px;
            }

            .hero-image img {
                max-width: 100%;
                margin-top: 20px;
            }

            .pet-types {
                grid-template-columns: repeat(2, 1fr);
                gap: 30px;
            }
        }

        @media (max-width: 480px) {
            .pet-types {
                grid-template-columns: 1fr;
            }

            .pet-card .image {
                width: 100%;
                height: 220px;
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
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/dog.jpeg');" role="img" aria-label="Dogs"></div>
        <p>Dogs</p>
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/bird.jpeg');" role="img" aria-label="Birds"></div>
        <p>Birds</p>
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/reptile.jpg');" role="img" aria-label="Reptiles"></div>
        <p>Reptiles</p>
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/hamster.jpg');" role="img" aria-label="Small Mammals"></div>
        <p>Small Mammals</p>
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/lizard.jpg');" role="img" aria-label="Exotic Animals"></div>
        <p>Exotic Animals</p>
    </div>
</div>

<!-- Include footer -->
<?php include('includes/footer.php'); ?>

</body>
</html>