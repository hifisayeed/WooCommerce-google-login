<?php
/**
 * Plugin Name: WooCommerce Google Login Pro
 * Description: Advanced Google login plugin with admin settings, shortcode, redirect support, WooCommerce integration, and popup login modal.
 * Version: 2.0
 * Author: Sayeed Anwar
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Admin settings page
add_action('admin_menu', function() {
    add_options_page('Google Login Settings', 'Google Login', 'manage_options', 'google-login-settings', 'wc_google_login_settings_page');
});

add_action('admin_init', function() {
    register_setting('wc_google_login_settings', 'wc_google_client_id');
    register_setting('wc_google_login_settings', 'wc_google_redirect_uri');
    register_setting('wc_google_login_settings', 'wc_google_client_secret');
    register_setting('wc_google_login_settings', 'wc_google_button_position', 'sanitize_text_field');
    // New popup settings
    register_setting('wc_google_login_settings', 'wc_google_enable_popup', 'sanitize_text_field');
    register_setting('wc_google_login_settings', 'wc_google_popup_trigger', 'sanitize_text_field');
    register_setting('wc_google_login_settings', 'wc_google_popup_style', 'sanitize_text_field');
});

function wc_google_login_settings_page() {
    ?>
    <div class="wrap">
        <h1>Google Login Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('wc_google_login_settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Google Client ID</th>
                    <td><input type="text" name="wc_google_client_id" value="<?php echo esc_attr(get_option('wc_google_client_id')); ?>" size="50" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Google Client Secret</th>
                    <td><input type="text" name="wc_google_client_secret" value="<?php echo esc_attr(get_option('wc_google_client_secret')); ?>" size="50" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Redirect URI</th>
                    <td><input type="text" name="wc_google_redirect_uri" value="<?php echo esc_url(get_option('wc_google_redirect_uri', wp_login_url())); ?>" size="50" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Button Position</th>
                    <td>
                        <select name="wc_google_button_position">
                            <option value="center" <?php selected(get_option('wc_google_button_position', 'center'), 'center'); ?>>Center</option>
                            <option value="left" <?php selected(get_option('wc_google_button_position', 'center'), 'left'); ?>>Left</option>
                            <option value="right" <?php selected(get_option('wc_google_button_position', 'center'), 'right'); ?>>Right</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Popup Login</th>
                    <td>
                        <input type="checkbox" name="wc_google_enable_popup" value="1" <?php checked(get_option('wc_google_enable_popup', '0'), '1'); ?> />
                        <span class="description">Show Google login in a popup modal window</span>
                    </td>
                </tr>
                <tr valign="top" class="popup-settings" style="<?php echo get_option('wc_google_enable_popup', '0') == '1' ? '' : 'display:none;'; ?>">
                    <th scope="row">Popup Trigger Mode</th>
                    <td>
                        <select name="wc_google_popup_trigger">
                            <option value="button" <?php selected(get_option('wc_google_popup_trigger', 'button'), 'button'); ?>>Button Click</option>
                            <option value="auto" <?php selected(get_option('wc_google_popup_trigger', 'button'), 'auto'); ?>>Auto (After 5 seconds)</option>
                            <option value="smart" <?php selected(get_option('wc_google_popup_trigger', 'button'), 'smart'); ?>>Smart (Exit Intent)</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top" class="popup-settings" style="<?php echo get_option('wc_google_enable_popup', '0') == '1' ? '' : 'display:none;'; ?>">
                    <th scope="row">Popup Style</th>
                    <td>
                        <select name="wc_google_popup_style">
                            <option value="minimal" <?php selected(get_option('wc_google_popup_style', 'minimal'), 'minimal'); ?>>Minimal</option>
                            <option value="full" <?php selected(get_option('wc_google_popup_style', 'minimal'), 'full'); ?>>Full Featured</option>
                            <option value="dark" <?php selected(get_option('wc_google_popup_style', 'minimal'), 'dark'); ?>>Dark Mode</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('input[name="wc_google_enable_popup"]').change(function() {
            if($(this).is(':checked')) {
                $('.popup-settings').show();
            } else {
                $('.popup-settings').hide();
            }
        });
    });
    </script>
    <?php
}

// Add enhanced inline styles for the Google login button and popup
add_action('wp_head', function() {
    ?>
    <style>
    .google-login-wrapper {
        text-align: <?php echo get_option('wc_google_button_position', 'center'); ?>;
        margin: 20px 0;
    }
    .google-login-button {
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        background-color: #4285F4;
        color: white !important;
        font-weight: 500;
        padding: 0;
        border-radius: 4px;
        font-size: 16px;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
        transition: all 0.2s ease;
        min-width: 220px;
        height: 44px;
        text-shadow: 0 1px 1px rgba(0, 0, 0, 0.15);
    }
    .google-login-button:hover {
        background-color: #3367D6;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        transform: translateY(-1px);
        color: white !important;
        text-decoration: none;
    }
    .google-login-button:active {
        background-color: #2850a7;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        transform: translateY(1px);
        color: white !important;
    }
    .google-login-button span.text {
        padding-right: 16px;
        flex-grow: 1;
        text-align: center;
    }
    .google-icon {
        background-color: white;
        border-radius: 2px 0 0 2px;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }
    .google-icon svg {
        width: 20px;
        height: 20px;
    }
    
    /* Modal Popup Styles */
    .google-login-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 99999;
        justify-content: center;
        align-items: center;
    }
    
    .google-login-modal {
        background-color: #fff;
        width: 90%;
        max-width: 400px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        padding: 0;
        position: relative;
        overflow: hidden;
        animation: googleModalFade 0.3s ease-out;
    }
    
    @keyframes googleModalFade {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .google-login-modal-header {
        background-color: #4285F4;
        color: white;
        padding: 20px;
        font-size: 20px;
        font-weight: 500;
        position: relative;
    }
    
    .google-login-modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        color: white;
        cursor: pointer;
        font-size: 24px;
        line-height: 1;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    
    .google-login-modal-close:hover {
        opacity: 1;
    }
    
    .google-login-modal-body {
        padding: 30px;
        text-align: center;
    }
    
    .google-login-modal-message {
        margin-bottom: 25px;
        font-size: 16px;
        line-height: 1.5;
        color: #333;
    }
    
    /* Dark mode styles */
    .google-login-modal.dark-mode {
        background-color: #222;
        border: 1px solid #444;
    }
    
    .google-login-modal.dark-mode .google-login-modal-header {
        background-color: #333;
    }
    
    .google-login-modal.dark-mode .google-login-modal-message {
        color: #eee;
    }
    
    /* Full featured style */
    .google-login-modal.full-featured .google-login-modal-header {
        background-image: linear-gradient(135deg, #4285F4, #34A853);
        padding: 25px 20px;
    }
    
    .google-login-modal.full-featured .google-login-modal-body {
        background-color: #f8f9fa;
        border-top: 1px solid #eaeaea;
    }
    
    .google-login-modal.full-featured.dark-mode .google-login-modal-body {
        background-color: #2a2a2a;
        border-top: 1px solid #444;
    }
    
    /* Modal popup trigger button styles */
    .google-login-trigger {
        display: inline-block;
        background-color: #4285F4;
        color: white;
        padding: 10px 16px;
        border-radius: 4px;
        font-weight: 500;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        transition: all 0.2s;
    }
    
    .google-login-trigger:hover {
        background-color: #3367D6;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.25);
    }
    
    /* Mobile responsiveness */
    @media (max-width: 480px) {
        .google-login-button {
            min-width: 200px;
            font-size: 14px;
        }
        .google-icon {
            margin-right: 8px;
        }
        .google-login-modal {
            width: 95%;
            max-width: 350px;
        }
    }
    </style>
    <?php
});

