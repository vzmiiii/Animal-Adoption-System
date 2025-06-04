<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include('../db_connection.php');

if (!isset($_GET['id'])) {
    exit("Missing shelter ID.");
}

$shelter_id = intval($_GET['id']);
$get_status = $conn->query("SELECT status FROM users WHERE id = $shelter_id")->fetch_assoc();
$new_status = ($get_status['status'] === 'active') ? 'deactivated' : 'active';

$stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
$stmt->bind_param("si", $new_status, $shelter_id);
$stmt->execute();

header("Location: manage_shelters.php");
exit();
