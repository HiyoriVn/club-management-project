</div>
</main>
<footer class="bg-gray-100 text-center text-sm text-gray-500 py-4">
    <p>&copy; <?php echo date('Y'); ?> - Club Management System - </p>
</footer>

</div>
</main>

</div>

</div>

<div id="toast-container" class="fixed top-20 right-5 z-50 space-y-3 w-full max-w-sm">

    <div id="toast-template" class="hidden transform transition-all duration-300 ease-in-out">
        <div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <ion-icon id="toast-icon" name="checkmark-circle" class="h-6 w-6"></ion-icon>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p id="toast-message" class="text-sm font-medium text-gray-900">
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button type="button" id="toast-close" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <ion-icon name="close" class="h-5 w-5"></ion-icon>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showToast(message, type = 'info') {
        const container = document.getElementById('toast-container');
        const template = document.getElementById('toast-template');

        // 1. Sao chép (clone) cái template
        const newToast = template.cloneNode(true);
        newToast.id = ''; // Xóa ID để tránh trùng lặp

        // 2. Lấy các phần tử con
        const icon = newToast.querySelector('#toast-icon');
        const text = newToast.querySelector('#toast-message');
        const closeButton = newToast.querySelector('#toast-close');

        // Xóa ID của các phần tử con
        icon.id = '';
        text.id = '';
        closeButton.id = '';

        // 3. Cập nhật nội dung
        text.textContent = message;

        // 4. Cấu hình màu sắc theo 'type'
        // Xóa các class màu mặc định (nếu có)
        icon.classList.remove('text-green-500', 'text-red-500', 'text-blue-500');

        if (type === 'success') {
            icon.setAttribute('name', 'checkmark-circle');
            icon.classList.add('text-green-500'); // Màu xanh (Success)
        } else if (type === 'error') {
            icon.setAttribute('name', 'alert-circle');
            icon.classList.add('text-red-500'); // Màu đỏ (Error/Fail)
        } else { // 'info' hoặc mặc định
            icon.setAttribute('name', 'information-circle');
            icon.classList.add('text-blue-500'); // Màu xanh dương (Info)
        }

        // 5. Thêm hiệu ứng "trượt vào"
        // Bắt đầu: Ẩn và dịch lên trên
        newToast.classList.add('opacity-0', '-translate-y-12');
        newToast.classList.remove('hidden'); // Hiển thị nó

        // 6. Thêm toast vào container
        container.appendChild(newToast);

        // 7. Kích hoạt animation (chờ 1 chút để DOM kịp update)
        setTimeout(() => {
            // Kết thúc: Hiện rõ và về vị trí 0
            newToast.classList.remove('opacity-0', '-translate-y-12');
            newToast.classList.add('opacity-100', 'translate-y-0');
        }, 100); // 100ms

        // 8. Gán sự kiện cho nút Close
        closeButton.addEventListener('click', () => {
            hideToast(newToast);
        });

        // 9. Tự động ẩn sau 4 giây
        setTimeout(() => {
            hideToast(newToast);
        }, 4000); // 4 giây
    }

    function hideToast(toastElement) {
        // Kích hoạt animation "trượt ra"
        toastElement.classList.remove('opacity-100', 'translate-y-0');
        toastElement.classList.add('opacity-0', '-translate-y-12');

        // Xóa hẳn element khỏi DOM sau khi animation kết thúc (300ms)
        setTimeout(() => {
            if (toastElement.parentNode) {
                toastElement.remove();
            }
        }, 350);
    }
</script>

</body>

</html>
</body>

</html>