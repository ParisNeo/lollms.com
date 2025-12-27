<?php
/**
 * LoLLMs Nexus Engine v3.9 (Apps Ecosystem)
 */

if (!defined('ABSPATH')) exit;

// --- 1. SETUP & DB INSTALLER ---
function lollms_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    
    $current_db_ver = '4.0'; 
    if (get_option('lollms_db_version') != $current_db_ver) {
        lollms_install_tables();
        update_option('lollms_db_version', $current_db_ver);
    }
}
add_action('after_setup_theme', 'lollms_theme_setup');

function lollms_install_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql1 = "CREATE TABLE {$wpdb->prefix}lollms_items (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        item_name varchar(255) NOT NULL,
        description text DEFAULT NULL,
        location varchar(255) DEFAULT NULL,
        attendees text DEFAULT NULL,
        quantity varchar(50) DEFAULT '',
        price decimal(10,2) DEFAULT 0.00,
        barcode varchar(100) DEFAULT NULL,
        image_data longtext DEFAULT NULL, 
        due_date datetime DEFAULT NULL,
        end_date datetime DEFAULT NULL,
        category varchar(50) DEFAULT 'general',
        is_checked boolean DEFAULT 0,
        buy_count int(11) DEFAULT 1,
        last_bought datetime DEFAULT CURRENT_TIMESTAMP,
        list_type varchar(100) DEFAULT 'grocery',
        PRIMARY KEY  (id),
        INDEX barcode_idx (barcode),
        INDEX list_idx (list_type)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql1);
}

// --- 2. ASSETS ---
function lollms_enqueue_assets() {
    wp_enqueue_style('lollms-main', get_stylesheet_uri(), [], time());
    if (is_page_template('page-tools.php')) {
        // Scanner & Codes
        wp_enqueue_script('html5-qrcode', 'https://unpkg.com/html5-qrcode', [], null, true);
        wp_enqueue_script('jsbarcode', 'https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js', [], null, true);
        wp_enqueue_script('qrcodejs', 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js', [], null, true);

        // Calendar / Maps
        wp_enqueue_script('fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js', [], null, true);
        wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
        wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], null, true);

        // Crypto Libraries
        // BitcoinJS (Browser Bundle)
        wp_enqueue_script('bitcoinjs', 'https://cdn.jsdelivr.net/gh/bitcoinjs/bitcoinjs-lib@5.2.0/dist/bitcoinjs-lib.min.js', [], null, true);
        // Ethers.js
        wp_enqueue_script('ethers', 'https://cdnjs.cloudflare.com/ajax/libs/ethers/6.7.0/ethers.umd.min.js', [], null, true);
    }
}
add_action('wp_enqueue_scripts', 'lollms_enqueue_assets');

// --- 3. API ENDPOINTS ---
add_action('rest_api_init', function () {
    register_rest_route('lollms/v1', '/items/(?P<type>[a-zA-Z0-9-_%]+)', [
        'methods' => 'GET', 'callback' => 'lollms_api_get_items', 'permission_callback' => function() { return is_user_logged_in(); }
    ]);
    register_rest_route('lollms/v1', '/add', [
        'methods' => 'POST', 'callback' => 'lollms_api_add_item', 'permission_callback' => function() { return is_user_logged_in(); }
    ]);
    register_rest_route('lollms/v1', '/update', [
        'methods' => 'POST', 'callback' => 'lollms_api_update_fields', 'permission_callback' => function() { return is_user_logged_in(); }
    ]);
    register_rest_route('lollms/v1', '/deduct', [
        'methods' => 'POST', 'callback' => 'lollms_api_deduct_value', 'permission_callback' => function() { return is_user_logged_in(); }
    ]);
    register_rest_route('lollms/v1', '/toggle', [
        'methods' => 'POST', 'callback' => 'lollms_api_toggle_item', 'permission_callback' => function() { return is_user_logged_in(); }
    ]);
    register_rest_route('lollms/v1', '/delete', [
        'methods' => 'POST', 'callback' => 'lollms_api_delete_item', 'permission_callback' => function() { return is_user_logged_in(); }
    ]);
    register_rest_route('lollms/v1', '/scan', [
        'methods' => 'POST', 'callback' => 'lollms_api_scan_barcode', 'permission_callback' => function() { return is_user_logged_in(); }
    ]);
    register_rest_route('lollms/v1', '/suggestions', [
        'methods' => 'GET', 'callback' => 'lollms_api_get_suggestions', 'permission_callback' => function() { return is_user_logged_in(); }
    ]);
});

