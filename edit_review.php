<?php
session_start();
if (!isset($_SESSION["user_id"])) { 
    header("Location: login.php"); 
    exit; 
}

include "db.php";

$id  = intval($_GET["id"]);
$uid = $_SESSION["user_id"];

// Fetch the review
$sql    = "SELECT * FROM reviews WHERE id=$id AND user_id=$uid";
$result = mysqli_query($conn, $sql);
$review = mysqli_fetch_assoc($result);

if (!$review) { 
    die("Unauthorized access."); 
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Review | FoodReview+</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="nav-container">
        <div class="logo"><a href="index.php"><strong>FoodReview+</strong></a></div>
        <div class="links">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

<!-- EDIT FORM CONTAINER -->
<div class="form-page-container">
    <div class="form-box">

        <h2>✏️ Edit Review</h2>

        <form method="POST" enctype="multipart/form-data">

            <!-- Restaurant -->
            <label>Restaurant Name</label>
            <input type="text" name="restaurant" 
                   value="<?= htmlspecialchars($review['restaurant']) ?>" required>

            <!-- Dish -->
            <label>Dish Name</label>
            <input type="text" name="dish" 
                   value="<?= htmlspecialchars($review['dish']) ?>" required>

            <!-- Price -->
            <label>Price (RM)</label>
            <input type="number" step="0.01" name="price"
                   value="<?= htmlspecialchars($review['price']) ?>" required>

            <!-- Category -->
            <label>Category</label>
            <select name="category" required>
                <option value="Main Dish" <?= ($review['category']=="Main Dish")?"selected":"" ?>>Main Dish</option>
                <option value="Drink" <?= ($review['category']=="Drink")?"selected":"" ?>>Drink</option>
                <option value="Dessert" <?= ($review['category']=="Dessert")?"selected":"" ?>>Dessert</option>
                <option value="Snack" <?= ($review['category']=="Snack")?"selected":"" ?>>Snack</option>
            </select>

            <!-- Rating -->
            <label>Rating</label>
            <select name="rating">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>" <?= ($i == $review['rating']) ? "selected" : "" ?>>
                        <?= $i ?> Stars
                    </option>
                <?php endfor; ?>
            </select>

            <!-- Review Text -->
            <label>Your Review</label>
            <textarea name="review_text" required><?= htmlspecialchars($review['review_text']) ?></textarea>

            <!-- CURRENT IMAGE -->
            <label>Current Image</label>
            <?php if ($review["image"]): ?>
                <img src="uploads/<?= htmlspecialchars($review['image']) ?>" 
                     class="review-img" style="max-height:200px;">
            <?php else: ?>
                <p style="color:#777;">No image uploaded.</p>
            <?php endif; ?>

            <!-- REPLACE IMAGE -->
            <label>Replace Image (optional)</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit">Save Changes</button>
        </form>

        <!-- PROCESS UPDATE -->
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $restaurant = mysqli_real_escape_string($conn, $_POST["restaurant"]);
            $dish       = mysqli_real_escape_string($conn, $_POST["dish"]);
            $price      = mysqli_real_escape_string($conn, $_POST["price"]);
            $category   = mysqli_real_escape_string($conn, $_POST["category"]);
            $rating     = intval($_POST["rating"]);
            $text       = mysqli_real_escape_string($conn, $_POST["review_text"]);
            $imgName    = $review["image"];

            // New image uploaded?
            if (!empty($_FILES["image"]["name"])) {

                $newName = time() . "_" . basename($_FILES["image"]["name"]);
                $target  = "uploads/" . $newName;

                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target)) {

                    // delete old image
                    if (!empty($review["image"]) && file_exists("uploads/" . $review["image"])) {
                        unlink("uploads/" . $review["image"]);
                    }

                    $imgName = $newName;
                }
            }

            // UPDATE QUERY
            $update = "UPDATE reviews 
                       SET restaurant='$restaurant',
                           dish='$dish',
                           price='$price',
                           category='$category', 
                           rating=$rating, 
                           review_text='$text',
                           image='$imgName'
                       WHERE id=$id AND user_id=$uid";

            if (mysqli_query($conn, $update)) {
                echo "<p class='success'>Changes saved!</p>";
            } else {
                echo "<p class='error'>Error updating review.</p>";
            }
        }
        ?>
    </div>
</div>

</body>
</html>
