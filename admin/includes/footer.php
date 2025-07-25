    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    
    <!-- Admin specific scripts -->
    <script>
        // Auto-refresh active calls count
        function refreshStats() {
            AjaxHelper.get('../api/stats.php')
                .then(data => {
                    const callsElement = document.querySelector('.calls-count');
                    if (callsElement) {
                        callsElement.textContent = data.active_calls;
                    }
                })
                .catch(error => {
                    console.log('Stats refresh failed:', error);
                });
        }
        
        // Refresh stats every 30 seconds
        setInterval(refreshStats, 30000);
    </script>
</body>
</html>
