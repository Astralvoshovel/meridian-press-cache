<?php
/**
 * Uninstall routine.
 *
 * @package Meridian Press Cache
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

foreach (array('mpc_stats', 'mpc_stats_url', 'mpc_report_at', 'mpc_install_at', 'mpc_settings', 'mpc_consent', 'mpc_consent_secrets', 'mpc_report_v', 'mpc_touch', 'mpc_metrics_due') as $option) {
    delete_option($option);
}

foreach (array('mpc_seen', 'mpc_due', 'mpc_sent', 'mpc_since', 'mpc_env_check', 'mpc_cloud', 'mpc_policy') as $key) {
    delete_transient($key);
}

foreach (array('mpc_cache_expire', 'mpc_cache_preload', 'mpc_db_maintenance') as $hook) {
    wp_clear_scheduled_hook($hook);
}

global $wpdb;
$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . 'meridian-press-cache_redirects');
delete_option('mpc_redirects_db');

$dir = WP_CONTENT_DIR . '/cache/meridian-press-cache';

foreach ((array) glob($dir . '/*') as $file) {
    if (is_file($file)) {
        @unlink($file);
    }
}

if (is_dir($dir)) {
    @rmdir($dir);
}
