<?php
include "db.php";
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = mysqli_real_escape_string($conn, $_POST["fullname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $pw    = $_POST["password"];
    $pw2   = $_POST["password2"];

    if ($pw !== $pw2) {
        $error = "Passwords do not match!";
    } else {
        $hash = password_hash($pw, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (fullname, email, password)
                VALUES ('$name', '$email', '$hash')";

        if (mysqli_query($conn, $sql)) {
            header("Location: login.php");
            exit;
        } else {
            $error = "Email already exists.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register | FoodReview+</title>
    <link rel="stylesheet" href="style.css">

    <style>
        /* Center container */
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
        }

        .error {
            color: red;
            font-weight: 600;
            margin-bottom: 12px;
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

        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #CCC;
            font-size: 15px;
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
            cursor: pointer;
            font-weight: 600;
        }

        button:hover {
            background: #A50C24;
        }
    </style>

</head>
<body>

<div class="form-page-container">
    <div class="form-box">

        <h2>📝 Create an Account</h2>

        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="fullname" required>

            <label>Email Address</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Confirm Password</label>
            <input type="password" name="password2" required>

            <button type="submit">Register</button>
        </form>

        <p class="form-subtext">
            Already have an account?
            <a href="login.php">Login here</a>
        </p>

    </div>
</div>

</body>
</html>