// --- CALLBACKS ---

function lollms_api_get_items($data) {
    global $wpdb;
    $user_id = get_current_user_id();
    $type = urldecode($data['type']);
    $order = "ORDER BY id DESC";
    
    if (in_array($type, ['coupon', 'calendar', 'qrcode', 'crypto'])) {
        $sql = "SELECT * FROM {$wpdb->prefix}lollms_items WHERE user_id = %d AND list_type = %s $order";
    } else {
        $sql = "SELECT * FROM {$wpdb->prefix}lollms_items WHERE user_id = %d AND list_type = %s AND is_checked = 0 $order";
    }
    
    return $wpdb->get_results($wpdb->prepare($sql, $user_id, $type));
}

function lollms_api_add_item($request) {
    global $wpdb;
    $user_id = get_current_user_id();
    $image_data = isset($request['image_data']) ? $request['image_data'] : null;

    $data = [
        'user_id' => $user_id,
        'item_name' => sanitize_text_field($request['name']),
        'list_type' => sanitize_text_field($request['type']),
        'due_date'  => sanitize_text_field($request['date']),     
        'end_date'  => sanitize_text_field($request['end_date']), 
        'description' => sanitize_textarea_field($request['description']),
        'location' => sanitize_text_field($request['location']),
        'attendees' => sanitize_text_field($request['attendees']),
        'quantity' => sanitize_text_field($request['qty']),
        'price' => floatval($request['price']),
        'barcode' => sanitize_text_field($request['barcode']),
        'image_data' => $image_data 
    ];

    $wpdb->insert("{$wpdb->prefix}lollms_items", $data);
    return ['status' => 'success', 'id' => $wpdb->insert_id];
}

function lollms_api_deduct_value($request) {
    global $wpdb;
    $id = intval($request['id']);
    $spent = floatval($request['spent']);
    $user_id = get_current_user_id();

    $item = $wpdb->get_row($wpdb->prepare("SELECT price FROM {$wpdb->prefix}lollms_items WHERE id = %d AND user_id = %d", $id, $user_id));
    if (!$item) return ['status' => 'error'];

    $new_val = max(0, $item->price - $spent);
    $is_empty = ($new_val <= 0.01) ? 1 : 0;

    $wpdb->update("{$wpdb->prefix}lollms_items", ['price' => $new_val, 'is_checked' => $is_empty], ['id' => $id, 'user_id' => $user_id]);
    return ['status' => 'success', 'new_value' => $new_val, 'archived' => $is_empty];
}

function lollms_api_update_fields($request) {
    global $wpdb;
    $id = intval($request['id']);
    $user_id = get_current_user_id();
    $allowed = ['price', 'quantity', 'item_name', 'barcode', 'due_date', 'end_date', 'description', 'location', 'attendees', 'image_data'];
    $updates = [];
    if ($request['field'] && isset($request['value'])) {
        $f = sanitize_text_field($request['field']);
        if (in_array($f, $allowed)) $updates[$f] = ($f === 'image_data') ? $request['value'] : sanitize_text_field($request['value']);
    }
    foreach ($allowed as $f) {
        if (isset($request[$f])) $updates[$f] = ($f === 'image_data') ? $request[$f] : sanitize_text_field($request[$f]);
    }
    if (!empty($updates)) {
        if (isset($updates['price'])) $updates['is_checked'] = (floatval($updates['price']) <= 0) ? 1 : 0;
        $wpdb->update("{$wpdb->prefix}lollms_items", $updates, ['id' => $id, 'user_id' => $user_id]);
    }
    return ['status' => 'success'];
}

