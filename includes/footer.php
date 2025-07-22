    </main>
    
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?= SITE_NAME ?></h3>
                    <p><?= SITE_TAGLINE ?></p>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Latest Articles</a></li>
                        <?php if (defined('ENABLE_NEWSLETTER') && ENABLE_NEWSLETTER): ?>
                            <li><a href="newsletter.php">Newsletter</a></li>
                        <?php endif; ?>
                        <?php if (defined('ENABLE_COMMUNITY') && ENABLE_COMMUNITY): ?>
                            <li><a href="community.php">Community</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="about.php">About</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms of Service</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Connect</h4>
                    <div class="social-links">
                        <!-- Social media links will be added when available -->
                        <p>Building the future of fatherhood support</p>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
                <p>Made with ❤️ for dads everywhere</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="js/app.js"></script>
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Service Worker Registration (PWA) -->
    <?php if (defined('ENABLE_PWA') && ENABLE_PWA): ?>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('service-worker.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>
    <?php endif; ?>
</body>
</html>