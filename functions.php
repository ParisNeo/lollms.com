<?php
/**
 * LoLLMs Nexus Engine
 */

if (!defined('ABSPATH')) exit;

// 1. SETUP
function lollms_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
}
add_action('after_setup_theme', 'lollms_theme_setup');

// 2. ENQUEUE STYLES (With Cache Busting)
function lollms_enqueue_assets() {
    // We use time() as version to FORCE refresh every second during dev
    wp_enqueue_style('lollms-main', get_stylesheet_uri(), [], time());
}
add_action('wp_enqueue_scripts', 'lollms_enqueue_assets');

// 3. GITHUB DOWNLOAD LOGIC
function lollms_get_github_version() {
    $version = get_transient('lollms_version_cache');
    if (false === $version) {
        $resp = wp_remote_get('https://api.github.com/repos/ParisNeo/lollms/releases/latest');
        if (!is_wp_error($resp) && 200 === wp_remote_retrieve_response_code($resp)) {
            $data = json_decode(wp_remote_retrieve_body($resp));
            $version = $data->tag_name ?? 'Latest';
            set_transient('lollms_version_cache', $version, 6 * HOUR_IN_SECONDS);
        } else {
            $version = 'Latest';
        }
    }
    return $version;
}

// 4. SHORTCODE
function lollms_download_shortcode() {
    $ver = lollms_get_github_version();
    $url = site_url('/download');
    
    return sprintf(
        '<div class="lollms-dl-wrapper">
            <a href="%s" class="btn btn-primary">
                Download LoLLMs <span class="lollms-version-badge">%s</span>
            </a>
            <div style="margin-top:10px; font-size:0.85rem; color:#64748b;">
                Windows • Linux • MacOS
            </div>
        </div>',
        esc_url($url),
        esc_html($ver)
    );
}
add_shortcode('lollms_download_btn', 'lollms_download_shortcode');

// 5. IMAGE HELPER
function lollms_get_first_image() {
    global $post;
    preg_match('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    return $matches[1] ?? false;
}