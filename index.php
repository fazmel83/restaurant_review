<?php
session_start();
include "db.php"; // ensure this sets up $conn (mysqli)

// -----------------------------
// Allowed filter values
// -----------------------------
$allowed_categories = ['Main Dish','Drink','Dessert','Snack'];
$allowed_price_ranges = [
    'all' => 'All',
    'lt10' => 'RM <10',
    '10_19' => 'RM 10-19',
    '20_29' => 'RM 20-29',
    'ge30' => 'RM >=30'
];

// -----------------------------
// Read & sanitize GET filters
// -----------------------------
$filter_category = isset($_GET['category']) ? trim($_GET['category']) : 'all';
if ($filter_category !== 'all' && !in_array($filter_category, $allowed_categories)) {
    $filter_category = 'all';
}

$filter_rating = isset($_GET['rating']) ? intval($_GET['rating']) : 0; // 0 means all
if ($filter_rating < 0 || $filter_rating > 5) $filter_rating = 0;

$filter_price = isset($_GET['price_range']) ? trim($_GET['price_range']) : 'all';
if (!array_key_exists($filter_price, $allowed_price_ranges)) $filter_price = 'all';

// Build WHERE clause array safely
$where_parts = [];
// category
if ($filter_category !== 'all') {
    $cat_escaped = mysqli_real_escape_string($conn, $filter_category);
    $where_parts[] = "category = '{$cat_escaped}'";
}
// rating
if ($filter_rating > 0) {
    $where_parts[] = "rating = " . intval($filter_rating);
}
// price ranges
switch ($filter_price) {
    case 'lt10':
        $where_parts[] = "price < 10";
        break;
    case '10_19':
        $where_parts[] = "price >= 10 AND price < 20";
        break;
    case '20_29':
        $where_parts[] = "price >= 20 AND price < 30";
        break;
    case 'ge30':
        $where_parts[] = "price >= 30";
        break;
}
$where_sql = count($where_parts) ? "WHERE " . implode(" AND ", $where_parts) : "";

// -----------------------------
// STATISTICS (filtered)
// -----------------------------
$stat_sql = "SELECT category, COUNT(*) AS total FROM reviews $where_sql GROUP BY category";
$stat_result = mysqli_query($conn, $stat_sql);

$total_reviews = 0;
$main_dish = $drink = $dessert = $snack = 0;
if ($stat_result) {
    while ($r = mysqli_fetch_assoc($stat_result)) {
        $cnt = intval($r['total']);
        $cat = $r['category'];
        $total_reviews += $cnt;
        if ($cat === 'Main Dish') $main_dish = $cnt;
        if ($cat === 'Drink') $drink = $cnt;
        if ($cat === 'Dessert') $dessert = $cnt;
        if ($cat === 'Snack') $snack = $cnt;
    }
}

/* Rating summary (filtered) */
$rating_sql = "
    SELECT
        COUNT(*) AS total_reviews,
        AVG(rating) AS avg_rating,
        SUM(CASE WHEN rating=5 THEN 1 ELSE 0 END) AS r5,
        SUM(CASE WHEN rating=4 THEN 1 ELSE 0 END) AS r4,
        SUM(CASE WHEN rating=3 THEN 1 ELSE 0 END) AS r3,
        SUM(CASE WHEN rating=2 THEN 1 ELSE 0 END) AS r2,
        SUM(CASE WHEN rating=1 THEN 1 ELSE 0 END) AS r1
    FROM reviews
    $where_sql
";
$rating_result = mysqli_query($conn, $rating_sql);
$rating_row = $rating_result ? mysqli_fetch_assoc($rating_result) : null;

$rating_total = $rating_row ? intval($rating_row['total_reviews']) : 0;
$avg_rating = $rating_row && $rating_row['avg_rating'] !== null ? round(floatval($rating_row['avg_rating']), 2) : 0.00;
$r5 = $rating_row ? intval($rating_row['r5']) : 0;
$r4 = $rating_row ? intval($rating_row['r4']) : 0;
$r3 = $rating_row ? intval($rating_row['r3']) : 0;
$r2 = $rating_row ? intval($rating_row['r2']) : 0;
$r1 = $rating_row ? intval($rating_row['r1']) : 0;

function pct($count, $total) {
    if ($total <= 0) return '0%';
    return round(($count / $total) * 100, 1) . '%';
}

