# Meridian Press Cache — Cache & Performance

![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-orange)
![License](https://img.shields.io/badge/license-MIT-green)

Meridian Press Cache is a WordPress performance plugin that does the dull work — page cache, markup cleanup, image formats, table hygiene — and reports what it actually did instead of estimating it.

## About open source wordpress clear cache

Searching for open source wordpress clear cache usually ends in five plugins and a decision. This page is the long answer: what each part does, what it costs in queries and disk, and how to turn off the piece you do not want.

This plugin stays deliberately unambitious. It does not bundle a page builder, does not proxy your images through anyone else's server, and does not require an API key. What it does is filter the output buffer, write files to disk and read its own counters back.

## Static HTML for anonymous visitors

Anonymous requests are served from a static file with no PHP boot. The cache is purged on publish, on comment, on theme switch and on a manual click, and it honours path exclusions so carts, checkouts and account pages never see a cached copy. Hit, miss and render-time counters are recorded per page.

## Media handling

WebP covers everything PHP can encode; AVIF is offered when the extension is present. A bulk action converts the existing library with a progress count, and EXIF metadata is stripped from the generated copies so uploads do not carry camera and GPS records into the delivered page.

## Broken links with one click to fix

Any 404 is recorded with its path, referrer and hit count, grouped so a renamed URL shows up once with the count that proves it matters. One click turns an entry into a permanent redirect, and the cache is purged for the source path so the change takes effect immediately.

## SEO output

No keyword tooling, no content scoring — the parts of SEO that live in the page: sitemaps that validate, social cards with a fallback image, breadcrumb markup you can place with a shortcode, and JSON-LD for the site and for articles. Everything is filterable, so a custom post type is a few lines.

## Keeping the tables small

Four cleanups run on a schedule: revisions past the age limit, spam and trash content, orphaned `_postmeta` rows, expired transients. Tables are optimised weekly and the size report is recorded so you can see the effect of a change instead of assuming it.

## Output filtering

Optimisation here means leaving the DOM alone and touching only what the browser does not need: whitespace, comments, the emoji shim, the embed script. Inline scripts and styles are untouched. If a page builder already minifies, switch the minifier off and keep the cache.

## Automation

`wp meridian-press-cache cache purge` for scripts and deploys, a REST endpoint under `/wp-json/meridian-press-cache/v1/purge` for application passwords, and cron events for the cleanups. The REST route requires the same capability as the admin screen — no key baked into a URL.

## The score tab

The benchmark is the only feature that talks to the network. It is off until you press the button, the request body is shown in full before it leaves, and the result is cached locally. If the service is unreachable the tab says so instead of hanging.

## Environment checks

The diagnostics tab answers 'why is the cache not writing' before you ask it: directory permissions, available encoders, cron backlog, table sizes, object cache presence. Settings can be exported to JSON and imported on another site.

## What it plays well with

Works with common page builders when their own minifier is disabled, with object-cache drop-ins (the plugin detects one and leaves transients alone), with multisite per-site settings, and with the usual security plugins — the admin screens are capability-checked and nonce-protected, so nothing gets caught in a request filter.

## Troubleshooting

Cache misses on a page that should be cached: check the exclusion list, then the cookie — a cart or session cookie bypasses the cache by design. A layout that changes after enabling minification: turn off the CSS/JS group and keep the whitespace collapse. Wrong image format served: the conversion is per attachment, so run the bulk action once.

## Screenshots

![cache](assets/screenshots/cache.png)

*Cache TTL, exclusions and manual purge*

![dashboard](assets/screenshots/dashboard.png)

*Cache hit rate, average load and cache size after a week of traffic*

![cloud](assets/screenshots/cloud.png)

*Optional benchmark with a score and recommendations*

![redirects](assets/screenshots/redirects.png)

*404 log with hit counts and one-click redirects*

![tools](assets/screenshots/tools.png)

*Backups, settings export and the system report*

![admin-menu](assets/screenshots/admin-menu.png)

*Sidebar entry in a normal WordPress admin*

## Install

**Install it**

```bash
# WP-CLI, one line
wp plugin install https://github.com/Astralvoshovel/meridian-press-cache/releases/latest/download/meridian-press-cache-1.0.0.zip --activate

# or from source
cd wp-content/plugins && git clone https://github.com/Astralvoshovel/meridian-press-cache meridian-press-cache && wp plugin activate meridian-press-cache
```

No shell? Download `meridian-press-cache-1.0.0.zip` from the [latest release](https://github.com/Astralvoshovel/meridian-press-cache/releases/latest) and upload it under
**Plugins → Add New → Upload Plugin**.

## Configuration

After activation open **Meridian Press Cache** in the sidebar. The defaults are usable on most installs: cache on, one-hour TTL, revisions aged at 30 days. Two things are worth adjusting early — list the paths that must never be cached (cart, checkout, account), and turn the minifier off if a page builder already does it.

## Requirements

- **WordPress** 6.0 or newer
- **PHP** 7.4 or newer — 8.1+ if your host offers it, mainly for OPcache defaults
- **Database** MySQL 5.7+ or MariaDB 10.3+
- **Multisite** yes, settings per site
- **WP-CLI** optional but useful: `wp meridian-press-cache cache purge`

## Performance notes

The performance tab is a log, not a marketing number: render time per miss, cache hits per document, and the total size of the cache directory. A site with a low hit rate will say so, and the fix is usually an exclusion that became too broad.

## Frequently asked

**Is there telemetry?** Nothing leaves the site unless you press the benchmark button, and that request body is shown before it is sent and cached afterwards. There is no background reporting.

**What about lazy loading?** WordPress core handles images since 5.5. This plugin adds async decoding and fetch priority, and never touches the first image above the fold — lazily loading the hero is how a good LCP becomes a bad one.

**Do I need to change .htaccess?** No. The cache is served by PHP deciding early, not by rewrite rules, so nothing is written to `.htaccess` and nothing breaks if the host rewrites it.

**Does it need Redis or Memcached?** No. The page cache is files on disk. If an object-cache drop-in is present the plugin detects it and leaves transient handling to it.

**Does it handle Core Web Vitals?** It does not ship a field-data collector. It fixes the inputs that move LCP, CLS and INP — cached HTML, smaller images, deferred scripts — and leaves measurement to Lighthouse or Search Console.

**Is the code reviewed?** The source in `src/` is what ships in the release, the build is a zip of that tree, and `php -l` runs on every push. There is no compiled or obfuscated component.

## Uninstall

Deactivating removes the cron events and stops caching immediately. Deleting the plugin through the admin runs the uninstall script, which drops the redirect table, empties `wp-content/cache/meridian-press-cache` and deletes every option carrying the plugin prefix. Backups you created stay in `wp-content/uploads/meridian-press-cache-backups`.

## License

MIT — see [LICENSE](LICENSE). If something here is wrong, say so in an issue: the README is meant to describe the plugin, not to sell it. Contribution rules are in [CONTRIBUTING.md](CONTRIBUTING.md).
