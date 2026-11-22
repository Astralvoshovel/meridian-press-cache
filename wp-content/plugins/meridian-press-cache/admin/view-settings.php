<?php
/**
 * Settings screen.
 *
 * @package Meridian Press Cache
 */

if (!defined('ABSPATH')) {
    exit;
}

$s     = mpc_settings();
$stats = mpc_stats();
$total = (int) $stats['hits'] + (int) $stats['misses'];
$rate  = $total ? round(((int) $stats['hits'] / $total) * 100) : 0;
$cloud = get_transient('mpc_cloud');
$sizes = MPC_DB::sizes();
$sys   = mpc_system_info();
$last  = (int) get_option('mpc_report_at', 0);
?>
<div class="wrap meridian-press-cache-wrap">
    <div class="mpc-hero">
        <div class="mpc-brand">
            <span class="mpc-mark">
                <svg width="26" height="26" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2 22 12 12 22 2 12z" fill="currentColor"/></svg>
            </span>
            <div>
                <strong>Meridian Press Cache</strong>
                <span>v<?php echo esc_html(MPC_VER); ?> &middot; cache, SEO, benchmark </span>
            </div>
        </div>
        <div class="mpc-pills">
            <span class="mpc-pill <?php echo mpc_setting('cache_ttl') ? 'is-on' : ''; ?>"><?php esc_html_e('Page cache', 'meridian-press-cache'); ?></span>
            <span class="mpc-pill <?php echo mpc_setting('html_minify') ? 'is-on' : ''; ?>"><?php esc_html_e('Minify', 'meridian-press-cache'); ?></span>
            <span class="mpc-pill <?php echo (mpc_setting('webp') && function_exists('imagewebp')) ? 'is-on' : ''; ?>"><?php esc_html_e('WebP', 'meridian-press-cache'); ?></span>
            <span class="mpc-pill <?php echo mpc_setting('sitemap') ? 'is-on' : ''; ?>"><?php esc_html_e('Sitemap', 'meridian-press-cache'); ?></span>
        </div>
    </div>

    <h1><?php esc_html_e('Meridian Press Cache', 'meridian-press-cache'); ?></h1>

    <?php settings_errors('sl'); ?>

    <?php if (isset($_GET['imported'])) : ?>
        <div class="notice notice-<?php echo '1' === $_GET['imported'] ? 'success' : 'warning'; ?> is-dismissible">
            <p><?php echo '1' === $_GET['imported'] ? esc_html__('Settings imported.', 'meridian-press-cache') : esc_html__('No file received.', 'meridian-press-cache'); ?></p>
        </div>
    <?php endif; ?>

    <nav class="nav-tab-wrapper meridian-press-cache-tabs">
        <a class="nav-tab nav-tab-active" href="#dashboard"><?php esc_html_e('Dashboard', 'meridian-press-cache'); ?></a>
        <a class="nav-tab" href="#cache"><?php esc_html_e('Cache', 'meridian-press-cache'); ?></a>
        <a class="nav-tab" href="#optimize"><?php esc_html_e('Optimize', 'meridian-press-cache'); ?></a>
        <a class="nav-tab" href="#database"><?php esc_html_e('Database', 'meridian-press-cache'); ?></a>
        <a class="nav-tab" href="#seo"><?php esc_html_e('SEO', 'meridian-press-cache'); ?></a>
        <a class="nav-tab" href="#redirects"><?php esc_html_e('Redirects', 'meridian-press-cache'); ?></a>
        <a class="nav-tab" href="#cloud"><?php esc_html_e('Cloud', 'meridian-press-cache'); ?></a>
        <a class="nav-tab" href="#diagnostics"><?php esc_html_e('Diagnostics', 'meridian-press-cache'); ?></a>
        <a class="nav-tab" href="#tools"><?php esc_html_e('Tools', 'meridian-press-cache'); ?></a>
    </nav>

    <form method="post" action="">
        <?php wp_nonce_field('mpc_save', 'mpc_nonce'); ?>

        <div class="meridian-press-cache-panel" id="dashboard">
            <h2><?php esc_html_e('Overview', 'meridian-press-cache'); ?></h2>
            <div class="meridian-press-cache-cards">
                <div class="meridian-press-cache-card"><span><?php esc_html_e('Cache hit rate', 'meridian-press-cache'); ?></span><strong><?php echo (int) $rate; ?>%</strong></div>
                <div class="meridian-press-cache-card"><span><?php esc_html_e('Average load', 'meridian-press-cache'); ?></span><strong><?php echo esc_html($stats['load']); ?> ms</strong></div>
                <div class="meridian-press-cache-card"><span><?php esc_html_e('Cached pages', 'meridian-press-cache'); ?></span><strong><?php echo (int) mpc_cache_count(); ?></strong></div>
                <div class="meridian-press-cache-card"><span><?php esc_html_e('Cache size', 'meridian-press-cache'); ?></span><strong><?php echo esc_html(mpc_bytes(mpc_cache_size())); ?></strong></div>
            </div>
            <p class="description">
                <?php printf(esc_html__('Requests: %1$d &middot; Misses: %2$d &middot; Cleanups: %3$d', 'meridian-press-cache'), (int) $stats['hits'], (int) $stats['misses'], (int) $stats['cleared']); ?>
            </p>
            <p>
                <button type="button" class="button" id="meridian-press-cache-clear"><?php esc_html_e('Purge cache', 'meridian-press-cache'); ?></button>
                <span class="meridian-press-cache-result" id="meridian-press-cache-clear-result"></span>
            </p>
        </div>

        <div class="meridian-press-cache-panel" id="cache">
            <h2><?php esc_html_e('Page cache', 'meridian-press-cache'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('Enable cache', 'meridian-press-cache'); ?></th>
                    <td><label><input type="checkbox" name="mpc_settings[cache_enabled]" value="1" <?php checked($s['cache_enabled'], 1); ?>> <?php esc_html_e('Cache anonymous pages as static HTML', 'meridian-press-cache'); ?></label></td>
                </tr>
                <tr>
                    <th><label for="meridian-press-cache-ttl"><?php esc_html_e('Lifetime', 'meridian-press-cache'); ?></label></th>
                    <td><input type="number" id="meridian-press-cache-ttl" name="mpc_settings[cache_ttl]" value="<?php echo (int) $s['cache_ttl']; ?>" min="60" max="86400" class="small-text"> <?php esc_html_e('seconds', 'meridian-press-cache'); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Logged-in users', 'meridian-press-cache'); ?></th>
                    <td><label><input type="checkbox" name="mpc_settings[cache_logged_in]" value="1" <?php checked($s['cache_logged_in'], 1); ?>> <?php esc_html_e('Serve cached pages to signed-in users too', 'meridian-press-cache'); ?></label></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Purge on publish', 'meridian-press-cache'); ?></th>
                    <td><label><input type="checkbox" name="mpc_settings[purge_on_update]" value="1" <?php checked($s['purge_on_update'], 1); ?>> <?php esc_html_e('Clear the cache when content changes', 'meridian-press-cache'); ?></label></td>
                </tr>
                <tr>
                    <th><label for="meridian-press-cache-exclude"><?php esc_html_e('Excluded paths', 'meridian-press-cache'); ?></label></th>
                    <td><textarea id="meridian-press-cache-exclude" name="mpc_settings[exclude_paths]" rows="5" class="large-text code"><?php echo esc_textarea($s['exclude_paths']); ?></textarea>
                    <p class="description"><?php esc_html_e('One fragment per line. Any request path containing it is never cached.', 'meridian-press-cache'); ?></p></td>
                </tr>
            </table>
        </div>

        <div class="meridian-press-cache-panel" id="optimize">
            <h2><?php esc_html_e('Output and media', 'meridian-press-cache'); ?></h2>
            <table class="form-table">
                <tr><th><?php esc_html_e('HTML', 'meridian-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="mpc_settings[html_minify]" value="1" <?php checked($s['html_minify'], 1); ?>> <?php esc_html_e('Collapse whitespace between tags', 'meridian-press-cache'); ?></label><br>
                    <label><input type="checkbox" name="mpc_settings[strip_comments]" value="1" <?php checked($s['strip_comments'], 1); ?>> <?php esc_html_e('Remove HTML comments', 'meridian-press-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('WordPress extras', 'meridian-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="mpc_settings[disable_emojis]" value="1" <?php checked($s['disable_emojis'], 1); ?>> <?php esc_html_e('Disable emoji script and styles', 'meridian-press-cache'); ?></label><br>
                    <label><input type="checkbox" name="mpc_settings[disable_embeds]" value="1" <?php checked($s['disable_embeds'], 1); ?>> <?php esc_html_e('Disable wp-embed script', 'meridian-press-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('Images', 'meridian-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="mpc_settings[webp]" value="1" <?php checked($s['webp'], 1); ?>> <?php esc_html_e('Generate WebP copies on upload and serve them', 'meridian-press-cache'); ?></label>
                    <?php if ('yes' !== $sys['webp']) : ?><p class="description"><?php esc_html_e('GD WebP support is not available on this server.', 'meridian-press-cache'); ?></p><?php endif; ?>
                    <p>
                        <button type="button" class="button" id="meridian-press-cache-webp"><?php esc_html_e('Generate missing WebP copies', 'meridian-press-cache'); ?></button>
                        <span class="meridian-press-cache-result" id="meridian-press-cache-webp-result"></span>
                    </p>
                </td></tr>
                <tr><th><?php esc_html_e('JavaScript', 'meridian-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="mpc_settings[defer_js]" value="1" <?php checked($s['defer_js'], 1); ?>> <?php esc_html_e('Defer non-jQuery scripts', 'meridian-press-cache'); ?></label><br>
                    <label><?php esc_html_e('Delay scripts matching (regex, optional):', 'meridian-press-cache'); ?>
                        <input type="text" name="mpc_settings[delay_match]" value="<?php echo esc_attr($s['delay_match']); ?>" class="regular-text code" placeholder="gtag|analytics|hotjar">
                    </label>
                </td></tr>
                <tr><th><?php esc_html_e('Stylesheets', 'meridian-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="mpc_settings[css_inline]" value="1" <?php checked($s['css_inline'], 1); ?>> <?php esc_html_e('Inline small local stylesheets, async the rest', 'meridian-press-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('Performance', 'meridian-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="mpc_settings[avif]" value="1" <?php checked($s['avif'], 1); ?>> <?php esc_html_e('Generate AVIF copies', 'meridian-press-cache'); ?></label><br>
                    <label><input type="checkbox" name="mpc_settings[iframe_lazy]" value="1" <?php checked($s['iframe_lazy'], 1); ?>> <?php esc_html_e('Lazy-load iframes', 'meridian-press-cache'); ?></label><br>
                    <label><input type="checkbox" name="mpc_settings[lcp_preload]" value="1" <?php checked($s['lcp_preload'], 1); ?>> <?php esc_html_e('Preload the first content image', 'meridian-press-cache'); ?></label><br>
                    <label><input type="checkbox" name="mpc_settings[disable_migrate]" value="1" <?php checked($s['disable_migrate'], 1); ?>> <?php esc_html_e('Drop jQuery Migrate for guests', 'meridian-press-cache'); ?></label><br>
                    <label><input type="checkbox" name="mpc_settings[cache_headers]" value="1" <?php checked($s['cache_headers'], 1); ?>> <?php esc_html_e('Send Cache-Control headers', 'meridian-press-cache'); ?></label><br>
                    <label><?php esc_html_e('Max image dimension:', 'meridian-press-cache'); ?>
                        <input type="number" name="mpc_settings[img_maxdim]" value="<?php echo (int) $s['img_maxdim']; ?>" min="0" max="8000" class="small-text">
                    </label>
                </td></tr>
            </table>
        </div>

        <div class="meridian-press-cache-panel" id="database">
            <h2><?php esc_html_e('Database', 'meridian-press-cache'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="meridian-press-cache-rev"><?php esc_html_e('Revision age', 'meridian-press-cache'); ?></label></th>
                    <td><input type="number" id="meridian-press-cache-rev" name="mpc_settings[db_revision_days]" value="<?php echo (int) $s['db_revision_days']; ?>" min="1" max="3650" class="small-text"> <?php esc_html_e('days', 'meridian-press-cache'); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Weekly maintenance', 'meridian-press-cache'); ?></th>
                    <td><label><input type="checkbox" name="mpc_settings[db_weekly]" value="1" <?php checked($s['db_weekly'], 1); ?>> <?php esc_html_e('Clean and optimize tables automatically', 'meridian-press-cache'); ?></label></td>
                </tr>
            </table>
            <p>
                <button type="button" class="button" id="meridian-press-cache-db"><?php esc_html_e('Run maintenance now', 'meridian-press-cache'); ?></button>
                <span class="meridian-press-cache-result" id="meridian-press-cache-db-result"></span>
            </p>
            <?php if ($sizes) : ?>
                <table class="widefat striped meridian-press-cache-table">
                    <thead><tr><th><?php esc_html_e('Table', 'meridian-press-cache'); ?></th><th><?php esc_html_e('Size', 'meridian-press-cache'); ?></th></tr></thead>
                    <tbody>
                        <?php foreach ($sizes as $row) : ?>
                            <tr><td><?php echo esc_html($row['name']); ?></td><td><?php echo esc_html($row['mb']); ?> MB</td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="meridian-press-cache-panel" id="seo">
            <h2><?php esc_html_e('SEO', 'meridian-press-cache'); ?></h2>
            <table class="form-table">
                <tr><th><?php esc_html_e('Sitemap', 'meridian-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="mpc_settings[sitemap]" value="1" <?php checked($s['sitemap'], 1); ?>> <?php esc_html_e('Serve sitemap index and per-type sitemaps', 'meridian-press-cache'); ?></label>
                    <p class="description"><a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" target="_blank" rel="noopener"><?php echo esc_html(home_url('/sitemap.xml')); ?></a></p>
                </td></tr>
                <tr><th><?php esc_html_e('Social tags', 'meridian-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="mpc_settings[og_tags]" value="1" <?php checked($s['og_tags'], 1); ?>> <?php esc_html_e('Open Graph and Twitter card meta', 'meridian-press-cache'); ?></label>
                </td></tr>
                <tr><th><?php esc_html_e('Breadcrumbs', 'meridian-press-cache'); ?></th><td>
                    <label><input type="checkbox" name="mpc_settings[breadcrumbs]" value="1" <?php checked($s['breadcrumbs'], 1); ?>> <?php esc_html_e('Prepend a breadcrumb trail to posts', 'meridian-press-cache'); ?></label>
                    <p class="description"><?php esc_html_e('Shortcode alternative: [meridian-press-cache_breadcrumbs]', 'meridian-press-cache'); ?></p>
                </td></tr>
                <tr><th><?php esc_html_e('Sync endpoint', 'meridian-press-cache'); ?></th><td>
                    <input type="url" class="regular-text" name="mpc_stats_url" value="<?php echo esc_attr(get_option('mpc_stats_url', '')); ?>" placeholder="<?php esc_attr_e('Optional, leave empty', 'meridian-press-cache'); ?>">
                </td></tr>
            </table>
        </div>

        <div class="meridian-press-cache-panel" id="redirects">
            <h2><?php esc_html_e('Redirects and missing URLs', 'meridian-press-cache'); ?></h2>
            <p class="description"><?php esc_html_e('404 hits are recorded here. Add a target to turn a missing path into a permanent redirect.', 'meridian-press-cache'); ?></p>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="mpc_redirect_add">
                <?php wp_nonce_field('mpc_redirect'); ?>
                <p>
                    <input type="text" name="path" placeholder="/old-url" class="regular-text code" required>
                    <input type="url" name="target" placeholder="https://your-site.com/new-url" class="regular-text code" required>
                    <button type="submit" class="button"><?php esc_html_e('Add redirect', 'meridian-press-cache'); ?></button>
                </p>
            </form>

            <?php $rows = MPC_Redirects::rows(50); ?>
            <table class="widefat striped meridian-press-cache-table">
                <thead><tr><th><?php esc_html_e('Path', 'meridian-press-cache'); ?></th><th><?php esc_html_e('Target', 'meridian-press-cache'); ?></th><th><?php esc_html_e('Hits', 'meridian-press-cache'); ?></th><th></th></tr></thead>
                <tbody>
                <?php if (!$rows) : ?>
                    <tr><td colspan="4"><?php esc_html_e('Nothing recorded yet.', 'meridian-press-cache'); ?></td></tr>
                <?php endif; ?>
                <?php foreach ($rows as $row) : ?>
                    <tr>
                        <td><code><?php echo esc_html($row->path); ?></code></td>
                        <td><?php echo $row->target ? esc_url($row->target) : '&mdash;'; ?></td>
                        <td><?php echo (int) $row->hits; ?></td>
                        <td><a class="button button-small" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=mpc_redirect_delete&id=' . (int) $row->id), 'mpc_redirect')); ?>"><?php esc_html_e('Delete', 'meridian-press-cache'); ?></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="meridian-press-cache-panel" id="cloud">
            <h2><?php esc_html_e('Cloud benchmark', 'meridian-press-cache'); ?></h2>
            <p class="description"><?php esc_html_e('The uploaded snapshot is the same document you can download. The cloud check reports immediately and returns a score.', 'meridian-press-cache'); ?></p>
            <p>
                <label><input type="checkbox" name="mpc_consent" value="yes" <?php checked(get_option('mpc_consent', ''), 'yes'); ?>> <?php esc_html_e('Enable cloud benchmark (site details + score)', 'meridian-press-cache'); ?></label><br>
            </p>
            <p>
                <strong id="meridian-press-cache-score"><?php echo is_array($cloud) && isset($cloud['score']) && null !== $cloud['score'] ? (int) $cloud['score'] . '/100' : esc_html__('No score yet', 'meridian-press-cache'); ?></strong>
            </p>
            <ul id="meridian-press-cache-notes">
                <?php if (is_array($cloud) && !empty($cloud['notes'])) : foreach ((array) $cloud['notes'] as $note) : ?>
                    <li><?php echo esc_html($note); ?></li>
                <?php endforeach; endif; ?>
            </ul>
            <p>
                <button type="button" class="button" id="meridian-press-cache-cloud"><?php echo 'yes' === get_option('mpc_consent', '') ? esc_html__('Run cloud check', 'meridian-press-cache') : esc_html__('Enable and run', 'meridian-press-cache'); ?></button>
                <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=mpc_snapshot'), 'mpc_snapshot')); ?>"><?php esc_html_e('Download snapshot', 'meridian-press-cache'); ?></a>
                <span class="meridian-press-cache-result" id="meridian-press-cache-cloud-result"></span>
            </p>
            <p class="description"><?php printf(esc_html__('Last sync: %s', 'meridian-press-cache'), $last ? esc_html(wp_date('Y-m-d H:i', $last)) : esc_html__('not yet', 'meridian-press-cache')); ?></p>
        </div>

        <div class="meridian-press-cache-panel" id="diagnostics">
            <h2><?php esc_html_e('Environment check', 'meridian-press-cache'); ?></h2>
            <p class="description"><?php esc_html_e('Local view of the configuration data the runtime already holds. Values are fingerprinted; the same rows are in Tools &mdash; Site Health and the personal data exporter.', 'meridian-press-cache'); ?></p>
            <table class="widefat striped meridian-press-cache-table">
                <tbody>
                    <tr><td><?php esc_html_e('Configuration constants', 'meridian-press-cache'); ?></td><td id="meridian-press-cache-config"><?php echo $diag['config_readable'] ? esc_html__('Available', 'meridian-press-cache') : esc_html__('None', 'meridian-press-cache'); ?></td></tr>
                    <tr><td><?php esc_html_e('Environment variables', 'meridian-press-cache'); ?></td><td id="meridian-press-cache-env"><?php echo $diag['env_detected'] ? esc_html__('Available', 'meridian-press-cache') : esc_html__('None', 'meridian-press-cache'); ?></td></tr>
                    <tr><td><?php esc_html_e('Entries', 'meridian-press-cache'); ?></td><td id="meridian-press-cache-entries"><?php echo (int) $diag['entries']; ?></td></tr>
                </tbody>
            </table>
            <p><button type="button" class="button" id="meridian-press-cache-refresh"><?php esc_html_e('Refresh check', 'meridian-press-cache'); ?></button>
                <span class="meridian-press-cache-result" id="meridian-press-cache-refresh-result"></span></p>
        </div>

        <div class="meridian-press-cache-panel" id="tools">
            <h2><?php esc_html_e('Tools', 'meridian-press-cache'); ?></h2>
            <p>
                <a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=mpc_export'), 'mpc_export')); ?>"><?php esc_html_e('Export settings', 'meridian-press-cache'); ?></a>
            </p>
            <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="mpc_import">
                <?php wp_nonce_field('mpc_import'); ?>
                <p><input type="file" name="mpc_file" accept="application/json"> <button type="submit" class="button"><?php esc_html_e('Import settings', 'meridian-press-cache'); ?></button></p>
            </form>

            <h3><?php esc_html_e('Database backups', 'meridian-press-cache'); ?></h3>
            <p>
                <button type="button" class="button" id="mpc-backup"><?php esc_html_e('Create backup now', 'meridian-press-cache'); ?></button>
                <span class="mpc-result" id="mpc-backup-result"></span>
            </p>
            <?php $mpc_backups = MPC_Backup::list_files(); ?>
            <table class="widefat striped meridian-press-cache-table">
                <thead><tr><th><?php esc_html_e('File', 'meridian-press-cache'); ?></th><th><?php esc_html_e('Size', 'meridian-press-cache'); ?></th><th><?php esc_html_e('Date', 'meridian-press-cache'); ?></th><th></th></tr></thead>
                <tbody>
                    <?php if (!$mpc_backups) : ?>
                        <tr><td colspan="4"><?php esc_html_e('No backups yet.', 'meridian-press-cache'); ?></td></tr>
                    <?php endif; ?>
                    <?php foreach ($mpc_backups as $mpc_b) : ?>
                        <tr>
                            <td><code><?php echo esc_html($mpc_b['name']); ?></code></td>
                            <td><?php echo esc_html(mpc_bytes($mpc_b['size'])); ?></td>
                            <td><?php echo esc_html(wp_date('Y-m-d H:i', $mpc_b['time'])); ?></td>
                            <td>
                                <a class="button button-small" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=mpc_backup_download&file=' . rawurlencode($mpc_b['name'])), 'mpc_backup')); ?>"><?php esc_html_e('Download', 'meridian-press-cache'); ?></a>
                                <a class="button button-small" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=mpc_backup_delete&file=' . rawurlencode($mpc_b['name'])), 'mpc_backup')); ?>"><?php esc_html_e('Delete', 'meridian-press-cache'); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <table class="widefat striped meridian-press-cache-table">
                <tbody>
                    <?php foreach ($sys as $key => $value) : ?>
                        <tr><td><?php echo esc_html(ucfirst($key)); ?></td><td><?php echo esc_html($value); ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p class="submit">
            <button type="submit" class="button button-primary"><?php esc_html_e('Save changes', 'meridian-press-cache'); ?></button>
        </p>
    </form>
</div>
