<?php
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
    <title>Quản lý Booking - MuTraPro Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<?php include 'admin_header.php'; ?>

<div class="min-h-screen pt-20">
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-calendar-check text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Quản lý Booking</h1>
                    <p class="text-gray-600 mt-1">Xem và xác nhận các buổi đặt chuyên gia</p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div id="bookingList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-gray-700">
            <div class="col-span-full text-center text-gray-500">Đang tải dữ liệu...</div>
        </div>
    </div>
</div>

<script>
async function fetchBookings() {
    const container = document.getElementById("bookingList");
    container.innerHTML = '<div class="col-span-full text-center text-gray-500">Đang tải dữ liệu...</div>';
    try {   
        const res = await fetch("../api/booking_api.php");
        const data = await res.json();

        if (data.length === 0) {
            container.innerHTML = '<div class="col-span-full text-center text-gray-500">Chưa có lịch đặt nào.</div>';
            return;
        }

        container.innerHTML = "";
        data.forEach(b => {
            const isDone = b.status === "completed";
            container.innerHTML += `
                <div class="bg-white border-2 rounded-xl shadow-sm p-6 hover:shadow-lg transition">
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="font-bold text-lg text-gray-900">Booking #${b.id}</h2>
                        <span class="text-sm px-3 py-1 rounded-full ${isDone ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}">
                            ${isDone ? 'Hoàn thành' : 'Đang chờ'}
                        </span>
                    </div>
                    <p><strong>Người đặt:</strong> ${b.user_name}</p>
                    <p><strong>Chuyên gia:</strong> ${b.expert_name}</p>
                    <p><strong>Ngày hẹn:</strong> ${b.date}</p>
                    <p><strong>Giờ hẹn:</strong> ${b.time_slot}</p>
                    <div class="mt-5 flex flex-col gap-2">
                        ${!isDone ? `
                        <button onclick="confirmBooking(${b.id})" class="bg-green-50 hover:bg-green-100 text-green-700 py-2 rounded-lg font-medium transition">
                            <i class='fas fa-check mr-2'></i>Xác nhận hoàn thành
                        </button>` : ""}
                        <button onclick="deleteBooking(${b.id})" class="bg-red-50 hover:bg-red-100 text-red-600 py-2 rounded-lg font-medium transition">
                            <i class='fas fa-trash mr-2'></i>Xóa booking
                        </button>
                    </div>
                </div>`;
        });
    } catch (err) {
        console.error(err);
        container.innerHTML = `<div class="col-span-full text-center text-red-600">Lỗi tải dữ liệu!</div>`;
    }
}

async function confirmBooking(id) {
    if (!confirm("Xác nhận buổi đặt này đã hoàn thành?")) return;
    const res = await fetch("../api/booking_api.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ booking_id: id })
    });
    const data = await res.json();
    showToast(data.message);
    fetchBookings();
}

async function deleteBooking(id) {
    if (!confirm("Bạn có chắc muốn xóa booking này không?")) return;
    const res = await fetch("../api/booking_api.php", {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ booking_id: id })
    });
    const data = await res.json();
    showToast(data.message);
    fetchBookings();
}

function showToast(message, type = "success") {
    const toast = document.createElement("div");
    toast.textContent = message;
    toast.className = `fixed bottom-6 right-6 px-4 py-3 rounded-lg text-white shadow-lg z-50 ${type === "success" ? "bg-green-600" : "bg-red-600"}`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.add("opacity-0", "transition");
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

document.addEventListener("DOMContentLoaded", fetchBookings);
</script>

</body>
</html>