function lollms_api_toggle_item($request) {
    global $wpdb;
    $wpdb->query($wpdb->prepare("UPDATE {$wpdb->prefix}lollms_items SET is_checked = 1 WHERE id = %d AND user_id = %d", intval($request['id']), get_current_user_id()));
    return ['status' => 'success'];
}

function lollms_api_delete_item($request) {
    global $wpdb;
    $wpdb->delete("{$wpdb->prefix}lollms_items", ['id' => intval($request['id']), 'user_id' => get_current_user_id()]);
    return ['status' => 'deleted'];
}

function lollms_api_scan_barcode($request) {
    global $wpdb;
    $code = sanitize_text_field($request['code']);
    $user_id = get_current_user_id();
    
    $item = $wpdb->get_row($wpdb->prepare("SELECT item_name, price, quantity FROM {$wpdb->prefix}lollms_items WHERE user_id = %d AND barcode = %s LIMIT 1", $user_id, $code));
    if ($item) return ['found' => true, 'source' => 'local', 'name' => $item->item_name, 'price' => $item->price, 'qty' => $item->quantity];

    $api_url = "https://world.openfoodfacts.org/api/v2/product/" . $code . ".json";
    $response = wp_remote_get($api_url);

    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
        $data = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($data['status']) && $data['status'] == 1) {
            return [
                'found' => true, 'source' => 'external',
                'name' => $data['product']['product_name'] ?? 'Unknown Product',
                'price' => '', 'qty' => $data['product']['quantity'] ?? ''
            ];
        }
    }
    return ['found' => false];
}

function lollms_api_get_suggestions() {
    global $wpdb;
    $user_id = get_current_user_id();
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}lollms_items WHERE user_id = %d AND is_checked = 1 ORDER BY last_bought DESC LIMIT 30", $user_id));
}

function lollms_download_shortcode() { /* ... */ }
add_shortcode('lollms_download_btn', 'lollms_download_shortcode');
function lollms_fix_news_query($query) { /* ... */ }
add_action('pre_get_posts', 'lollms_fix_news_query');
function lollms_get_first_image() { global $post; preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches); return $matches[1] ?? false; }

function lollms_custom_login_ui() {
    ?>
    <style type="text/css">
        :root { --bg-deep: #020617; --bg-panel: #0f172a; --primary: #6366f1; --accent: #d946ef; --text-main: #f8fafc; --text-dim: #94a3b8; --border: rgba(148, 163, 184, 0.15); }
        body.login { background-color: var(--bg-deep) !important; font-family: 'Inter', system-ui, sans-serif; color: var(--text-main); }
        .login h1 a { background-image: none !important; text-indent: 0 !important; font-size: 2.2rem; font-weight: 900; margin-bottom: 20px; display: block; text-align: center; color: var(--primary) !important; }
        .login form { background: var(--bg-panel) !important; border: 1px solid var(--border) !important; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.5) !important; padding: 40px !important; }
        .login label { color: var(--text-dim); text-transform: uppercase; font-size: 0.75rem; font-weight: 700; }
        .login input[type="text"], .login input[type="password"] { background: rgba(255, 255, 255, 0.05) !important; border: 1px solid var(--border) !important; color: white !important; border-radius: 8px !important; padding: 12px 15px !important; margin-top: 8px; margin-bottom: 20px; box-shadow: none !important; }
        .login input:focus { border-color: var(--primary) !important; }
        .wp-core-ui .button-primary { background: linear-gradient(135deg, var(--primary), var(--accent)) !important; border: none !important; color: white !important; padding: 12px 20px !important; font-weight: 700 !important; border-radius: 8px !important; width: 100%; margin-top: 10px; }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'lollms_custom_login_ui');
add_filter('login_headerurl', function() { return home_url(); });
add_filter('login_headertext', function() { return 'LoLLMs Nexus'; });
