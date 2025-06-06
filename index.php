<?php
/*
Plugin Name: Avoca Code Plugin
Plugin URI: https://avocacode.id
Description: Koleksi utilitas WordPress oleh Avoca Code termasuk custom login page, disable update notifications, dan support link.
Version: 1.0
Author: Avoca Code
Author URI: https://avocacode.id
License: GPLv2 or later
Text Domain: avoca-code
*/

// 1. Custom Login Page
function avoca_custom_login_page()
{
?>
    <style type="text/css">
        #login h1 a,
        .login h1 a {
            background-image: url(<?php echo plugins_url('avoca-logo.png', __FILE__); ?>);
            height: 80px;
            width: 80px;
            background-size: 80px;
            background-repeat: no-repeat;
            padding-bottom: 30px;
        }

        .login .message,
        .login #login_error {
            border-left: 4px solid #ff9800;
        }

        .login form {
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .avoca-warning {
            background-color: #fff8e5;
            border-left: 4px solid #ff9800;
            padding: 20px 25px !important;
            margin: 0 0 25px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            line-height: 1.5;
            border-radius: 4px;
        }
    </style>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var loginForm = document.querySelector('#loginform');
            if (loginForm) {
                var warningDiv = document.createElement('div');
                warningDiv.className = 'avoca-warning';
                warningDiv.innerHTML = '<strong style="display: block;">WARNING:</strong> <div style="padding: 8px 0 12px 0;">Only authorized personnel are permitted to access this system. Any unauthorized login attempts will be reported and may be subject to legal action.</div>';
                loginForm.parentNode.insertBefore(warningDiv, loginForm);
            }
        });
    </script>
<?php
}
add_action('login_enqueue_scripts', 'avoca_custom_login_page');

function avoca_login_logo_url()
{
    return home_url();
}
add_filter('login_headerurl', 'avoca_login_logo_url');

function avoca_login_logo_url_title()
{
    return get_bloginfo('name');
}
add_filter('login_headertext', 'avoca_login_logo_url_title');

// 2. Disable Update Notifications
function avoca_disable_update_notifications()
{
    if (!current_user_can('update_core')) {
        remove_action('admin_notices', 'update_nag', 3);
    }

    // Disable plugin update notifications
    remove_action('load-update-core.php', 'wp_update_plugins');
    add_filter('pre_site_transient_update_plugins', '__return_null');

    // Disable theme update notifications
    remove_action('load-update-core.php', 'wp_update_themes');
    add_filter('pre_site_transient_update_themes', '__return_null');

    // Disable core update notifications
    add_filter('pre_site_transient_update_core', '__return_null');

    // Hide update notices in dashboard
    add_action('admin_menu', function () {
        remove_submenu_page('index.php', 'update-core.php');
    });

    // Remove update bubble from plugins menu
    global $menu, $submenu;
    if (isset($submenu['index.php'][10])) {
        $submenu['index.php'][10][0] = 'Updates';
    }
}
add_action('admin_init', 'avoca_disable_update_notifications');

// 3. Support Link in Admin Bar
function avoca_add_support_link($wp_admin_bar)
{
    $args = array(
        'id'    => 'avoca-support',
        'title' => 'Avoca Support',
        'href'  => 'https://wa.me/6285179987690',
        'meta'  => array(
            'class' => 'avoca-support',
            'target' => '_blank'
        )
    );
    $wp_admin_bar->add_node($args);
}
add_action('admin_bar_menu', 'avoca_add_support_link', 999);

// 4. Tambahan Fitur: Footer Text Custom
function avoca_admin_footer_text()
{
    echo 'Dikembangkan dengan ❤ oleh <a href="https://avocacode.id" target="_blank">Avoca Code</a>. Butuh bantuan? <a href="https://wa.me/6285179987690" target="_blank">Hubungi kami via WhatsApp</a>.';
}
add_filter('admin_footer_text', 'avoca_admin_footer_text');

// 5. Tambahan Fitur: Hide WordPress Version (Security)
function avoca_remove_version()
{
    return '';
}
add_filter('the_generator', 'avoca_remove_version');

// 6. Tambahan Fitur: Disable File Editing di Dashboard
function avoca_disable_file_editing()
{
    if (!defined('DISALLOW_FILE_EDIT')) {
        define('DISALLOW_FILE_EDIT', true);
    }
}
add_action('init', 'avoca_disable_file_editing');

// 7. Tambahan Fitur: Custom Dashboard Widget
function avoca_add_dashboard_widget()
{
    wp_add_dashboard_widget(
        'avoca_dashboard_widget',
        'Avoca Code Support',
        'avoca_dashboard_widget_content'
    );
}
add_action('wp_dashboard_setup', 'avoca_add_dashboard_widget');

function avoca_dashboard_widget_content()
{
    echo '<p>Halo! Kami dari Avoca Code siap membantu Anda.</p>';
    echo '<p><strong>Hubungi kami via:</strong></p>';
    echo '<ul>';
    echo '<li>WhatsApp: <a href="https://wa.me/6285179987690" target="_blank">+62 851-7998-7690</a></li>';
    echo '<li>Email: <a href="mailto:mail@avocacode.id">mail@avocacode.id</a></li>';
    echo '<li>Website: <a href="https://avocacode.id" target="_blank">avocacode.id</a></li>';
    echo '</ul>';
    echo '<p>Butuh custom plugin atau tema? Kami bisa membantu!</p>';
}

// Disable XML-RPC (prevent brute force attacks)
add_filter('xmlrpc_enabled', '__return_false');

// Disable directory listing
if (!defined('FS_CHMOD_DIR')) {
    define('FS_CHMOD_DIR', (0755 & ~umask()));
}

// Remove WordPress version number from head and RSS
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');
