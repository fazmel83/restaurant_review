<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$id = $_GET["id"];
$uid = $_SESSION["user_id"];

$sql = "DELETE FROM reviews WHERE id=$id AND user_id=$uid";
mysqli_query($conn, $sql);

header("Location: dashboard.php");
exit;
?>
