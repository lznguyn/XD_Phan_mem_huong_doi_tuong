<?php
include '../user/config.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    header('location:login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng - MuTraPro Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#f59e0b',
                        accent: '#10b981',
                        danger: '#dc2626',
                        admin: '#7c3aed'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">

<?php include 'admin_header.php'; ?>

<div class="min-h-screen pt-20">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="bg-primary bg-opacity-10 p-3 rounded-xl">
                    <i class="fas fa-users text-primary text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Quản lý người dùng</h1>
                    <p class="text-gray-600 mt-1">Xem và quản lý tất cả tài khoản trong hệ thống</p>
                </div>
            </div>

            <div class="hidden md:flex items-center space-x-6" id="stats-container">
                <!-- Stats sẽ được render bằng JS -->
            </div>
        </div>
    </div>

    <!-- Danh sách người dùng -->
    <div id="user-list" class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Danh sách sẽ được render bằng JS -->
    </div>
</div>

<!-- Toast thông báo -->
<script>
function showToast(message, type = "success") {
    const toast = document.createElement("div");
    toast.textContent = message;
    toast.className = `fixed bottom-6 right-6 px-4 py-3 rounded-lg text-white shadow-lg z-50 ${
        type === "success" ? "bg-green-600" : "bg-red-600"
    }`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.add("opacity-0", "transition");
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

// Fetch danh sách user + stats
function loadUsers() {
    fetch('../api/admin_user_api.php')
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('user-list');
            container.innerHTML = '';

            const statsContainer = document.getElementById('stats-container');
            statsContainer.innerHTML = `
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary">${data.stats.total}</div>
                    <div class="text-sm text-gray-600">Tổng cộng</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-accent">${data.stats.users}</div>
                    <div class="text-sm text-gray-600">Người dùng</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-admin">${data.stats.admins}</div>
                    <div class="text-sm text-gray-600">Quản trị viên</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-admin">${data.stats.coordinator}</div>
                    <div class="text-sm text-gray-600">Điều phối viên</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-admin">${data.stats.transcription}</div>
                    <div class="text-sm text-gray-600">Chuyên gia phối âm</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-admin">${data.stats.recording_artists}</div>
                    <div class="text-sm text-gray-600">Chuyên gia hòa khí</div>
                </div>
            `;

            data.users.forEach(user => {
                const isAdmin = user.user_type === 'admin';
                const isCoordinator = user.user_type === 'coordinator';
                const isTranscription = user.user_type === 'transcription';
                const isRecordingArtist = user.user_type === 'recording_artists';
                const badgeColor = isAdmin ? 'bg-admin text-white' : 'bg-accent text-white';

                const userCard = document.createElement('div');
                userCard.className = `bg-white border-2 ${isAdmin ? 'border-admin' : 'border-gray-200'} rounded-xl shadow-sm p-6 hover:shadow-lg transition-all`;

                userCard.innerHTML = `
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto">
                            <i class="fas ${isAdmin ? 'fa-user-shield' : isCoordinator ? 'fa-user-tie' : isTranscription ? 'fa-user-tie' : isRecordingArtist ? 'fa-user-tie' : 'fa-user'} text-white text-xl"></i>
                        </div>
                        <div class="${badgeColor} rounded-full mt-2 px-2 py-1 text-xs font-semibold inline-block">
                            ${isAdmin ? 'Admin' : isCoordinator ? 'Coordinator' : isTranscription ? 'Transcription' : isRecordingArtist ? 'Recording_artists' : 'User'}
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-center text-gray-900">${user.name}</h3>
                    <p class="text-gray-600 text-center text-sm break-words mb-4">${user.email}</p>
                    <div class="flex flex-col gap-2">
                        ${user.id != <?php echo $admin_id; ?> ? `
                        <button data-id="${user.id}" class="bg-red-50 hover:bg-red-100 text-red-600 py-2 rounded-lg font-medium text-center delete-btn">
                            <i class="fas fa-trash mr-2"></i>Xóa tài khoản
                        </button>` : `
                        <div class="bg-gray-50 text-gray-400 py-2 rounded-lg font-medium text-center">
                            <i class="fas fa-lock mr-2"></i>Tài khoản hiện tại
                        </div>`}
                    </div>
                `;
                container.appendChild(userCard);
            });

            // Gán sự kiện xóa
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    if(confirm('Bạn có chắc muốn xóa tài khoản này không?')) {
                        fetch('../api/admin_user_api.php', {
                            method: 'DELETE',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({id})
                        })
                        .then(res => res.json())
                        .then(resp => {
                            showToast(resp.message || resp.error, resp.success ? 'success' : 'error');
                            if(resp.success) loadUsers();
                        });
                    }
                });
            });
        });
}

document.addEventListener("DOMContentLoaded", loadUsers);
</script>

</body>
</html>
