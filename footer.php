<footer class="bg-gray-900 text-white pt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <!-- Logo + About -->
            <div>
                <h3 class="text-2xl font-bold mb-4">MuTraPro</h3>
                <p class="text-gray-400 leading-relaxed">
                    Nền tảng dịch vụ âm nhạc chuyên nghiệp hàng đầu Việt Nam.
                </p>
            </div>

            <!-- Services -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Dịch vụ</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white transition-colors">Ký âm</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Phối khí</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Thu âm</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Mixing & Mastering</a></li>
                </ul>
            </div>

            <!-- Links -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Liên kết</h4>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white transition-colors">Về chúng tôi</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Liên hệ</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Chính sách</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Điều khoản</a></li>
                </ul>
            </div>

            <!-- Social + Subscribe -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Theo dõi</h4>
                <div class="flex space-x-4 mb-6">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-facebook-f text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-instagram text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-youtube text-xl"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-tiktok text-xl"></i></a>
                </div>
                <h4 class="text-lg font-semibold mb-2">Subscribe</h4>
                <form class="flex flex-col space-y-2">
                    <label class="text-gray-400 text-sm">Đăng ký nhận bản tin của chúng tôi</label>
                    <input type="email" placeholder="Email của bạn" required class="px-3 py-2 rounded text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary">
                    <button type="submit" class="bg-primary text-white py-2 rounded hover:bg-blue-700 transition-colors">Subscribe</button>
                </form>
            </div>

        </div>

        <!-- Footer Bottom -->
        <div class="border-t border-gray-800 mt-8 pt-6 text-center text-gray-400 text-sm">
            <p>&copy; <span id="year"></span> MuTraPro. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>

    <script>
        // Cập nhật năm tự động
        document.getElementById("year").textContent = new Date().getFullYear();
    </script>
</footer>
