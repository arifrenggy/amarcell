</div> <!-- End main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            if (bootstrap.Alert.getInstance(alert)) {
                bootstrap.Alert.getInstance(alert).close();
            }
        });
    }, 5000);
</script>

</body>
</html>