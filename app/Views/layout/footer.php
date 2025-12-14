</div>
<footer class="footer mt-auto py-3 bg-light border-top">
    <div class="container text-center text-muted">
        <span class="small">&copy; <?= date('Y') ?> Club Management System. All rights reserved.</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<script>
    // Auto hide alert after 5 seconds
    setTimeout(function() {
        var alertNode = document.querySelector('.alert');
        if (alertNode) {
            var alert = new bootstrap.Alert(alertNode);
            alert.close();
        }
    }, 5000);
</script>
</body>

</html>