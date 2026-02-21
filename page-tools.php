<?php
/**
 * Template Name: App Tools
 */
if ( !is_user_logged_in() ) { wp_redirect(wp_login_url(get_permalink())); exit; }

// Define Apps List
$apps = ['launcher', 'chat', 'grocery', 'cards', 'coupons', 'calendar', 'qr', 'crypto', 'converter', 'modals'];

// Enqueue App Styles with Cache Busting using time()
foreach($apps as $app) {
    wp_enqueue_style("lollms-app-$app", get_theme_file_uri("apps/$app/style.css"), [], time());
}

get_header(); 
?>

<script>
    // --- KERNEL STATE ---
    const apiRoot = "<?php echo esc_url_raw(rest_url('lollms/v1/')); ?>";
    const nonce = "<?php echo wp_create_nonce('wp_rest'); ?>";
    
    // Global State
    let currentApp = 'launcher';
    
    // Shared Resources (initialized by apps)
    let html5QrcodeScanner = null;
    let calendarInstance = null;
    let mapInstance = null;
    let editorImageBase64 = null;
    let qrScannedContent = null;
    
    // Shared Data
    let fetchedRates = { BTC: 0, ETH: 0 }; 
    let fiatRates = { USD: 1, EUR: 0.92, GBP: 0.79, TND: 3.10, JPY: 148, CAD: 1.35, AUD: 1.52, CHF: 0.88, CNY: 7.19 };
</script>

<main class="nexus-container">
    <div class="app-container">
        <!-- APP HEADER (Shared) -->
        <div id="app-nav" class="app-header" style="display:none;">
            <button class="btn-home" onclick="goHome()">⌂</button>
            <h2 id="app-title" style="margin:0; font-size:1.1rem; font-weight:600;">App</h2>
            <button id="btn-header-add" class="btn-header-add" onclick="openEditor('generic')">+</button>
        </div>

        <?php
            // Load Applications
            foreach($apps as $app) {
                // Include the view/logic
                $file = get_theme_file_path("apps/$app/index.php");
                if (file_exists($file)) {
                    include $file;
                }
            }
        ?>

    </div>
</main>

<?php get_footer(); ?>