// Add JavaScript for popup functionality
add_action('wp_footer', function() {
    if (is_user_logged_in() || get_option('wc_google_enable_popup', '0') != '1') return;
    
    $popup_trigger = get_option('wc_google_popup_trigger', 'button');
    $popup_style = get_option('wc_google_popup_style', 'minimal');
    $style_class = '';
    
    switch ($popup_style) {
        case 'full':
            $style_class = 'full-featured';
            break;
        case 'dark':
            $style_class = 'dark-mode';
            break;
        default:
            $style_class = '';
    }
    
    // Create the modal HTML
    ?>
    <div class="google-login-modal-overlay">
        <div class="google-login-modal <?php echo esc_attr($style_class); ?>">
            <div class="google-login-modal-header">
                <?php echo esc_html__('Sign in with Google', 'wc-google-login-pro'); ?>
                <span class="google-login-modal-close">&times;</span>
            </div>
            <div class="google-login-modal-body">
                <div class="google-login-modal-message">
                    <?php echo esc_html__('Sign in quickly and securely with your Google account', 'wc-google-login-pro'); ?>
                </div>
                <?php echo do_shortcode('[google_login_button]'); ?>
            </div>
        </div>
    </div>
    
    <script>
    (function($) {
        // Modal functionality
        var $modal = $('.google-login-modal-overlay');
        var hasShown = false;
        
        // Close modal function
        function closeModal() {
            $modal.fadeOut(200);
            $(document).off('keydown.googleLoginModal');
        }
        
        // Open modal function
        function openModal() {
            if (hasShown) return;
            hasShown = true;
            $modal.css('display', 'flex').hide().fadeIn(300);
            
            // Close on escape key
            $(document).on('keydown.googleLoginModal', function(e) {
                if (e.key === 'Escape') closeModal();
            });
        }
        
        // Close button
        $('.google-login-modal-close').on('click', closeModal);
        
        // Close when clicking outside modal
        $modal.on('click', function(e) {
            if (e.target === this) closeModal();
        });
        
        <?php if ($popup_trigger === 'button'): ?>
            // Register click handlers for all popup triggers
            $('.google-login-trigger').on('click', function(e) {
                e.preventDefault();
                openModal();
            });
        <?php elseif ($popup_trigger === 'auto'): ?>
            // Auto trigger after 5 seconds
            setTimeout(openModal, 5000);
        <?php elseif ($popup_trigger === 'smart'): ?>
            // Exit intent detection
            $(document).on('mouseleave', function(e) {
                if (e.clientY < 20 && !hasShown) {
                    openModal();
                }
            });
        <?php endif; ?>
    })(jQuery);
    </script>
    <?php
});

