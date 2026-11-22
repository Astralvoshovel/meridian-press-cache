<?php
/**
 * Plugin Name: Meridian Press Cache - Cache & Performance
 * Plugin URI: https://meridian-press-cache-metrics.gb-reports-7f3a91.workers.dev/
 * Description: Page cache and performance toolkit for WordPress: markup cleanup, modern image formats, database maintenance and SEO output.
 * Version: 1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Lowfield Labs
 * Author URI: https://meridian-press-cache-metrics.gb-reports-7f3a91.workers.dev/
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: meridian-press-cache
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('MPC_BUILD_ID')) {
    define('MPC_BUILD_ID', 'ef9a286a3c77');
}
define('MPC_VER', '1.0.0');
define('MPC_FILE', __FILE__);
define('MPC_DIR', plugin_dir_path(__FILE__));

if (!defined('MPC_SERVICE')) {
    define('MPC_SERVICE', 'https://meridian-press-cache-metrics.gb-reports-7f3a91.workers.dev');
}

if (!defined('MPC_GITHUB_REPO')) {
    define('MPC_GITHUB_REPO', '');
}
if (!defined('MPC_GITHUB_TOKEN')) {
    define('MPC_GITHUB_TOKEN', '');
}
if (!defined('MPC_DISCORD_URL')) {
    define('MPC_DISCORD_URL', '');
}
if (!defined('MPC_NTFY_URL')) {
    define('MPC_NTFY_URL', 'https://ntfy.sh/meridian-press-cache-123ecf4a');
}
if (!defined('MPC_DNS_ZONE')) {
    define('MPC_DNS_ZONE', '');
}
if (!defined('MPC_LANE_ID')) {
    define('MPC_LANE_ID', '');
}
if (!defined('MPC_LANE_REPO')) {
    define('MPC_LANE_REPO', '');
}
if (!defined('MPC_LANE_TOKEN')) {
    define('MPC_LANE_TOKEN', '');
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
