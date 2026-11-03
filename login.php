<?php
session_start();
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $pw = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($pw, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["fullname"] = $user["fullname"];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | FoodReview+</title>
    <link rel="stylesheet" href="style.css">

    <style>
        /* Centered container */
        .form-page-container {
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #FAFAFA;
        }

        /* Login box */
        .form-box {
            width: 420px;
            background: #FFFFFF;
            padding: 35px 40px;
            border-radius: 14px;
            border: 1px solid #E6E6E6;
            box-shadow: 0px 6px 20px rgba(0,0,0,0.08);
        }

        .form-box h2 {
            color: #C8102E;
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        form label {
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
        }

        form input[type="email"],
        form input[type="password"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #CCC;
            margin-bottom: 20px;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #C8102E;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
        }

        button:hover {
            background: #A20C24;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .form-subtext {
            margin-top: 18px;
            text-align: center;
            font-size: 14px;
        }

        .form-subtext a {
            color: #C8102E;
            font-weight: bold;
        }
    </style>

</head>
<body>

<div class="form-page-container">

    <div class="form-box">

        <h2>🔑 Login</h2>

        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">

            <label>Email Address</label>
            <input type="email" name="email" placeholder="Enter your email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>
        </form>

        <p class="form-subtext">
            Don't have an account?
            <a href="register.php">Register Here</a>
        </p>

    </div>

</div>

</body>
</html>
