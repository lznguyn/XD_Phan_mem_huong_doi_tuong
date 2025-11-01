<?php
header('Content-Type: application/json');
include '../user/config.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// GET: Lấy danh sách booking
if ($method === 'GET') {
    $bookings = [];
    $res = mysqli_query($conn, "
        SELECT b.id, b.user_id, b.expert_id, b.date, b.time_slot, b.status,
               u.name AS user_name, e.name AS expert_name
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN experts e ON b.expert_id = e.id
        ORDER BY b.id DESC
    ");

    while ($row = mysqli_fetch_assoc($res)) {
        $bookings[] = $row;
    }
    echo json_encode($bookings);
    exit();
}

// POST: Xác nhận booking hoàn thành
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['booking_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'booking_id is required']);
        exit();
    }
    $booking_id = mysqli_real_escape_string($conn, $data['booking_id']);
    mysqli_query($conn, "UPDATE bookings SET status='completed' WHERE id='$booking_id'");
    echo json_encode(['success' => true, 'message' => "Đã xác nhận booking #$booking_id"]);
    exit();
}

// DELETE: Xóa booking
if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['booking_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'booking_id is required']);
        exit();
    }
    $booking_id = mysqli_real_escape_string($conn, $data['booking_id']);
    mysqli_query($conn, "DELETE FROM bookings WHERE id='$booking_id'");
    echo json_encode(['success' => true, 'message' => "Đã xóa booking #$booking_id"]);
    exit();
}

// Nếu phương thức không hợp lệ
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