/* Price analysis (filtered) */
$price_sql = "SELECT AVG(price) AS avg_price, MIN(price) AS min_price, MAX(price) AS max_price FROM reviews $where_sql";
$price_res = mysqli_query($conn, $price_sql);
$price_row = $price_res ? mysqli_fetch_assoc($price_res) : null;
$avg_price = $price_row && $price_row['avg_price'] !== null ? floatval($price_row['avg_price']) : 0.00;
$min_price = $price_row && $price_row['min_price'] !== null ? floatval($price_row['min_price']) : 0.00;
$max_price = $price_row && $price_row['max_price'] !== null ? floatval($price_row['max_price']) : 0.00;

$range_sql = "
    SELECT
        SUM(CASE WHEN price < 10 THEN 1 ELSE 0 END) AS r_lt10,
        SUM(CASE WHEN price >=10 AND price <20 THEN 1 ELSE 0 END) AS r_10_19,
        SUM(CASE WHEN price >=20 AND price <30 THEN 1 ELSE 0 END) AS r_20_29,
        SUM(CASE WHEN price >=30 THEN 1 ELSE 0 END) AS r_ge30
    FROM reviews
    $where_sql
";
$range_res = mysqli_query($conn, $range_sql);
$ranges = $range_res ? mysqli_fetch_assoc($range_res) : null;
$range_counts = [
    'RM <10' => $ranges ? intval($ranges['r_lt10']) : 0,
    'RM 10-19' => $ranges ? intval($ranges['r_10_19']) : 0,
    'RM 20-29' => $ranges ? intval($ranges['r_20_29']) : 0,
    'RM >=30' => $ranges ? intval($ranges['r_ge30']) : 0
];
arsort($range_counts);
$popular_budget_range = key($range_counts);

// Average by category (filtered)
$price_cat_sql = "SELECT category, AVG(price) AS avg_price, COUNT(*) AS cnt FROM reviews $where_sql GROUP BY category";
$price_cat_res = mysqli_query($conn, $price_cat_sql);
$price_by_category = [];
if ($price_cat_res) {
    while ($pc = mysqli_fetch_assoc($price_cat_res)) {
        $price_by_category[] = $pc;
    }
}

// -----------------------------
// Fetch reviews (filtered)
// -----------------------------
$reviews_sql = "
    SELECT reviews.*, users.fullname
    FROM reviews
    LEFT JOIN users ON reviews.user_id = users.id
    $where_sql
    ORDER BY reviews.id DESC
    LIMIT 12