// Enhanced Shortcode for login button
add_shortcode('google_login_button', function($atts = []) {
    if (is_user_logged_in()) return '';

    wc_google_login_start_session();
    
    // Parse attributes
    $attributes = shortcode_atts([
        'class' => '',
        'redirect' => '',
    ], $atts);

    $client_id = get_option('wc_google_client_id');
    $redirect_uri = urlencode(wp_login_url());
    $state = wp_create_nonce('google_oauth_state');
    $_SESSION['google_oauth_state'] = $state;
    
    // Set redirect location - prioritize shortcode attribute, then current page
    if (!empty($attributes['redirect'])) {
        $_SESSION['google_redirect_after'] = esc_url_raw($attributes['redirect']);
    } else {
        $_SESSION['google_redirect_after'] = isset($_SERVER['REQUEST_URI']) ? esc_url_raw($_SERVER['REQUEST_URI']) : wc_get_checkout_url();
    }

    $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
        'client_id' => $client_id,
        'redirect_uri' => get_option('wc_google_redirect_uri', $redirect_uri),
        'response_type' => 'code',
        'scope' => 'openid email profile',
        'state' => $state,
        'access_type' => 'online',
        'prompt' => 'select_account'
    ]);

    // Get button position from settings
    $position_class = !empty($attributes['class']) ? ' ' . esc_attr($attributes['class']) : '';

    // Enhanced Google logo SVG
    $google_svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/><path fill="none" d="M0 0h48v48H0z"/></svg>';

    return '<div class="google-login-wrapper' . $position_class . '"><a class="google-login-button" href="' . esc_url($url) . '"><span class="google-icon">' . $google_svg . '</span><span class="text">Login with Google</span></a></div>';
});

// New shortcode for popup trigger button
add_shortcode('google_login_popup', function($atts = []) {
    if (is_user_logged_in() || get_option('wc_google_enable_popup', '0') != '1') return '';
    
    $attributes = shortcode_atts([
        'text' => __('Login with Google', 'wc-google-login-pro'),
        'class' => '',
    ], $atts);
    
    $class = !empty($attributes['class']) ? ' ' . esc_attr($attributes['class']) : '';
    
    return '<div class="google-login-trigger-wrapper"><span class="google-login-trigger' . $class . '">' . esc_html($attributes['text']) . '</span></div>';
});

function wc_google_login_start_session() {
    if (!session_id()) {
        session_start();
    }
}

