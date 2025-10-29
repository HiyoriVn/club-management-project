</div>
<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> - CLB Khoa học kỹ thuật và Robot. Phát triển bởi Hiro.</p>
</footer>

<script>
    function toggleNotificationDropdown() {
        var dropdown = document.getElementById('notificationDropdown');
        dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
    }

    // Đóng dropdown nếu click ra ngoài
    document.addEventListener('click', function(event) {
        var dropdown = document.getElementById('notificationDropdown');
        var icon = dropdown.previousElementSibling; // Lấy thẻ <a> chứa chuông
        if (dropdown && dropdown.style.display === 'block' && !dropdown.contains(event.target) && !icon.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>
</body>

</html>