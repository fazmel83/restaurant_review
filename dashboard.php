<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
include "db.php";
$uid = $_SESSION["user_id"];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard | FoodReview+</title>
    <link rel="stylesheet" href="style.css">

    <style>
        /* Dashboard Header */
        .dashboard-header {
            width: 1200px;
            margin: 40px auto 20px;
        }

        .dashboard-welcome {
            font-size: 28px;
            font-weight: 700;
            color: #1A1A1A;
            margin-bottom: 5px;
        }

        .dashboard-subtitle {
            color: #555;
            margin-bottom: 15px;
        }

        .add-review-btn {
            display: inline-block;
            padding: 10px 16px;
            background: #C8102E;
            color: #fff !important;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            margin-top: 10px;
        }

        .add-review-btn:hover {
            background: #a50c24;
        }

        /* Actions under card */
        .review-actions {
            margin-top: 12px;
        }

        .btn-small {
            font-size: 14px;
            text-decoration: none;
            margin-right: 12px;
        }

        .edit-btn { color: #C8102E; font-weight: 600; }
        .delete-btn { color: #000; font-weight: 600; }
        .delete-btn:hover { color: red; }

    </style>

</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="nav-container">
        <div class="logo"><a href="index.php"><strong>FoodReview+</strong></a></div>
        <div class="links">
            <a href="add_review.php">Add Review</a>
            <a href="feedback.php">Feedback</a>
            <a href="index.php">Logout</a>
        </div>
    </div>
</div>

<!-- DASHBOARD HEADER -->
<div class="dashboard-header">
    <div class="dashboard-welcome">
        Welcome, <?= htmlspecialchars($_SESSION["fullname"]) ?>!
    </div>
    <div class="dashboard-subtitle">Your personal review collection.</div>

    <a href="add_review.php" class="add-review-btn">➕ Add New Review</a>
</div>

<!-- USER REVIEWS -->
<div class="container">

<?php
$sql = "SELECT * FROM reviews WHERE user_id=$uid ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "<p class='empty-message'>You have not posted any reviews yet.</p>";
}

while ($row = mysqli_fetch_assoc($result)) {

    $stars = str_repeat("★", $row['rating']) . str_repeat("☆", 5 - $row['rating']);
?>
    <div class="review-card">

        <?php if ($row["image"]): ?>
            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="review-img">
        <?php endif; ?>

        <div class="category-tag"><?= htmlspecialchars($row['category']) ?></div>

        <div class="review-title">
            <div class="restaurant-name"><?= htmlspecialchars($row["restaurant"]) ?></div>
            <div class="dish-name"><?= htmlspecialchars($row["dish"]) ?></div>
            <div class="dish-price">RM <?= number_format($row["price"], 2) ?></div>
        </div>

        <div class="stars"><?= $stars ?></div>

        <p><?= nl2br(htmlspecialchars(substr($row["review_text"], 0, 180))) ?>...</p>

        <small>Posted on <?= $row["created_at"] ?></small>

        <div class="review-actions">
            <a href="edit_review.php?id=<?= $row['id'] ?>" class="btn-small edit-btn">✏️ Edit</a>
            <a href="delete_review.php?id=<?= $row['id'] ?>"
               class="btn-small delete-btn"
               onclick="return confirm('Delete this review?');">🗑 Delete</a>
        </div>

    </div>

<?php } ?>

</div>

<!-- FOOTER -->
<div class="footer">
    <p>© 2025 FoodReview+. All Rights Reserved.</p>
</div>

</body>
</html>
