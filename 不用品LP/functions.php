<?php
function ecotomi_enqueue_assets() {
    $css_file = get_template_directory() . '/assets/css/main.css';
    $js_file  = get_template_directory() . '/assets/js/main.js';
    $ver = file_exists($css_file) ? filemtime($css_file) : '1.0.0';
    $ver_js = file_exists($js_file) ? filemtime($js_file) : '1.0.0';
    wp_enqueue_style('ecotomi-main', get_template_directory_uri() . '/assets/css/main.css', array(), $ver);
    wp_enqueue_style('ecotomi-fonts', 'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700;900&display=swap', array(), null);
    wp_enqueue_script('ecotomi-main', get_template_directory_uri() . '/assets/js/main.js', array(), $ver_js, true);
}
add_action('wp_enqueue_scripts', 'ecotomi_enqueue_assets');
function ecotomi_preconnect() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action('wp_head', 'ecotomi_preconnect', 1);
function ecotomi_setup() { add_theme_support('title-tag'); }
add_action('after_setup_theme', 'ecotomi_setup');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
function ecotomi_asset($path) { return get_template_directory_uri() . '/assets/' . ltrim($path, '/'); }

// Emoji完全無効化
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
add_filter('emoji_svg_url', '__return_false');
add_filter('tiny_mce_plugins', function($plugins) {
    return is_array($plugins) ? array_diff($plugins, ['wpemoji']) : [];
});

// REST API制限
add_filter('rest_authentication_errors', function($result) {
    if (!empty($result)) return $result;
    if (!is_user_logged_in()) {
        return new WP_Error('rest_disabled', 'REST API is disabled for unauthenticated users.', ['status' => 401]);
    }
    return $result;
});

// セキュリティヘッダー
add_action('send_headers', function() {
    if (!is_admin()) {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    }
});
