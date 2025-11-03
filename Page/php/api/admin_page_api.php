<?php
header('Content-Type: application/json');
include '../user/config.php';  // Kết nối MySQL

session_start();
$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Lấy dữ liệu thống kê
$data = [];

// Tổng tiền pending
$res = mysqli_query($conn, "SELECT SUM(total_price) AS total FROM orders WHERE payment_status='pending'");
$row = mysqli_fetch_assoc($res);
$data['total_pendings'] = (int)$row['total'];

// Tổng tiền completed
$res = mysqli_query($conn, "SELECT SUM(total_price) AS total FROM orders WHERE payment_status='completed'");
$row = mysqli_fetch_assoc($res);
$data['total_completed'] = (int)$row['total'];

// Tổng đơn hàng
$res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders");
$row = mysqli_fetch_assoc($res);
$data['orders_count'] = (int)$row['cnt'];

// Tổng dịch vụ âm nhạc
$res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM services");
$row = mysqli_fetch_assoc($res);
$data['products_count'] = (int)$row['cnt'];

// Booking pending & completed
$res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM bookings WHERE status='pending'");
$row = mysqli_fetch_assoc($res);
$data['pending_orders_count'] = (int)$row['cnt'];

$res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM bookings WHERE status='completed'");
$row = mysqli_fetch_assoc($res);
$data['completed_orders_count'] = (int)$row['cnt'];

// Experts
$res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM experts");
$row = mysqli_fetch_assoc($res);
$data['experts_count'] = (int)$row['cnt'];

// Music submissions
$res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM music_submissions WHERE status='pending'");
$row = mysqli_fetch_assoc($res);
$data['musicsub_pending_count'] = (int)$row['cnt'];

$res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM music_submissions WHERE status='completed'");
$row = mysqli_fetch_assoc($res);
$data['musicsub_completed_count'] = (int)$row['cnt'];

// Users & Admins
$res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM users WHERE user_type='user'");
$row = mysqli_fetch_assoc($res);
$data['users_count'] = (int)$row['cnt'];

$res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM users WHERE user_type='admin'");
$row = mysqli_fetch_assoc($res);
$data['admins_count'] = (int)$row['cnt'];

//nv
$res = mysqli_query($conn, "
    SELECT COUNT(*) AS cnt 
    FROM users 
    WHERE user_type IN ('arrangement', 'transcription')
");

$row = mysqli_fetch_assoc($res);
$data['staff_count'] = (int)$row['cnt'];


echo json_encode($data);
