<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Animal Adoption System - Home</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/adopter.css">

    <style>
        body {
            margin: 0;
            background-color: #ffffff;
        }

        .hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 60px 80px;
            background-color: #ffffff;
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

        .section-title {
            text-align: center;
            font-size: 22px;
            background-color: #f7e6cf;
            display: inline-block;
            padding: 10px 25px;
            border-radius: 20px;
            margin: 40px auto 20px;
            font-weight: bold;
        }

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
    box-shadow: none;
    border: none;
    outline: none;
}

.pet-card .image {
    width: 180px;
    height: 240px;
    background-position: center;
    background-size: cover;
    background-repeat: no-repeat;
    border-radius: 30px;
    margin-bottom: 12px;
    box-shadow: none;
    border: none;
    outline: none;
}


.pet-card p {
    font-weight: 600;
    font-size: 15px;
    margin: 0;
}


    </style>
</head>
<body>

<?php include('includes/navbar_public.php'); ?>

<div class="hero">
    <div class="hero-text">
        <h1>Find your perfect<br>companion today!</h1>
        <a href="browse_available_pets.php">Adopt Now</a>
    </div>
    <div class="hero-image">
        <img src="images/home/hero-dog.jpg" alt="Shelter dogs">
    </div>
</div>

<div style="text-align: center;">
    <h2 class="section-title">Types of pets available</h2>
</div>

<div class="pet-types">
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/cat.jpg');"></div>
        <p>Cats</p>
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/dog.jpeg');"></div>
        <p>Dogs</p>
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/bird.jpeg');"></div>
        <p>Birds</p>
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/reptile.jpg');"></div>
        <p>Reptiles</p>
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/hamster.jpg');"></div>
        <p>Small Mammals</p>
    </div>
    <div class="pet-card">
        <div class="image" style="background-image: url('images/home/lizard.jpg');"></div>
        <p>Exotic Animals</p>
    </div>
</div>


<?php include('includes/footer.php'); ?>

</body>
</html>
