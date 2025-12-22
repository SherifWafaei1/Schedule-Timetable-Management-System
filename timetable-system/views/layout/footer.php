    </main>

    <!-- Footer Content (Optional) -->
    <footer class="footer mt-auto py-3 bg-white text-center border-top">
        <div class="container">
            <span class="text-muted">&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</span>
        </div>
    </footer>

    <!-- Core Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="<?php echo APP_URL; ?>/assets/js/<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Modal Auto-open Handler -->
    <script>
        window.addEventListener('load', function() {
            const hash = window.location.hash;
            if (hash) {
                const targetModal = document.querySelector(hash + 'Modal');
                if (targetModal) {
                    const modal = new bootstrap.Modal(targetModal);
                    modal.show();
                    history.replaceState(null, null, ' ');
                }
            }
        });
    </script>
</body>
</html>