";
$reviews_res = mysqli_query($conn, $reviews_sql);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>FoodReview+ | Discover Restaurants</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        .filters { width:1200px; margin: 18px auto; display:flex; gap:12px; align-items:center; }
        .filters select, .filters button { padding:10px 12px; border-radius:8px; border:1px solid #DDD; background:#FFF; }
        .filters button { background:#C8102E; color:#fff; border:0; cursor:pointer; }
        .filters label { font-weight:600; margin-right:6px; }
        .filters .spacer { flex:1; }
        @media (max-width:1220px){
            .filters, .stats-grid, .container { width: calc(100% - 40px); margin: 18px 20px; }
            .nav-container { width: calc(100% - 40px); }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="nav-container">

        <!-- Logo -->
        <div class="logo">
            <a href="index.php"><strong>FoodReview+</strong></a>
        </div>

        <!-- Navigation Links -->
        <div class="links">
            <a href="about.php">About</a>

            <?php if (!isset($_SESSION["user_id"])): ?>
                <a href="login.php" class="btn-login">Login</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Login</a>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- HERO -->
<div class="hero">
    <div class="hero-text">
        <h1>Discover the Best Food</h1>
        <p>Honest Reviews • Real Foodies • Top Picks Just for You</p>
    </div>
</div>

<!-- FILTER BAR -->
<form method="GET" class="filters" id="filterForm">
    <div>
        <label for="category">Category</label><br>
        <select name="category" id="category" onchange="document.getElementById('filterForm').submit();">
            <option value="all" <?= $filter_category === 'all' ? 'selected' : '' ?>>All</option>
            <?php foreach ($allowed_categories as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>" <?= $filter_category === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="rating">Rating</label><br>
        <select name="rating" id="rating" onchange="document.getElementById('filterForm').submit();">
            <option value="0" <?= $filter_rating === 0 ? 'selected' : '' ?>>All</option>
            <?php for ($i=5; $i>=1; $i--): ?>
                <option value="<?= $i ?>" <?= $filter_rating === $i ? 'selected' : '' ?>><?= $i ?> ★</option>
            <?php endfor; ?>
        </select>
    </div>

    <div>
        <label for="price_range">Price</label><br>
        <select name="price_range" id="price_range" onchange="document.getElementById('filterForm').submit();">
            <?php foreach ($allowed_price_ranges as $k=>$label): ?>
                <option value="<?= $k ?>" <?= $filter_price === $k ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="spacer"></div>

    <div>
       <a href="index.php" class="btn-reset">Reset</a>
    </div>
</form>

<!-- ============================
     STATISTICS — 3 CARDS
=============================== -->
<div class="stats-grid">

    <div class="stats-box">
        <h2>Platform Statistics</h2>
        <p><strong>Total Reviews:</strong> <?= intval($total_reviews) ?></p>
        <p><strong>Main Dishes:</strong> <?= intval($main_dish) ?></p>
        <p><strong>Drinks:</strong> <?= intval($drink) ?></p>
        <p><strong>Desserts:</strong> <?= intval($dessert) ?></p>
        <p><strong>Snacks:</strong> <?= intval($snack) ?></p>
        <!-- ⭐ Most Popular REMOVED COMPLETELY -->
    </div>

    <div class="stats-box">
        <h2>Rating Summary</h2>
        <p><strong>Average Rating:</strong> <?= number_format($avg_rating, 2) ?> ★</p>
        <p><strong>Total reviews:</strong> <?= $rating_total ?></p>

        <div style="margin-top:12px;">
            <strong>Rating breakdown</strong>
            <div style="margin-top:10px;">
                <div>5★ — <?= pct($r5, $rating_total) ?> (<?= $r5 ?>)</div>
                <div>4★ — <?= pct($r4, $rating_total) ?> (<?= $r4 ?>)</div>
                <div>3★ — <?= pct($r3, $rating_total) ?> (<?= $r3 ?>)</div>
                <div>2★ — <?= pct($r2, $rating_total) ?> (<?= $r2 ?>)</div>
                <div>1★ — <?= pct($r1, $rating_total) ?> (<?= $r1 ?>)</div>
            </div>
        </div>
    </div>

    <div class="stats-box">
        <h2>Price Analysis</h2>
        <p><strong>Average price (per dish):</strong> RM <?= number_format($avg_price, 2) ?></p>
        <p><strong>Observed range:</strong> RM <?= number_format($min_price,2) ?> — RM <?= number_format($max_price,2) ?></p>
        <p><strong>Popular budget range:</strong> <?= htmlspecialchars($popular_budget_range) ?> (<?= $range_counts[$popular_budget_range] ?>)</p>

        <div style="margin-top:12px;">
            <strong>Menu price comparison (avg by category)</strong>
            <ul style="margin-top:8px; padding-left:18px;">
                <?php if (count($price_by_category) === 0): ?>
                    <li style="color:#777;">No pricing data in this filter.</li>
                <?php else: ?>
                    <?php foreach ($price_by_category as $pc): ?>
                        <li><?= htmlspecialchars($pc['category']) ?> — RM <?= number_format((float)$pc['avg_price'],2) ?> (<?= intval($pc['cnt']) ?>)</li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

</div>

<!-- ============================
     LATEST REVIEWS (filtered)
=============================== -->

<div class="section">
    <h2>Latest Reviews</h2>

    <div class="container">
        <?php
        if (!$reviews_res || mysqli_num_rows($reviews_res) == 0) {
            echo "<p class='empty-message'>No reviews found for the selected filters.</p>";
        } else {
            while ($row = mysqli_fetch_assoc($reviews_res)):
                $stars = str_repeat("★", intval($row['rating'])) . str_repeat("☆", 5 - intval($row['rating']));
                $img_html = $row['image'] ? "<img src='uploads/".htmlspecialchars($row['image'])."' class='review-img' alt=''>" : "";
                $price_display = isset($row['price']) ? 'RM '.number_format((float)$row['price'],2) : 'RM 0.00';
        ?>
        <div class="review-card">
            <?= $img_html ?>
            <div class="category-tag"><?= htmlspecialchars($row['category']) ?></div>

            <div class="review-title">
                <div class="restaurant-name"><?= htmlspecialchars($row['restaurant']) ?></div>
                <div class="dish-name"><?= htmlspecialchars($row['dish']) ?></div>
                <div class="dish-price"><?= $price_display ?></div>
            </div>

            <div class="stars"><?= $stars ?></div>

            <p><?= nl2br(htmlspecialchars(substr($row['review_text'],0,140))) ?>...</p>

            <small>By <?= htmlspecialchars($row['fullname'] ?? 'Unknown') ?></small>
        </div>
        <?php endwhile; } ?>
    </div>
</div>

<!-- FOOTER -->
<div class="footer"><p>© 2025 FoodReview+. All Rights Reserved.</p></div>

</body>
</html>
