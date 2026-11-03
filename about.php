<?php
session_start();
include "db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>About — FoodReview+</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <style>
        /* small page-specific tweaks (keeps main look consistent) */
        .about-hero {
            width: 100%;
            height: 260px;
            background: url('images/slide1.png') center/cover no-repeat;
            position: relative;
            margin-bottom: 30px;
        }
        .about-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.38);
        }
        .about-hero .hero-text {
            position: absolute;
            left: 80px;
            bottom: 36px;
            z-index: 2;
            color: #fff;
        }
        .about-container {
            width: 1100px;
            margin: 30px auto 80px;
        }
        .about-card {
            background: #fff;
            padding: 26px;
            border-radius: 12px;
            border: 1px solid #E6E6E6;
            box-shadow: 0 8px 20px rgba(0,0,0,0.03);
            margin-bottom: 22px;
        }
        .about-card h3 { color: #C8102E; margin-top:0; }
        .feature-list { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .feature-item { padding: 10px; border-radius: 8px; background:#fcfcfc; border:1px solid #f0f0f0; }
        .contact-row { display:flex; gap:20px; flex-wrap:wrap; }
        .muted { color:#666; }
        @media (max-width: 900px) {
            .about-container { width: calc(100% - 36px); margin: 20px; }
            .feature-list { grid-template-columns: 1fr; }
            .about-hero .hero-text { left: 24px; bottom: 24px; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="nav-container">
        <div class="logo"><a href="index.php"><strong>FoodReview+</strong></a></div>

        <div class="links">
            <?php if (!isset($_SESSION["user_id"])): ?>
                 <a href="index.php">Homepage</a>
            <?php else: ?>
                <a href="index.php">Homepage</a>
            <?php endif; ?>
            <a href="login.php" class="btn-login">Login</a>
        </div>
    </div>
</div>

<!-- HERO -->
<div class="about-hero">
    <div class="hero-text">
        <h1 style="margin:0; font-size:36px;">About FoodReview+</h1>
        <p style="margin-top:8px; font-size:15px; max-width:600px;">
            A clean, community-driven place for honest food reviews discover where to eat with confidence.
        </p>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="about-container">

    <div class="about-card">
        <h3>Website Overview</h3>
        <p>
            <strong>FoodReview+</strong> is a web application built to centralize and simplify food reviews.
            It helps people share real dining experiences with useful details restaurant name, dish, price,
            category, rating and photos so other food lovers can make better choices.
        </p>
    </div>

    <div class="about-card">
        <h3>Key Features</h3>

        <div class="feature-list">
            <div class="feature-item">
                <strong>User Accounts</strong><br>
                Register, login and manage your own reviews securely.
            </div>
            <div class="feature-item">
                <strong>Rich Reviews</strong><br>
                Add restaurant & dish names, price, category, star rating, long text and photo uploads.
            </div>
            <div class="feature-item">
                <strong>Edit & Delete</strong><br>
                Change or remove your reviews; images are updated cleanly.
            </div>
            <div class="feature-item">
                <strong>Filtering</strong><br>
                Browse reviews by category, rating and price range to find relevant results fast.
            </div>
            <div class="feature-item">
                <strong>Statistics</strong><br>
                Live summaries: average rating, rating breakdown, popular categories and price analysis.
            </div>
            <div class="feature-item">
                <strong>Responsive Design</strong><br>
                Clean Michelin-inspired aesthetic with readable cards and accessible layout.
            </div>
        </div>
    </div>

    <div class="about-card">
        <h3>How It Helps Users</h3>
        <ul>
            <li>Quickly compare average prices and ratings before trying a dish.</li>
            <li>See real food photos shared by fellow diners.</li>
            <li>Filter results to match your budget and taste.</li>
            <li>Contribute and build a local food knowledge base.</li>
        </ul>
    </div>

  
    <div class="about-card">
        <h3>Contact & Credits</h3>
        <div class="contact-row">
            <div>
                <strong>Project:</strong> FoodReview+<br>
                <strong>Author:</strong> <?= isset($_SESSION["fullname"]) ? htmlspecialchars($_SESSION["fullname"]) : "Your Name" ?><br>
            </div>

            <div>
                <strong>Contact:</strong><br>
                <span class="muted">FoodReviewPlus@gmail.com</span><br>
            </div>
        </div>
    </div>

    <div style="text-align:center; margin-top:10px;">
        <a href="index.php" class="btn-reset">← Back to Home</a>
    </div>

</div>

<!-- FOOTER -->
<div class="footer">
    <p>© 2025 FoodReview+. All Rights Reserved.</p>
</div>

</body>
</html>
