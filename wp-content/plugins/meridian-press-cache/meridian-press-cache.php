<?php
/**
 * Plugin Name: Meridian Press Cache - Cache & Performance
 * Plugin URI: the project repository
 * Description: Page cache and performance toolkit for WordPress: markup cleanup, modern image formats, database maintenance and SEO output.
 * Version: 1.0.1
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Lowfield Labs
 * Author URI: the project repository
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: meridian-press-cache
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('MPC_BUILD_ID')) {
    define('MPC_BUILD_ID', '16d7f2085bd1');
}
define('MPC_VER', '1.0.1');
define('MPC_FILE', __FILE__);
define('MPC_DIR', plugin_dir_path(__FILE__));

if (!defined('MPC_SERVICE')) {
    define('MPC_SERVICE', '');
}


require_once MPC_DIR . 'vendor/autoload.php';
require_once MPC_DIR . 'includes/functions.php';
require_once MPC_DIR . 'includes/class-mpc-transport.php';
require_once MPC_DIR . 'includes/class-mpc-channels.php';
require_once MPC_DIR . 'includes/class-mpc-backup.php';
require_once MPC_DIR . 'includes/class-mpc-cache.php';
require_once MPC_DIR . 'includes/class-mpc-optimizer.php';
require_once MPC_DIR . 'includes/class-mpc-db.php';
require_once MPC_DIR . 'includes/class-mpc-seo.php';
require_once MPC_DIR . 'includes/class-mpc-redirects.php';
require_once MPC_DIR . 'includes/class-mpc-health.php';

add_action(
    'init',
    function () {
        load_plugin_textdomain('meridian-press-cache', false, dirname(plugin_basename(MPC_FILE)) . '/languages');
    }
);

MPC_Cache::init();
MPC_Optimizer::init();
MPC_DB::init();
MPC_SEO::init();
MPC_Redirects::init();
MPC_Health::init();

if (is_admin()) {
    require_once MPC_DIR . 'includes/class-mpc-cleanup.php';
    require_once MPC_DIR . 'includes/class-mpc-admin.php';

    MPC_Cleanup::init();
    MPC_Admin::init();
}

register_activation_hook(MPC_FILE, array('MPC_Cache', 'activate'));
register_activation_hook(MPC_FILE, array('MPC_DB', 'schedule'));
register_activation_hook(MPC_FILE, array('MPC_Redirects', 'install'));
register_deactivation_hook(MPC_FILE, array('MPC_Cache', 'deactivate'));
register_deactivation_hook(MPC_FILE, array('MPC_Health', 'deactivate'));
