<?php
header('Content-Type: application/json');
include '../user/config.php';
session_start();

// Kiểm tra admin
$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $total_all_query = mysqli_query($conn, "SELECT COUNT(*) AS total_all FROM users");
    $total_all_users = mysqli_fetch_assoc($total_all_query)['total_all'];

    $total_users_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE user_type = 'user'");
    $total_users = mysqli_fetch_assoc($total_users_query)['total'];

    $total_admins_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE user_type = 'admin'");
    $total_admins = mysqli_fetch_assoc($total_admins_query)['total'];

    $total_coordinator_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE user_type = 'coordinator'");
    $total_coordinator = mysqli_fetch_assoc($total_coordinator_query)['total'];

    $total_transcription_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE user_type = 'transcription'");
    $total_transcription = mysqli_fetch_assoc($total_transcription_query)['total'];

    $total_recording_artist_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE user_type = 'recording_artists'");
    $total_recording_artist = mysqli_fetch_assoc($total_recording_artist_query)['total'];
    // Lấy danh sách người dùng
    $users = [];
    $result = mysqli_query($conn, "SELECT * FROM `users` ORDER BY id DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = [    
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'user_type' => $row['user_type']
        ];
    }
    echo json_encode([
        'stats' => [
            'total' => (int)$total_all_users, // Tổng toàn bộ người dùng
            'users' => (int)$total_users,
            'admins' => (int)$total_admins,
            'coordinator' => (int)$total_coordinator,
            'transcription' => (int)$total_transcription,
            'recording_artists' => (int)$total_recording_artist
        ],
        'users' => $users
    ]);
    exit();
}

if ($method === 'DELETE') {
    // Xóa người dùng và dữ liệu liên quan
    $input = json_decode(file_get_contents('php://input'), true);
    $delete_id = mysqli_real_escape_string($conn, $input['id'] ?? '');

    if (!$delete_id) {
        http_response_code(400);
        echo json_encode(['error' => 'User ID is required']);
        exit();
    }

    if ($delete_id == $admin_id) {
        http_response_code(403);
        echo json_encode(['error' => 'Cannot delete current admin account']);
        exit();
    }

    // Xóa giỏ hàng
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$delete_id'");
    // Xóa đơn hàng
    mysqli_query($conn, "DELETE FROM `orders` WHERE user_id = '$delete_id'");
    // Xóa nhạc
    mysqli_query($conn, "DELETE FROM `music_submissions` WHERE user_id = '$delete_id'");
    // Xóa booking
    mysqli_query($conn, "DELETE FROM `bookings` WHERE user_id = '$delete_id'");
    // Xóa user
    mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'");

    echo json_encode(['success' => true, 'message' => 'Đã xóa tài khoản và dữ liệu liên quan']);
    exit();
}

// Nếu method không hợp lệ
http_response_code(405);
echo json_encode(['error' => 'Method Not Allowed']);