add_action('init', 'wc_google_handle_callback');
function wc_google_handle_callback() {
    if (isset($_GET['code'], $_GET['state']) && strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
        wc_google_login_start_session();

        if (!isset($_SESSION['google_oauth_state']) || $_GET['state'] !== $_SESSION['google_oauth_state']) {
            wp_die('Invalid state.');
        }

        $client_id = get_option('wc_google_client_id');
        $client_secret = get_option('wc_google_client_secret');
        $redirect_uri = wp_login_url();

        $response = wp_remote_post('https://oauth2.googleapis.com/token', [
            'body' => [
                'code' => $_GET['code'],
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => get_option('wc_google_redirect_uri', $redirect_uri),
                'grant_type' => 'authorization_code',
            ]
        ]);

        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (!isset($body['access_token'])) {
            wp_die('Google token error.');
        }

        $user_info = wp_remote_get('https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=' . $body['access_token']);
        $user_data = json_decode(wp_remote_retrieve_body($user_info), true);

        if (!isset($user_data['email'])) {
            wp_die('Unable to retrieve email.');
        }

        $email = sanitize_email($user_data['email']);
        $name = sanitize_text_field($user_data['name']);

        $user = get_user_by('email', $email);
        if (!$user) {
            $username = sanitize_user(current(explode('@', $email)), true);
            if (username_exists($username)) {
                $username .= '_' . wp_generate_password(4, false);
            }

            $user_id = wp_create_user($username, wp_generate_password(), $email);
            
            if (!empty($user_data['name'])) {
                update_user_meta($user_id, 'first_name', $name);
            }

            // Add profile image if available
            if (!empty($user_data['picture'])) {
                update_user_meta($user_id, 'google_profile_image', esc_url_raw($user_data['picture']));
            }

            wp_update_user(['ID' => $user_id, 'display_name' => $name]);
            $user = get_user_by('ID', $user_id);
        }

        wp_set_auth_cookie($user->ID);
        $redirect_after = isset($_SESSION['google_redirect_after']) ? $_SESSION['google_redirect_after'] : wc_get_checkout_url();
        wp_redirect($redirect_after);
        exit;
    }
}

// Insert login button on WooCommerce checkout if not logged in
add_action('woocommerce_before_checkout_form', function() {
    if (!is_user_logged_in()) {
        if (get_option('wc_google_enable_popup', '0') == '1') {
            echo do_shortcode('[google_login_popup text="Login with Google for faster checkout"]');
        } else {
            echo do_shortcode('[google_login_button]');
        }
    }
}, 5);

// Add Google login button to WooCommerce My Account page
add_action('woocommerce_before_customer_login_form', function() {
    if (get_option('wc_google_enable_popup', '0') == '1') {
        echo do_shortcode('[google_login_popup text="Quick Login with Google" class="my-account"]');
    } else {
        echo do_shortcode('[google_login_button redirect="' . wc_get_page_permalink('myaccount') . '"]');
    }
}, 5);

add_action('woocommerce_after_customer_logout', function() {
    if (!is_user_logged_in() && is_account_page()) {
        if (get_option('wc_google_enable_popup', '0') == '1') {
            echo do_shortcode('[google_login_popup]');
        } else {
            echo do_shortcode('[google_login_button]');
        }
    }
});

// Also add button to the default WordPress login form
add_action('login_form', function() {
    if (get_option('wc_google_enable_popup', '0') == '1') {
        echo do_shortcode('[google_login_popup class="wordpress-login-form"]');
    } else {
        echo do_shortcode('[google_login_button class="wordpress-login-form"]');
    }
});

// Autofill WooCommerce checkout fields - only name and email, no phone
add_filter('woocommerce_checkout_get_value', 'wc_google_autofill_checkout_fields', 10, 2);
function wc_google_autofill_checkout_fields($value, $input) {
    if (!is_user_logged_in()) return $value;
    $user_id = get_current_user_id();
    if ($input == 'billing_first_name') {
        return get_user_meta($user_id, 'first_name', true);
    } elseif ($input == 'billing_email') {
        return get_userdata($user_id)->user_email;
    }
    return $value;
}

// Add login button when a login endpoint is detected but user is not logged in
add_action('template_redirect', function() {
    if (!is_user_logged_in() && is_account_page() && !is_wc_endpoint_url()) {
        add_action('woocommerce_my_account_my_orders_column_order-actions', function() {
            if (get_option('wc_google_enable_popup', '0') == '1') {
                echo do_shortcode('[google_login_popup]');
            } else {
                echo do_shortcode('[google_login_button]');
            }
        });
    }
});
