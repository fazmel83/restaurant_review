<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
include "db.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Review | FoodReview+</title>
    <link rel="stylesheet" href="style.css">

    <style>
        /* Page layout */
        .form-page-container {
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 60px 0;
            background: #FAFAFA;
        }

        /* Form box */
        .form-box {
            width: 650px;
            background: #fff;
            padding: 35px 40px;
            border-radius: 14px;
            border: 1px solid #E6E6E6;
            box-shadow: 0px 6px 18px rgba(0,0,0,0.06);
        }

        .form-box h2 {
            font-size: 30px;
            font-weight: bold;
            color: #C8102E;
            margin-bottom: 10px;
        }

        .form-subtext {
            font-size: 15px;
            color: #555;
            margin-bottom: 25px;
        }

        /* Form elements */
        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        form input[type="text"],
        form input[type="number"],
        form select,
        form textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #CCC;
            font-size: 15px;
            margin-bottom: 18px;
        }

        textarea {
            height: 120px;
            resize: none;
        }

        form input[type="file"] {
            margin-bottom: 18px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #C8102E;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background: #A50C24;
        }

        .success {
            margin-top: 15px;
            color: green;
            font-weight: 600;
        }
    </style>

</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="nav-container">
        <div class="logo"><a href="index.php"><strong>FoodReview+</strong></a></div>
        <div class="links">
            <a href="dashboard.php">Dashboard</a>
            <a href="feedback.php">Feedback</a>
            <a href="index.php">Logout</a>
        </div>
    </div>
</div>

<!-- FORM PAGE -->
<div class="form-page-container">

    <div class="form-box">

        <h2>🍽️ Add a New Review</h2>
        <p class="form-subtext">Share your dining experience with our community.</p>

        <form method="POST" enctype="multipart/form-data">

            <label>Restaurant Name</label>
            <input type="text" name="restaurant" required>

            <label>Dish Name</label>
            <input type="text" name="dish" required>

            <label>Price (RM)</label>
            <input type="number" step="0.01" name="price" placeholder="e.g. 12.90" required>

            <label>Category</label>
            <select name="category" required>
                <option value="">Select Category</option>
                <option value="Main Dish">Main Dish</option>
                <option value="Drink">Drink</option>
                <option value="Dessert">Dessert</option>
                <option value="Snack">Snack</option>
            </select>

            <label>Rating (1–5)</label>
            <select name="rating" required>
                <option value="">Select Rating</option>
                <option value="5">★★★★★ (5)</option>
                <option value="4">★★★★☆ (4)</option>
                <option value="3">★★★☆☆ (3)</option>
                <option value="2">★★☆☆☆ (2)</option>
                <option value="1">★☆☆☆☆ (1)</option>
            </select>

            <label>Your Review</label>
            <textarea name="review_text" required></textarea>

            <label>Food Image (optional)</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit">Submit Review</button>
        </form>

        <?php
        // PROCESS SUBMISSION
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $restaurant = mysqli_real_escape_string($conn, $_POST["restaurant"]);
            $dish       = mysqli_real_escape_string($conn, $_POST["dish"]);
            $price      = mysqli_real_escape_string($conn, $_POST["price"]);
            $category   = mysqli_real_escape_string($conn, $_POST["category"]);
            $rating     = (int)$_POST["rating"];
            $text       = mysqli_real_escape_string($conn, $_POST["review_text"]);
            $uid        = $_SESSION["user_id"];
            $imgName    = NULL;

            if (!empty($_FILES["image"]["name"])) {
                $imgName = time() . "_" . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/" . $imgName);
            }

            $sql = "INSERT INTO reviews (user_id, restaurant, dish, price, category, rating, review_text, image)
                    VALUES ('$uid', '$restaurant', '$dish', '$price', '$category', '$rating', '$text', '$imgName')";

            mysqli_query($conn, $sql);

            echo "<p class='success'>Review added successfully!</p>";
        }
        ?>

    </div>
</div>

</body>
</html>
