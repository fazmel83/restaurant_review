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
    <title>Feedback | FoodReview+</title>
    <link rel="stylesheet" href="style.css">

    <style>
        /* Centered form container */
        .form-page-container {
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 60px 0;
            background: #FAFAFA;
        }

        /* Form box */
        .form-box {
            width: 600px;
            background: #FFFFFF;
            padding: 35px 40px;
            border-radius: 14px;
            border: 1px solid #E6E6E6;
            box-shadow: 0px 6px 18px rgba(0,0,0,0.06);
        }

        .form-box h2 {
            font-size: 30px;
            font-weight: bold;
            color: #C8102E;
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        form label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }

        form textarea {
            width: 100%;
            height: 130px;
            border: 1px solid #CCC;
            border-radius: 8px;
            padding: 12px;
            font-size: 15px;
            resize: none;
            margin-bottom: 20px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #C8102E;
            color: #fff;
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
            <a href="index.php">Logout</a>
        </div>
    </div>
</div>

<!-- FEEDBACK PAGE -->
<div class="form-page-container">
    <div class="form-box">
        <h2>💬 Send Feedback</h2>

        <form method="POST">
            <label>Your Message</label>
            <textarea name="message" required></textarea>

            <button type="submit">Send Feedback</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $msg = mysqli_real_escape_string($conn, $_POST["message"]);
            $uid = $_SESSION["user_id"];

            $sql = "INSERT INTO feedback (user_id, message) VALUES ($uid, '$msg')";
            mysqli_query($conn, $sql);

            echo "<p class='success'>Message sent!</p>";
        }
        ?>
    </div>
</div>

</body>
</html>
